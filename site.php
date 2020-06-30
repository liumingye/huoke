<?php

defined('IN_IA') or exit('Access Denied');

class huoke_videoModuleSite extends WeModuleSite {

    public function __call($name, $arguments)
    {
        include __DIR__.'/index.php';
        exit;
    }

}
