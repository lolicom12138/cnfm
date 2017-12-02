<?php
namespace Statistique\Controller;
use Statistique\Common\Controller\Base;
class RetirerController extends Base {
    public function index(){
       if(!testClassified($this->user,"statistiqueRetirer")){
       		$this->error("权限不足");
       		return;
       }
       $userModel = D('User');
       $list = $userModel->select();
       $this->assign("list",$list);
       $this->display();
    }
}