<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-16 13:57:45
 * @LastEditTime: 2020-06-26 13:42:06
 */

namespace app\common\controller;

use think\Controller;

class Common extends Controller
{
    protected $uniacid;

    public function initialize()
    {
        global $_W;
        $this->uniacid = $_W["uniacid"];
    }
}
