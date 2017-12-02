<?php

namespace Statistique\Controller;

use Statistique\Common\Controller\Base;

class PointConsoController extends Base{
	public function index(){
		if(!testClassified($this->user,"statistiquePointConso")){
			$this->error("权限不足");
			return;
		}
		$this->display();
	}
}