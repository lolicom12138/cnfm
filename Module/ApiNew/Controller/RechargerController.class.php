<?php
namespace ApiNew\Controller;

use Think\Exception;

class RechargerController extends BaseController {
	public function __construct(){
		parent::__construct();
		/*
		if(!$this->user){
			$this->ajaxReturn(["status"=>false,"message"=>"login error"]);
			die;
		}
		*/
	}
	public function _empty(){
		$this->ajaxReturn(["status"=>false,"message"=>"url error"]);
	}
	public function index(){
		if(!IS_POST){
			$this->ajaxReturn(["status"=>false]);
			return;
		}
		$cardNumber = I('post.cardNumber');
		if(!$cardNumber){
			$this->ajaxReturn(["status"=>false,"message"=>"cardNumber error"]);
			return;
		}
		$vipModel = D('Vip');
		$rechargerModel = M('recharger');
		$vip = $vipModel->get($cardNumber);
		if(!$vip){
			$this->ajaxReturn(["status"=>false,"message"=>"cardNumber error"]);
			return;
		}
		$data = $this->makeDataRecharger(I('post.'), $vip);
		try{
			$vipModel->where(["id"=>$vip['id']])->save($data['vip']);
			$idRecharger = $rechargerModel->add($data);
			
		}catch(Exception $e){
			$this->ajaxReturn(["status"=>false,"message"=>$e->getMessage()]);
		}
	}
	private function makeDataRecharger($dataPost,$vip){
		$sum = $dataPost['sum'];
		$raison = $dataPost['raison'];
		$etc = $dataPost['etc'];
		$operator = $this->user;
		$data = array(
				"vip" => $vip['id'],
				"sum" => $sum,
				"raison" => $raison,
				"etc" => $etc,
				"operator" => $operator['id'],
				"time" => date("Y-m-d H:i:s"),
				"typeRecharger" => $dataPost['type']
		);
		switch($dataPost['sum']){
			case "credit":
				$dataVip = array(
				"credit" => $vip['credit']+$sum
				);
				$data['newSum'] = $dataVip['credit'];
				break;
			case "creditGive":
				$dataVip = array(
				"creditGive" => $vip['creditgive']+$sum
				);
				$data['newSum'] = $dataVip['creditGive'];
				break;
			case "pointRecharger":
				$dataVip = array(
				"pointRecharger" => $vip['pointrecharger']+$sum
				);
				if($vip['card']['name']=="生活卡"&&$sum>=200){
					$pointConso = intval($sum/200)*10;
					$dataVip['pointConso'] = $vip['pointconso']+$pointConso;
				}
				$data['newSum'] = $dataVip['pointRecharger'];
				break;
			case "pointGive":
				$dataVip = array(
				"pointGive" => $vip['pointgive']+$sum
				);
				$data['newSum'] = $dataVip['pointGive'];
				break;
			case "pointConso":
				$dataVip = array(
				"pointConso" => $vip['pointconso']+$sum
				);
				$data['newSum'] = $dataVip['pointConso'];
				break;
			case "pointTourism":
				$dataVip = array(
				"pointTourism" => $vip['pointtourism']+$sum
				);
				$data['newSum'] = $dataVip['pointTourism'];
				break;
			case "pointHeal":
				$dataVip = array(
				"pointHeal" => $vip['pointheal']+$sum
				);
				$data['newSum'] = $dataVip['pointHeal'];
				break;
		}
		return ["recharger"=>$data,"vip"=>$dataVip];
	}
}