<?php
namespace Vip\Controller;
use Vip\Common\Controller\Base;
class IndexController extends Base {
    public function index(){
    	$this->assign("user",$this->user);
    	$this->display();
    }
}