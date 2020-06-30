<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-23 09:35:30
 * @LastEditTime: 2020-06-26 14:19:18
 * @FilePath: \dev\application\admin\controller\Upload.php
 */

namespace app\admin\controller;

use think\facade\Env;

class Upload extends Base
{

    public function test()
    {
        $temp_file = tempnam(sys_get_temp_dir(), 'HuoKe');
        if ($temp_file) {
            echo '测试写入成功：' . $temp_file;
        } else {
            echo '写入失败，请检查临时文件目录权限：' . sys_get_temp_dir();
        }
    }

    public function upload()
    {
        // 获取表单上传文件 例如上传了001.mp4
        $file = request()->file('file');

        if (empty($file)) {
            return json_return([], 1001, '未找到上传的文件(原因：表单名可能错误，默认表单名“file”)！');
        }
        if ($file->getMime() == 'text/x-php') {
            return json_return([], 1002, '禁止上传php,html文件！');
        }

        $upload_image_ext = 'jpg,jpeg,png,gif';
        $upload_file_ext = 'doc,docx,xls,xlsx,ppt,pptx,pdf,wps,txt,rar,zip,torrent';
        $upload_media_ext = 'rm,rmvb,avi,mkv,mp4,mp3';

        // 格式校验
        if ($file->checkExt($upload_image_ext)) {
            $type = 'image';
        } elseif ($file->checkExt($upload_file_ext)) {
            $type = 'file';
        } elseif ($file->checkExt($upload_media_ext)) {
            $type = 'media';
        } else {
            return json_return([], 1003, '非系统允许的上传格式！');
        }

        // 上传附件路径
        $_upload_path = Env::get('root_path') . 'upload' . '/' . $type . '/';

        // 附件访问路径
        $_save_path = 'upload' . '/' . $type . '/';
        $ymd = date('Ymd');

        $n_dir = $ymd;
        // 每个文件夹存储999个文件
        for ($i = 1; $i <= 100; $i++) {
            $n_dir = $ymd . '-' . $i;
            $path1 = $_upload_path . $n_dir . '/';
            if (file_exists($path1)) {
                $farr = glob($path1 . '*.*');
                if ($farr) {
                    $fcount = count($farr);
                    if ($fcount > 999) {
                        continue;
                    } else {
                        break;
                    }
                } else {
                    break;
                }
            } else {
                break;
            }
        }

        $savename = $n_dir . '/' . md5(microtime(true));

        $upfile = $file->move($_upload_path, $savename);

        if (!is_file($_upload_path . $upfile->getSaveName())) {
            return json_return([], 1004, '文件上传失败！');
        }

        $file_size = round($upfile->getInfo('size') / 1024, 2);
        $data = [
            'file'  => module_url() . $_save_path . str_replace('\\', '/', $upfile->getSaveName()),
            'type'  => $type,
            'size'  => $file_size,
            'ctime' => request()->time()
        ];

        unset($upfile);

        return json_return($data, 1, '文件上传成功');
    }
}
