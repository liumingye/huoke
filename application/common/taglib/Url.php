<?php
/*
         * @Created by: VSCode
         * @User: BigLop
         * @Date: 2020-06-16 15:31:26
         * @LastEditTime: 2020-06-22 09:28:34
         */

namespace app\common\taglib;

use think\template\TagLib;

class Url extends TagLib
{
    /**
     * 定义标签列表
     */
    protected $tags =  [
        /**标签定义
        * attr：属性列表
        * close：是否闭合（0表示闭合，1表示不闭合。默认值为1）
        * alias：标签别名
        * level：嵌套层次
        */
        'tpurl'      => ['attr' => 'url', 'close' => 0],

    ];

    /**
     * 不闭合标签
     * $tag：存放标签属性的数组
     * $content：标签内的数据
     */
    public static function tagTpurl($tag)
    {
        return "./index.php?c=site&a=entry&do={$tag['url']}&m=" . MODULE_NAME . "&version_id=1";
    }
}
