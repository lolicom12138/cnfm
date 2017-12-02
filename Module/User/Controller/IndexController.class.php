<?php
namespace User\Controller;
use User\Common\Controller\Base;
class IndexController extends Base {
    public function index(){
    	$this->assign("user",$this->user);
    	$this->display();
    }
}