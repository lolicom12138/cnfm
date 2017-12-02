<?php
namespace Statistique\Controller;
use Statistique\Common\Controller\Base;
class GiftController extends Base {
    public function index(){
       if(!testClassified($this->user,"statistiqueGift")){
       		$this->error("权限不足");
       		return;
       }
       $userList = D('User')->relation(true)->select();
       $this->assign("userList",$userList);
       $this->display();
    }
}