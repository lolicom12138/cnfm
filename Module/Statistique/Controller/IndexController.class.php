<?php
namespace Statistique\Controller;
use Statistique\Common\Controller\Base;
class IndexController extends Base {
    public function index(){
    	
		$this->assign("user",$this->user);
		$this->display();
    }
}