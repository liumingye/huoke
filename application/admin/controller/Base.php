<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-12 16:30:13
 * @LastEditTime: 2020-06-27 11:10:42
 */

namespace app\admin\controller;

use app\common\controller\Common;

class Base extends Common
{
    protected $adminUser;

    public function initialize()
    {
        global $_W;
        parent::initialize();
        // 判断是否登录
        if (isset($_W["user"])) {
            $this->adminUser = $_W["user"];
        } else {
            $this->redirect($_W['siteroot']);
            die;
        }
    }

    /**
     * @param string $template
     */
    protected function label_fetch($tpl = null)
    {
        $html = view($tpl);
        // 页面压缩
        // $html = compressHtml($html->getContent());
        return $html;
    }
}
