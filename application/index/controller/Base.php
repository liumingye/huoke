<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-26 13:29:44
 * @LastEditTime: 2020-06-26 13:30:30
 * @FilePath: \dev\application\index\controller\Base.php
 */

namespace app\index\controller;

use think\Controller;
use think\facade\View;

class Base extends Controller
{

    protected function label_fetch($tpl = null)
    {
        $html = View::fetch($tpl);
        return $html;
    }
}
