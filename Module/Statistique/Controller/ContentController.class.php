<?php
namespace Statistique\Controller;
use Statistique\Common\Controller\Base;
class ContentController extends Base {
    public function index(){
		$vipModel = D('Vip');
		$where = array(
				"dateCard"=>date("Y-m-d",strtotime("-1 day")),
		);
		$where['card'] = 1;
		$countYestodayLife = $vipModel->where($where)->count();
		$where['card'] = 6;
		$countYestodayPoint = $vipModel->where($where)->count();
		$where['card'] = array(
				"in",
				"2,3,4,5"
		);
		$countYestodayLiberty = $vipModel->where($where)->count();
		$cardModel = M('card');
		$cards = $cardModel->where(array("name"=>"自由卡"))->select();
		$cardLiberty = array();
		foreach($cards as $c){
			array_push($cardLiberty,$c['id']);
		}
		$where = array(
				"card"=>array(
						"in",
						$cardLiberty
				)
		);
		$listCardLiberty = $vipModel->where($where)->select();
		$lessFouty = array();
		if(count($listCardLiberty)){
			foreach($listCardLiberty as $l){
				if($l['credit'] < $l['card']['credit']*0.4){
					array_push($lessFouty,$l);
				}
			}
		}
		$clientModel = D('Client');
		$where = array(
				"dateBirth"=>array(
						"like",
						"%".date("-m-d")."%"
				)
		);
		$listClientBirthday = $clientModel->where($where)->select();
		$presentNum = M('clientPresent')->where(array(
				"timeFin"=>array(
						"like",
						"%".date("Y-m-d",strtotime("-1 day"))."%"
				)	
		))->count();
		$rechargerModel = D('Recharger');
		$where = array(
				"time"=>array(
						"like",
						"%".date("Y-m-d",strtotime("-1 day"))."%"
				),
				"typeRecharger"=>"credit"
		);
		$listRecharger = $rechargerModel->where($where)->select();
		$totalCreditRecharger = 0;
		for($i = 0;$listRecharger[$i];$i++){
			$totalCreditRecharger+=$listRecharger[$i]['sum'];
		}
		$where = array(
				"time"=>array(
						"like",
						"%".date("Y-m-d",strtotime("-1 day"))."%"
				),
				"typeRecharger"=>"creditGive"
		);
		$listRecharger = $rechargerModel->where($where)->select();
		$totalCreditGiveRecharger = 0;
		for($i = 0;$listRecharger[$i];$i++){
			$totalCreditGiveRecharger+=$listRecharger[$i]['sum'];
		}
		
		$where = array(
				"time"=>array(
						"like",
						"%".date("Y-m-d",strtotime("-1 day"))."%"
				),
				"typeRecharger"=>"pointRecharger"
		);
		$listRecharger = $rechargerModel->where($where)->select();
		$totalPointRechargerRecharger = 0;
		for($i = 0;$listRecharger[$i];$i++){
			$totalPointRechargerRecharger+=$listRecharger[$i]['sum'];
		}
		
		$where = array(
				"time"=>array(
						"like",
						"%".date("Y-m-d",strtotime("-1 day"))."%"
				),
				"typeRecharger"=>"pointGive"
		);
		$listRecharger = $rechargerModel->where($where)->select();
		$totalPointGiveRecharger = 0;
		for($i = 0;$listRecharger[$i];$i++){
			$totalPointGiveRecharger+=$listRecharger[$i]['sum'];
		}
		
		$where = array(
				"time"=>array(
						"like",
						"%".date("Y-m-d",strtotime("-1 day"))."%"
				),
				"typeRecharger"=>"pointHeal"
		);
		$listRecharger = $rechargerModel->where($where)->select();
		$totalPointHealRecharger = 0;
		for($i = 0;$listRecharger[$i];$i++){
			$totalPointHealRecharger+=$listRecharger[$i]['sum'];
		}
		
		$where = array(
				"time"=>array(
						"like",
						"%".date("Y-m-d",strtotime("-1 day"))."%"
				),
				"typeRecharger"=>"pointTourism"
		);
		$listRecharger = $rechargerModel->where($where)->select();
		$totalPointTourismRecharger = 0;
		for($i = 0;$listRecharger[$i];$i++){
			$totalPointTourismRecharger+=$listRecharger[$i]['sum'];
		}
		
		//前一日消费金额
		$where = array(
				"time"=>array(
						"like",
						"%".date("Y-m-d",strtotime("-1 days"))."%"
				),
				"status"=>"已完成"
		);
		$orderModel = D('Order');
		$yestodayOrder = $orderModel->where($where)->select();
		$totalConso = array(
				"all"=>0,
				"credit"=>0,
				"creditGive"=>0,
				"pointRecharger"=>0,
				"pointGive"=>0,
				"pointConso"=>0,
				"pointHeal"=>0,
				"pointTourism"=>0,
		);
		if(count($yestodayOrder)){
			$temp = array();
			foreach($yestodayOrder as $y){
				array_push($temp,$y['id']);
			}
			$yestodayConso = M("pointconsoRecord")->where(array("idOrder"=>array("in",$temp)))->select();
			foreach($yestodayConso as $y){
				$totalConso['credit'] += $y['credit'];
				$totalConso['creditGive'] += $y['creditgive'];
				$totalConso['pointRecharger'] += $y['pointrecharger'];
				$totalConso['pointGive'] += $y['pointgive'];
				$totalConso['pointConso'] += $y['pointconso'];
				$totalConso['pointTourism'] += $y['pointtourism'];
				$totalConso['pointHeal'] += $y['pointheal'];
			}
			$totalConso['all'] = $totalConso['credit']+$totalConso['creditGive']+$totalConso['pointRecharger']+$totalConso['pointGive']+$totalConso['pointConso']+$totalConso['pointTourism']+$totalConso['pointHeal'];
			/*
			foreach($yestodayOrder as $y){
				$totalConso['all'] += $y['pricesold'];
				$model = M('pointconsoRecord');
				switch($y['typepay']){
					case "credit":
						$totalConso['credit'] += $y['pricesold'];
						break;
					case "creditGive":
						$totalConso['creditGive'] += $y['pricesold'];
						break;
					case "pointHeal":
						$totalConso['pointHeal'] += $y['pricesold'];
						break;
					case "pointTourism":
						$totalConso['pointTourism'] += $y['pricesold'];
						break;
					case "pointConso":
						$result = $model->where(array("idOrder"=>$y['id']))->find();
						$totalConso['pointRecharger'] = $result['pointrecharger'];
						$totalConso['pointGive'] = $result['pointgive'];
						$totalConso['pointConso'] = $result['pointconso'];
						break;
				}
			}
			*/
		}
		//前一日计次消费
		$where = array(
				"time"=>array(
						"like",
						"%".date("Y-m-d",strtotime("-1 days"))."%"
				),
		);
		$listFois = M('foisRecord')->where($where)->select();
		$foisModel = D('Fois');
		$userModel = D('User');
		for($i = 0;$listFois[$i];$i++){
			$listFois[$i]['fois'] = $foisModel->get($listFois[$i]['fois']);
			$listFois[$i]['operator'] = $userModel->get($listFois[$i]['operator']);
		}
		
		$this->assign("listFois",$listFois);
		$this->assign("totalConso",$totalConso);
		$this->assign("listClientBirth",$listClientBirthday);
		$this->assign("yestodayLifeNumber",$countYestodayLife);
		$this->assign("yestodayLibertyNumber",$countYestodayLiberty);
		$this->assign("yestodayPointNumber",$countYestodayPoint);
		$this->assign("totalCreditRecharger",$totalCreditRecharger);
		$this->assign("totalCreditGiveRecharger",$totalCreditGiveRecharger);
		$this->assign("totalPointRechargerRecharger",$totalPointRechargerRecharger);
		$this->assign("totalPointGiveRecharger",$totalPointGiveRecharger);
		$this->assign("totalPointHealRecharger",$totalPointHealRecharger);
		$this->assign("totalPointTourismRecharger",$totalPointTourismRecharger);
		$this->assign("numPresent",$presentNum);
		$this->assign("lessFourty",$lessFouty);
		$this->assign("nombreVip",$nombreVip);
		$this->display();
    }
}