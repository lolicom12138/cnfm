<?php
namespace Vip\Controller;
use Vip\Common\Controller\Base;
class StatisticController extends Base {
	public function listClientIndex(){
		if(!testClassified($this->user,"listClient")){
			$this->error("权限不足");
			return;
		}
		$this->display();
	}
    public function listClient(){
    	if(!testClassified($this->user,"listClient")){
    		$this->error("权限不足");
    		return;
    	}
    	$this->display();
    }
    public function listVipIndex(){
    	if(!testClassified($this->user,"listVip")){
    		$this->error("权限不足");
    		return;
    	}
    	$this->display();
    }
    public function listVip(){
    	if(!testClassified($this->user,"listVip")){
    		$this->error("权限不足");
    		return;
    	}
    	$cardModel = D('Card');
    	$list = $cardModel->select();
    	$this->assign("list",$list);
    	$this->display();
    }
    public function listRecharger(){
    	$this->assign("user",$this->user);
    	$this->display();
    }
}