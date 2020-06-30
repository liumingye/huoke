<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-22 09:14:48
 * @LastEditTime: 2020-06-22 15:42:35
 * @FilePath: \dev\application\common\model\Category.php
 */ 

namespace app\common\model;

use think\Model;

class Category extends Model
{
    protected $name = 'category';

    // 自动写入时间戳
    protected $autoWriteTimestamp = 'datetime';
    
}
