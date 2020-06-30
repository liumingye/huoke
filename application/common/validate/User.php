<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-26 09:15:35
 * @LastEditTime: 2020-06-26 10:03:46
 * @FilePath: \dev\application\common\validate\User.php
 */ 

namespace app\common\validate;

use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'nickname'  => 'max:16',
        'signature'  => 'max:255',
    ];

    protected $message  =   [
        'nickname.max'     => '昵称最多不能超过16个字符',
        'signature.max'     => '个性签名最多不能超过32个字符',
    ];

    protected $scene = [
        'add'  =>  ['nickname', 'signature'],
        'edit'  =>  ['nickname', 'signature'],
    ];
}
