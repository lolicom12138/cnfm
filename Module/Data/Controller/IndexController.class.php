<?php
namespace Data\Controller;
use Data\Common\Controller\Base;
class IndexController extends Base {
    public function index(){
    	$this->assign("user",$this->user);
    	$this->display();
    }
}