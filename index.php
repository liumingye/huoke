<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-16 13:33:23
 * @LastEditTime: 2020-06-22 09:30:37
 */ 

namespace think;

//thinkphp开始接替
global $_GPC;
//微擎模块
$m = $_GPC['m'];
//设置常量
define('MODULE_NAME',$m);
//tp模块
$tpM = $_GPC['do'] ? $_GPC['do']:'index';
//tp控制器
$tpC = isset($_GPC['tp_c'])? $_GPC['tp_c']:'index';
$_GPC['tp_c'] = $tpC = strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $tpC), "_"));
//tp方法
$_GPC['tp_a'] =  $tpA = isset($_GPC['tp_a'])? trim($_GPC['tp_a']):'index';

// 加载基础文件
require __DIR__ . '/thinkphp/base.php';
// 执行应用并响应
Container::get('app')->path(__DIR__.'/application/')->bind($tpM."/".$tpC."/".$tpA)->run()->send();
