<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-23 09:35:33
 * @LastEditTime: 2020-06-24 14:59:50
 * @FilePath: \dev\application\common\validate\Video.php
 */

namespace app\common\validate;

use think\Validate;

class Video extends Validate
{
    protected $rule =   [
        'msg'  => 'require|max:255',
        'src'  => 'require',
        'uid'  => 'require',
        'create_time'  => 'require|date',
    ];

    protected $message  =   [
        'msg.require' => '描述必须',
        'msg.max'     => '描述最多不能超过255个字符',
        'src.require' => '播放链接必须',
        'uid.require' => '发布用户ID必须',
        'create_time'  => '创建日期不正确',
    ];

    protected $scene = [
        'add'  =>  ['msg', 'src', 'uid', 'create_time'],
        'edit'  =>  ['msg', 'src', 'uid', 'create_time'],
    ];
}
