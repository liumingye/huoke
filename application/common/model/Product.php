<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-12 11:22:24
 * @LastEditTime: 2020-06-20 14:57:04
 */ 

namespace app\common\model;

use think\Model;

class Product extends Model
{
    protected $name = 'product';

    // 自动写入时间戳
    protected $autoWriteTimestamp = 'datetime';

}
