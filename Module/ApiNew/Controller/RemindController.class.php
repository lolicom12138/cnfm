<?php
namespace ApiNew\Controller;
class RemindController extends BaseController {
	public function __construct(){
		/*
		parent::__construct();
		if(!$this->user){
			$this->ajaxReturn(["status"=>false,"message"=>"login error"]);
			die;
		}
		*/
	}
	public function _empty(){
		$this->ajaxReturn(["status"=>false,"message"=>"url error"]);
	}
	public function cardTryFifty(){
		$where = [
				"card"=>6,
				"active"=>'1'
		];
		$list = D('Vip')->where($where)->relation(true)->select();
		foreach($list as $key=>$value){
			$lastRecharger = end($value['recharger']);
			if(time()-strtotime($lastRecharger['time']) <= 4320000){
				unset($list[$key]);
			}
		}
		$this->ajaxReturn(["status"=>true,"list"=>$list]);
	}
	public function cardTrySixty(){
		$model = D('Vip');
		$where = [
				"card"=>6,
				"active"=>'1'
		];
		$list = $model->where($where)->relation(true)->select();
		foreach($list as $key=>$value){
			$lastRecharger = end($value['recharger']);
			if(time()-strtotime($lastRecharger['time']) <= 5184000){
				unset($list[$key]);
			}
		}
		$this->ajaxReturn(["status"=>true,"list"=>$list]);
	}
	public function cancelCardTrySixty(){
		$where = [
				"card"=>6,
				"active"=>'1'
		];
		$list = D('Vip')->where($where)->relation(true)->select();
		$listToCancel = [];
		foreach($list as $key=>$value){
			$lastRecharger = end($value['recharger']);
			if(time()-strtotime($lastRecharger['time']) > 5184000){
				array_push($listToCancel,$value['id']);
			}
		}
		$where = [
				"id"=>[
						"in",
						$listToCancel
				]
		];
		$data = [
				"active"=>0,
				"dateExpire"=>date("Y-m-d")
		];
		D('Vip')->where($where)->save($data);
		$this->ajaxReturn(["status"=>true]);
	}
}