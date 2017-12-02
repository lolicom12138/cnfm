<?php

namespace Statistique\Controller;

use Statistique\Common\Controller\Base;

class ClientGroupController extends Base{
	public function index(){
		if(!testClassified($this->user,"statistiqueClientGroup")){
			$this->error("权限不足");
			return;
		}
		$serveurList = D('User')->relation(true)->select();
		$this->assign("serveurList",$serveurList);
		$this->display();
	}
}