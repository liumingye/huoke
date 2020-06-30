<?php

defined('IN_IA') or exit('Access Denied');

class huoke_videoModule extends WeModule {

	public function welcomeDisplay()
    {
    	global $_W;
        $url = $this->createWebUrl('admin');
        if ($_W['role'] == 'operator') {
        	message("您不是管理员,无权限访问");
        }
        Header("Location: " . $url);
    }

}
