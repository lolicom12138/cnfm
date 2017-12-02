<?php
namespace Statistique\Controller;
use Statistique\Common\Controller\Base;
class VipController extends Base {
    public function index(){
       if(!testClassified($this->user,"statistiqueVip")){
       		$this->error("权限不足");
       		return;
       }
       $cardModel = D('Card');
       $list = $cardModel->select();
       $this->assign("list",$list);
       $this->display();
    }
    public function cardTryFifty(){
    	$this->display();
    }
    public function cardTrySixty(){
    	$this->display();
    }
}