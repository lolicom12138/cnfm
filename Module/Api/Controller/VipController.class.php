<?php

namespace Api\Controller;

use Think\Controller;

class VipController extends Controller{
	public function getVip(){
		$cardNumber = I('post.cardNumber');
		if(!$cardNumber){
			$str = '{"status": "error"}';
			echo $str;
			return;
		}
		$vipModel = D('Vip');
		$card = $vipModel->get($cardNumber);
		if(!$card){
			$str = '{"status": "error"}';
			echo $str;
			return;
		}
		if(!$card['active']){
			$str = '{"status": "no active"}';
			echo $str;
			return;
		}
		if($card['dateexpire'] && $card['dateexpire']<date("Y-m-d")){
			$str = '{"status": "expire"}';
			echo $str;
			return;
		}
		$str = '{"status":"ok","name":"'.$card['client']['name'].'","card":"'.$card['card']['name'].'","credit":"'.$card['credit'].'","creditGive":"'.$card['creditgive'].'","pointRecharger":"'.$card['pointrecharger'].'","pointGive":"'.$card['pointgive'].'","pointConso":"'.$card['pointconso'].'","pointHeal":"'.$card['pointheal'].'","pointTourism":"'.$card['pointtourism'].'","dateCard":"'.$card['datecard'].'","dateActive":"'.$card['dateactive'].'","dateExpire":"'.$card['dateexpire'].'"}';
		echo $str;
	}
	public function presentDaily(){
		if(!IS_POST){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$cardNumber = I('post.cardNumber');
		if(!$cardNumber){
			echo '{
    			"status":"no card number"
    		}';
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($cardNumber);
		if(!$vip){
			echo '{
    			"status":"card not found"
					"cardNumber":"'.$cardNumber.'"
    		}';
			return;
		}
		/*
		if($vip['card']['name']!="生活卡"){
			echo '{
    			"status":"card type invalid"
    		}';
			return;
		}
		*/
		if(!$vip['active']){
			echo '{
    			"status":"not active"
    		}';
			return;
		}
		$presentPolicy = M('presentPoint')->where(array("card"=>$vip['card']['id']))->find();
		if(!$presentPolicy){
			apiReturn("card type invalid");
			return;
		}
		$presentModel = M('clientPresent');
		$where = array(
				"client" => $vip['client']['id'],
				"timeDebut" => array(
						"like",
						"%".date("Y-m-d")."%"
				)
		);
		$present = $presentModel->where($where)->find();
		$time = date("H:i:s");
		if(!$present){
			if($time < $presentPolicy['timebegin'] || $time > $presentPolicy['timeend']){
				apiReturn("time wrong");
				return;
			}
			$data = array(
					"client" => $vip['client']['id'],
					"timeDebut" => date("Y-m-d H:i:s")
			);
			$idRecord = $presentModel->add($data);
			if($idRecord){
				$operator = session("user");
				$data = array(
						"operator"=>$operator['id'],
						"operation"=>"日常签到",
						"content"=>"会员卡号:".$vip['cardnumber'],
						"time"=>date("Y-m-d H:i:s"),
						"ip"=>get_client_ip(),
				);
				M('operationRecord')->add($data);
				echo '{
	    			"status":"ok"
	    		}';
			}else{
				echo '{
	    			"status":"fail"
	    		}';
			}
		}else{
			if($present['timefin']){
				echo '{
	    			"status":"already"
	    		}';
				return;
			}
			if($time > $presentPolicy['timeend']){
				apiReturn("time wrong");
				return;
			}
			$data['timeFin'] = date("Y-m-d H:i:s");
			$timeOk = (strtotime($data['timeFin'])-strtotime($present['timedebut'])>1800);
			if(!$timeOk&&!I('post.confirm')){
				echo '{
	    			"status":"not enough"
	    		}';
				return;
			}
			if($timeOk){
				$givable = M('pointgive')->find();
				if($givable['pointgive'] >= 0){
					$dataVip = array(
							"pointGive" => $vip['pointgive']+$presentPolicy['point']
					);
					$result = $vipModel->where("id='".$vip['id']."'")->save($dataVip);
					if(!is_numeric($result)){
						echo '{
		    			"status":"fail"
		    		}';
						return;
					}
				}else{
					$data['etc'] = "送分已到限额，未送分";
				}
				$result = $presentModel->where("id='".$present['id']."'")->save($data);
				if($result){
					if($givable['pointgive'] >= 0){
						$operator = session("user");
						$data = array(
								"vip"=>$vip['id'],
								"sum"=>$presentPolicy['point'],
								"typeRecharger"=>"pointGive",
								"raison"=>"日常签到送分",
								"time"=>date("Y-m-d H:i:s"),
								"operator"=>$operator['id'],
						);
						$rechargerModel = M('recharger');
						$rechargerModel->add($data);
					}
					$operator = session("user");
					$data = array(
							"operator"=>$operator['id'],
							"operation"=>"日常签退",
							"content"=>"会员卡号:".$vip['cardnumber'],
							"time"=>date("Y-m-d H:i:s"),
							"ip"=>get_client_ip(),
					);
					M('operationRecord')->add($data);
					echo '{
		    			"status":"ok"
		    		}';
				}else{
					$dataVip = array(
							"pointGive" => $vip['pointgive']
					);
					$vipModel->where("id='".$vip['id']."'")->save($dataVip);
					echo '{
		    			"status":"fail"
		    		}';
				}
			}else{
				$result = $presentModel->where("id='".$present['id']."'")->save($data);
				if($result){
					$operator = session("user");
					$data = array(
							"operator"=>$operator['id'],
							"operation"=>"日常签退",
							"content"=>"会员卡号:".$vip['cardnumber'],
							"time"=>date("Y-m-d H:i:s"),
							"ip"=>get_client_ip(),
					);
					M('operationRecord')->add($data);
					echo '{
		    			"status":"ok"
		    		}';
				}else{
					echo '{
		    			"status":"fail"
		    		}';
				}
			}
		}
	}
	public function recharger(){
		if(!IS_POST){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$cardNumber = I('post.cardNumber');
		if(!$cardNumber){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($cardNumber);
		if(!$vip){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$sum = I('post.sum');
		$raison = I('post.raison');
		$etc = I('post.etc');
		$operator = session("user");
		$data = array(
				"vip" => $vip['id'],
				"sum" => $sum,
				"raison" => $raison,
				"etc" => $etc,
				"operator" => $operator['id'],
				"time" => date("Y-m-d H:i:s"),
				"typeRecharger" => I('post.type')
		);
		switch(I('post.type')){
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
					//$dataVip['pointConso'] = $vip['pointconso']+$sum*0.05;
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
		$result = $vipModel->where("id='".$vip['id']."'")->save($dataVip);
		if(!is_numeric($result)){
			echo '{
						"status":"fail"
					}';
			return;
		}
		$rechargerModel = M('recharger');
		$result = $rechargerModel->add($data);
		if($result){
			if(isset($pointConso)){
				$dataPointConso = array(
						"vip" => $vip['id'],
						"sum" => $pointConso,
						"newSum"=>$dataVip['pointConso'],
						"raison" => "充值储值积分赠送",
						"etc" => "",
						"operator" => $operator['id'],
						"time" => date("Y-m-d H:i:s"),
						"typeRecharger" => "pointConso"
				);
				$rechargerModel->add($dataPointConso);
			}
			$card = M('card')->where(array("name"=>"自由卡","level"=>1))->find();
			if($vip['card']['name'] == "自由卡" && I('post.type') == "credit" && $sum > $card['credit']){
				$policy = $policyModel->where(array("card"=>$card['id']))->find();
				$dataPolicy = array(
						"vip"=>$vip['id'],
						"pointGive"=>$policy['pointgive'],
						"timePointGive"=>$policy['timepointgive'],
						"lifeShipping"=>$policy['lifeshipping'],
						"timeLifeShipping"=>$policy['timelifeshipping'],
				);
				if($dataPolicy['timePointGive'] == 1){
					$dataPolicy['timePointGive'] = 0;
					$dataPolicy['pointGive'] = 0;
				}
				$policyRecordModel = M('libertyPolicyRecord');
				$resultPolicy = $policyRecordModel->add($dataPolicy);
				$data = array(
						"pointGive"=>$vip['pointgive']+$policy['pointgive'],
						"pointHeal"=>$vip['pointheal']+$policy['pointheal'],
						"pointTourism"=>$vip['pointtourism']+$policy['pointtourism'],
				);
				$vipModel->where(array("id"=>$vip['id']))->save($data);
				/*
				$data = array(
						"vip"=>$vip['id'],
						"price"=>$policy['lifeshipping'],
						"fois"=>$policy['timelifeshipping']
				);
				M('lifeshipping')->add($data);
				*/
			}
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"储值",
					"content"=>"会员卡号:".$vip['cardnumber']." 类型:".I('post.type')." 金额:".$sum." 原因:".$raison,
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			echo '{
				"status":"ok"
			}';
		}else{
			echo '{
				"status":"record fail"		
			}';
		}
	}
	public function presentActivity(){
		if(!IS_POST){
			apiReturn("false");
			return;
		}
		$idActivity = I('post.activity');
		$cardNumber = I('post.cardNumber');
		if(!$idActivity || !$cardNumber){
			apiReturn("false");
			return;
		}
		$activityModel = D('Activity');
		$vipModel = D('Vip');
		$activity = $activityModel->get($idActivity);
		if(!$activity){
			apiReturn("false");
			return;
		}
		if($activity['timeend'] < date("Y-m-d H:i:s")){
			apiReturn("expire");
			return;
		}
		if($activity['timebegin'] > date("Y-m-d H:i:s")){
			apiReturn("early");
			return;
		}
		$vip = $vipModel->get($cardNumber);
		if(!$vip){
			apiReturn("false");
			return;
		}
		if($vip['card']['name'] != "生活卡"){
			apiReturn("card type invalid");
			return;
		}
		if(!$vip['active']){
			apiReturn("not active");
			return;
		}
		$clientActivityModel = M('clientActivity');
		$where = array(
				"client"=>$vip['client']['id'],
				"activity"=>$activity['id'],
		);
		$result = $clientActivityModel->where($where)->find();
		if($result){
			apiReturn("already");
			return;
		}
		$where['time'] = date("Y-m-d H:i:s");
		$record = $clientActivityModel->add($where);
		if(!$record){
			apiReturn("fail");
			return;
		}
		$data = array(
				"pointGive"=>$vip['pointgive']+$activity['point']
		);
		if(is_numeric($vipModel->where(array("id"=>$vip['id']))->save($data))){
			$operator = session("user");
			$data = array(
					"vip"=>$vip['id'],
					"typeRecharger"=>"pointGive",
					"sum"=>$activity['point'],
					"raison"=>"活动签到送分",
					"time"=>date("Y-m-d H:i:s"),
					"operator"=>$operator['id'],
			);
			D('Recharger')->add($data);
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"活动签到",
					"content"=>"会员卡号:".$vip['cardnumber']." 活动:".$activity['name'],
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			apiReturn("ok");
		}else{
			$clientActivityModel->where("id='".$record."'")->delete();
			apiReturn("fail");
		}
	}
	public function retirer(){
		if(!IS_POST){
			apiReturn("false");
			return;
		}
		$cardNumber = I("post.cardNumber");
		$sum = I("post.sum");
		$pwd = I('post.pwd');
		if(!is_numeric($sum)){
			apiReturn("sum must be numeric");
			return;
		}
		if($sum <= 0){
			apiReturn("less than 0");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($cardNumber);
		if(!$vip){
			apiReturn("no card");
			return;
		}
		if($vip['card']['name'] != "生活卡"){
			apiReturn("card not ok");
			return;
		}
		if(chiffrement($pwd) != $vip['pwdpay']){
			apiReturn("pwd not match");
			return;
		}
		/*
		if($vip['pointrecharger']+intval($vip['pointgive']/1.2)+$vip['pointconso']-$sum < 500){
			apiReturn("point not enough");
			return;
		}
		*/
		if($vip['pointrecharger'] > $sum && $vip['pointrecharger']+$vip['pointgive']+$vip['pointconso']-$sum < 500){
			apiReturn("point not enough");
			return;
		}elseif($vip['pointgive']-$sum*1.2 && $vip['pointrecharger']+$vip['pointgive']+$vip['pointconso']-$sum*1.2 < 500){
			apiReturn("point not enough");
			return;
		}
		
		
		$operator = session("user");
		if($vip['pointrecharger']>$sum*1.05){
			$data = array(
					"pointRecharger"=>$vip['pointrecharger']-$sum*1.05,
			);
			$dataRecord = array(
					"vip"=>$vip['id'],
					"time"=>date("Y-m-d H:i:s"),
					"sum"=>$sum,
					"typeRetirer"=>"pointRecharger",
					"operator"=>$operator['id'],
			);
			$type = "pointRecharger";
		}elseif($vip['pointgive']>$sum*1.2){
			$data = array(
					"pointGive"=>$vip['pointgive']-$sum*1.2,
			);
			$dataRecord = array(
					"vip"=>$vip['id'],
					"time"=>date("Y-m-d H:i:s"),
					"sum"=>$sum,
					"typeRetirer"=>"pointGive",
					"operator"=>$operator['id'],
			);
			$type = "pointGive";
		}else{
			apiReturn("point not enough");
			return;
		}
		$result = $vipModel->where(array("id"=>$vip['id']))->save($data);
		if(is_numeric($result)){
			$retirerModel = M('retirer');
			$retirerModel->add($dataRecord);
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"取现",
					"content"=>"会员卡号:".$vip['cardnumber']." 金额:".$sum." 类型:".$type,
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			apiReturn("ok");
		}else{
			apiReturn("fail");
		}
	}
	public function getVipsByIdentity(){
		if(!IS_POST){
			apiReturn("false1");
			return;
		}
		$identity = I('post.identityUser');
		$clientModel = D('Client');
		$client = $clientModel->where(array("identity"=>$identity))->find();
		if(!$client){
			apiReturn("false2");
			return;
		}
		$vipModel = D('Vip');
		$vips = $vipModel->where(array("client"=>$client['id']))->relation(true)->select();
		if(count($vips)){
			$result = array(
					"status"=>"ok",
					"list"=>$vips
			);
			apiReturn($result);
		}else{
			apiReturn("false3");
		}
	}
	public function getVipByCardNumber(){
		$cardNumber = I('post.cardNumber');
		if(!$cardNumber){
			$str = '{"status": "error"}';
			echo $str;
			return;
		}
		$vipModel = D('Vip');
		$card = $vipModel->get($cardNumber);
		if(!$card){
			$str = '{"status": "error"}';
			echo $str;
			return;
		}
		$pointConso = $card['pointconso']+$card['pointgive']+$card['pointrecharger'];
		$str = '{"status":"ok","card":"'.$card['card']['name'].'","credit":"'.$card['credit'].'","creditGive":"'.$card['creditgive'].'","pointConso":"'.$pointConso.'","pointHeal":"'.$card['pointheal'].'","pointTourism":"'.$card['pointtourism'].'","dateCard":"'.$card['datecard'].'","dateActive":"'.$card['dateactive'].'","dateExpire":"'.$card['dateexpire'].'"}';
		$result = array(
				"status"=>"ok",
				"card"=>$card['card']['name'],
				"credit"=>$card['credit'],
				"creditGive"=>$card['creditgive'],
				"pointConso"=>$pointConso,
				"pointHeal"=>$card['pointheal'],
				"pointTourism"=>$card['pointtourism'],
				"dateCard"=>$card['datecard'],
				"dateActive"=>$card['dateactive'],
				"dateExpire"=>$card['dateexpire'],
				"recharger"=>$card['recharger'],
				"order"=>$card['order'],
		);
		apiReturn($result);
	}
}