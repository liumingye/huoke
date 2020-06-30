<?php

defined('IN_IA') or exit('Access Denied');

class huoke_videoModuleWxapp extends WeModuleWxapp {

    public function __call($name, $arguments)
    {
        include __DIR__.'/index.php';
        exit;
    }

}
