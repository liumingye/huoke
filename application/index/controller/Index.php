<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-26 13:28:40
 * @LastEditTime: 2020-06-26 13:31:27
 * @FilePath: \dev\application\index\controller\Index.php
 */ 

namespace app\index\controller;

class Index extends Base {
	
	public function index (){
        return $this->label_fetch();
    }
}
