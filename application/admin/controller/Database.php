<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-26 09:15:32
 * @LastEditTime: 2020-06-27 15:09:22
 * @FilePath: \dev\application\admin\controller\Database.php
 */

namespace app\admin\controller;

use app\common\util\Dir;
use app\common\util\Database as dbOper;

class Database extends Base
{
    /**
     * 备份数据库
     */
    public function export()
    {
        if ($this->request->isPost()) {
            $ids = input("post.ids");
            if (empty($ids)) {
                return $this->error('请选择您要备份的数据表！');
            }
            if (!is_array($ids)) {
                $tables[] = $ids;
            } else {
                $tables = $ids;
            }
            // 读取备份配置
            $config = array(
                'path'     => config('huoke.db.backup_path') . DIRECTORY_SEPARATOR,
                'part'     => config('huoke.db.part_size'),
                'compress' => config('huoke.db.compress'),
                'level'    => config('huoke.db.compress_level'),
            );
            // 检查是否有正在执行的任务
            $lock = "{$config['path']}backup.lock";
            if (is_file($lock)) {
                return $this->error('检测到有一个备份任务正在执行，请稍后再试！');
            } else {
                if (!is_dir($config['path'])) {
                    Dir::create($config['path'], 0755, true);
                }
                //创建锁文件
                file_put_contents($lock, $this->request->time());
            }
            // 生成备份文件信息
            $file = [
                'name' => date('Ymd-His', $this->request->time()),
                'part' => 1,
            ];
            // 创建备份文件
            $database = new dbOper($file, $config);
            $start = 0;
            if ($database->create() !== false) {
                // 备份指定表
                foreach ($tables as $table) {
                    $start = $database->backup($table, $start);
                    while (0 !== $start) {
                        if (false === $start) {
                            return $this->error('备份出错！');
                        }
                        $start = $database->backup($table, $start[0]);
                    }
                }
                // 备份完成，删除锁定文件
                unlink($lock);
                return $this->success('备份完成。');
            }
            return $this->error('备份出错！');
        }
        global $_W;
        $title = "备份数据库";
        $tablepre = $_W['config']['db']['tablepre'] . MODULE_NAME;
        $list = db()->query("SHOW TABLE STATUS like '" . $tablepre . "%'");
        $this->assign(compact('title', 'list'));
        return $this->label_fetch();
    }

    /**
     * 恢复数据库
     */
    public function import()
    {
        if ($this->request->isPost()) {
            $id = input("id");
            if (empty($id)) {
                return $this->error('请选择您要恢复的备份文件！');
            }
            $name  = date('Ymd-His', $id) . '-*.sql*';
            $path  = trim(config('huoke.db.backup_path')) . DIRECTORY_SEPARATOR . $name;
            $files = glob($path);
            $list  = array();
            foreach ($files as $name) {
                $basename = basename($name);
                $match    = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
                $gz       = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
                $list[$match[6]] = array($match[6], $name, $gz);
            }
            ksort($list);
            // 检测文件正确性
            $last = end($list);
            if (count($list) === $last[0]) {
                foreach ($list as $item) {
                    $config = [
                        'path'     => trim(config('huoke.db.backup_path')) . DIRECTORY_SEPARATOR,
                        'compress' => $item[2]
                    ];
                    $database = new dbOper($item, $config);
                    $start = $database->import(0);
                    // 导入所有数据
                    while (0 !== $start) {
                        if (false === $start) {
                            return $this->error('数据恢复出错！');
                        }
                        $start = $database->import($start[0]);
                    }
                }
                return $this->success('数据恢复完成。');
            }
            return $this->error('备份文件可能已经损坏，请检查！');
        }
        $title = "恢复数据库";
        $path = trim(config('huoke.db.backup_path')) . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            Dir::create($path);
        }
        $flag = \FilesystemIterator::KEY_AS_FILENAME;
        $glob = new \FilesystemIterator($path,  $flag);
        $list = [];
        foreach ($glob as $name => $file) {
            if (preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)) {
                $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d');
                $date = "{$name[0]}-{$name[1]}-{$name[2]}";
                $time = "{$name[3]}:{$name[4]}:{$name[5]}";
                $part = $name[6];

                if (isset($list["{$date} {$time}"])) {
                    $info = $list["{$date} {$time}"];
                    $info['part'] = max($info['part'], $part);
                    $info['size'] = $info['size'] + $file->getSize();
                } else {
                    $info['part'] = $part;
                    $info['size'] = $file->getSize();
                }

                $extension        = strtoupper($file->getExtension());
                $info['compress'] = ($extension === 'SQL') ? '无' : $extension;
                $info['time']     = strtotime("{$date} {$time}");

                $list["{$date} {$time}"] = $info;
            }
        }
        $this->assign(compact('title', 'list'));
        return $this->label_fetch();
    }

    /**
     * 删除备份文件
     */
    public function del()
    {
        $id = input("id");
        if (empty($id)) {
            return $this->error('请选择您要删除的备份文件！');
        }
        $name  = date('Ymd-His', $id) . '-*.sql*';
        $path = trim(config('huoke.db.backup_path')) . DIRECTORY_SEPARATOR . $name;
        array_map("unlink", glob($path));
        if (count(glob($path)) && glob($path)) {
            return $this->error('备份文件删除失败，请检查权限！');
        }
        return $this->success('备份文件删除成功。');
    }

    /**
     * 数据库优化
     */
    public function optimize()
    {
        $ids = input("ids");
        if (empty($ids)) {
            return $this->error('请选择您要优化的数据表！');
        }

        if (!is_array($ids)) {
            $table[] = $ids;
        } else {
            $table = $ids;
        }

        $tables = implode('`,`', $table);
        $res = db()->query("OPTIMIZE TABLE `{$tables}`");
        if ($res) {
            return $this->success('数据表优化完成。');
        }
        return $this->error('数据表优化失败！');
    }

    /**
     * 数据库修复
     */
    public function repair()
    {
        $ids = input("ids");
        if (empty($ids)) {
            return $this->error('请选择您要修复的数据表！');
        }

        if (!is_array($ids)) {
            $table[] = $ids;
        } else {
            $table = $ids;
        }

        $tables = implode('`,`', $table);
        $res = db()->query("REPAIR TABLE `{$tables}`");
        if ($res) {
            return $this->success('数据表修复完成。');
        }
        return $this->error('数据表修复失败！');
    }

    /**
     * 执行SQL语句
     */
    public function sql()
    {
        if ($this->request->isPost()) {
            $sql = trim(input('post.sql'));
            if (!empty($sql)) {
                global $_W;
                $tablepre = $_W['config']['db']['tablepre'] . MODULE_NAME . '_';
                $sql = str_replace('{pre}', $tablepre, $sql);
                //查询语句返回结果集
                if (strtolower(substr($sql, 0, 6)) == "select") {
                    $res = db()->query($sql);
                    print_r($res);
                    $this->success('执行成功');
                } else {
                    db()->execute($sql);
                }
            }
            $this->success('执行成功');
        }
        $title = "执行SQL语句";
        $this->assign(compact('title'));
        return $this->label_fetch();
    }
}
