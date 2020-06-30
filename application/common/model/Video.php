<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-09 14:31:42
 * @LastEditTime: 2020-06-27 11:28:05
 */

namespace app\common\model;

class Video extends Base
{
    // 设置数据表（不含前缀）
    protected $name = 'video';

    // 自动写入时间戳
    protected $autoWriteTimestamp = 'datetime';

    /**
     * 计数数据
     */
    public function countData($where)
    {
        $total = $this->where($where)->count();
        return $total;
    }

    /**
     * 列出数据
     */
    public function listData($where, $order, $start = 0, $length = 10, $with = [], $field = '*')
    {
        if (!is_array($where)) {
            $where = json_decode($where, true);
        }
        $total = $this->countData($where);
        $res = $this->alias('v')->where($where)->order($order)->limit($start, $length);
        if (array_key_exists('user', $with) || in_array('user', $with)) {
            $res = $res->buildSql();
            $where = isset($with['user']['where']) ? $with['user']['where'] : '';
            $res = model('User')->where($where)->alias('u')->join([$res => 'v'], 'u.id = v.uid');
        }
        $list = $res->field($field)->select();
        return ['code' => 1, 'msg' => '数据列表', 'total' => $total, 'list' => $list];
    }

    /**
     * 获取数据
     */
    public function infoData($where, $field = '*')
    {
        if (empty($where) || !is_array($where)) {
            return ['code' => 1001, 'msg' => '参数错误'];
        }
        $info = $this->field($field)->where($where)->find();
        if (empty($info)) {
            return ['code' => 1002, 'msg' => '获取数据失败'];
        }
        $info = $info->toArray();
        return ['code' => 1, 'msg' => '获取成功', 'info' => $info];
    }

    /**
     * 保存数据
     */
    public function saveData($data)
    {
        $validate = validate('Video');
        if (!$validate->check($data)) {
            return ['code' => 1001, 'msg' => '参数错误：' . $validate->getError()];
        }
        $data['update_time'] = date("Y-m-d H:i:s", time());
        // 自动判断 插入 或 更新
        if (!empty($data['id'])) {
            $res = $this->allowField(true)->where(['id' => $data['id']])->update($data);
        } else {
            $data['create_time'] = date("Y-m-d H:i:s", time());
            $data['uniacid'] = $this->uniacid;
            $res = $this->allowField(true)->insert($data);
        }
        if (false === $res) {
            return ['code' => 1002, 'msg' => '保存失败：' . $this->getError()];
        }
        return ['code' => 1, 'msg' => '保存成功'];
    }

    /**
     * 删除数据
     */
    public function delData($where)
    {
        global $_W;
        // 删除上传的文件
        $list = $this->listData($where, '', 0, 9999);
        if ($list['code'] !== 1) {
            return ['code' => 1001, 'msg' => '删除失败：' . $this->getError()];
        }
        $path = $_W['siteroot'];
        foreach ($list['list'] as $val) {
            // 删除 视频
            if (isset($val['src'])) {
                $val['src'] = str_replace($path, '../', $val['src']);
                if (file_exists($val['src'])) {
                    unlink($val['src']);
                }
            }
            // 删除 预览图
            if (isset($val['cover'])) {
                $val['cover'] = str_replace($path, '../', $val['cover']);
                if (file_exists($val['cover'])) {
                    unlink($val['cover']);
                }
            }
        }
        $res = $this->where($where)->delete();
        if ($res === false) {
            return ['code' => 1002, 'msg' => '删除失败：' . $this->getError()];
        }
        return ['code' => 1, 'msg' => '删除成功'];
    }
}
