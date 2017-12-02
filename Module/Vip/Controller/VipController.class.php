<?php

namespace Vip\Controller;

use Vip\Common\Controller\Base;
use Common\Model\VipModel;

class VipController extends Base{
	public function addVip(){
		if(!testClassified($this->user,"addVip")){
			$this->error("权限不足");
			return;
		}
		$cardModel = M('card');
		if(!IS_POST){
			$cardLevel = $cardModel->select();
			$this->assign("card",$cardLevel);
			$this->display();
			return;
		}
		$clientModel = D('Client');
		$data['client'] = I('post.client');
		$client = $clientModel->get($data['client']);
		if(!$client){
			$this->error("顾客信息输入错误");
			return;
		}
		$data['cardNumber'] = I('post.cardNumber');
		$vipModel = D('Vip');
		$result = $vipModel->get($data['cardNumber']);
		if($result){
			$this->error("已存在的会员卡号码");
			return;
		}
		$data['card'] = I('post.card');
		$where = array(
				"id" => $data['card']
		);
		$cardLevel = $cardModel->where($where)->find();
		if(!$cardLevel){
			$this->error("会员卡类型错误");
			return;
		}
		$data['pwdPay'] = I('post.pwdPay');
		if(!$data['pwdPay']||strlen($data['pwdPay'])!=6){
			$this->error("支付密码未设置或密码位数不足");
			return;
		}
		$data['pwdPay'] = chiffrement($data['pwdPay']);
		$data['etc'] = I('post.etc');
		// 第一步，记录卡
		$data['credit'] = $cardLevel['credit'];
		$data['creditGive'] = $cardLevel['creditgive'];
		$data['pointRecharger'] = $cardLevel['pointrecharger'];
		$data['pointGive'] = $cardLevel['pointgive'];
		$data['pointConso'] = $cardLevel['pointconso'];
		$data['pointHeal'] = $cardLevel['pointheal'];
		$data['pointTourism'] = $cardLevel['pointtourism'];
		$data['dateCard'] = date('Y-m-d H:i:s');
		$result = $vipModel->add($data);
		if(!$result){
			$this->error("失败2");
			return;
		}
		$idVip = $result;
		$vip = $vipModel->where(array("id"=>$result))->find();
		$dataRecharger = array();
		$array = array(
				"vip"=>$vip['id'],
				"sum"=>$vip['credit'],
				"newSum"=>$vip['credit'],
				"typeRecharger"=>"credit",
				"raison"=>"开卡",
				"time"=>date("Y-m-d H:i:s"),
				"operator"=>$this->user['id'],
		);
		array_push($dataRecharger,$array);
		$array = array(
				"vip"=>$vip['id'],
				"sum"=>$vip['creditgive'],
				"newSum"=>$vip['creditgive'],
				"typeRecharger"=>"creditGive",
				"raison"=>"开卡",
				"time"=>date("Y-m-d H:i:s"),
				"operator"=>$this->user['id'],
		);
		array_push($dataRecharger,$array);
		$array = array(
				"vip"=>$vip['id'],
				"sum"=>$vip['pointrecharger'],
				"newSum"=>$vip['pointrecharger'],
				"typeRecharger"=>"pointRecharger",
				"raison"=>"开卡",
				"time"=>date("Y-m-d H:i:s"),
				"operator"=>$this->user['id'],
		);
		array_push($dataRecharger,$array);
		$array = array(
				"vip"=>$vip['id'],
				"sum"=>$vip['pointGive'],
				"newSum"=>$vip['pointgive'],
				"typeRecharger"=>"pointgive",
				"raison"=>"开卡",
				"time"=>date("Y-m-d H:i:s"),
				"operator"=>$this->user['id'],
		);
		array_push($dataRecharger,$array);
		$array = array(
				"vip"=>$vip['id'],
				"sum"=>$vip['pointConso'],
				"newSum"=>$vip['pointconso'],
				"typeRecharger"=>"pointconso",
				"raison"=>"开卡",
				"time"=>date("Y-m-d H:i:s"),
				"operator"=>$this->user['id'],
		);
		array_push($dataRecharger,$array);
		$array = array(
				"vip"=>$vip['id'],
				"sum"=>$vip['pointTourism'],
				"newSum"=>$vip['pointtourism'],
				"typeRecharger"=>"pointtourism",
				"raison"=>"开卡",
				"time"=>date("Y-m-d H:i:s"),
				"operator"=>$this->user['id'],
		);
		array_push($dataRecharger,$array);
		$array = array(
				"vip"=>$vip['id'],
				"sum"=>$vip['pointheal'],
				"newSum"=>$vip['pointheal'],
				"typeRecharger"=>"pointHeal",
				"raison"=>"开卡",
				"time"=>date("Y-m-d H:i:s"),
				"operator"=>$this->user['id'],
		);
		array_push($dataRecharger,$array);
		M('recharger')->addAll($dataRecharger);
		// 第二部 自由卡加政策
		$policyModel = M('libertyCardPolicy');
		$policy = $policyModel->where("card='".$cardLevel['id']."'")->find();
		if($policy){
			$dataPolicy = array(
					"vip" => $result,
					"pointGive" => $policy['pointgive'],
					"timePointGive" => $policy['timepointgive'],
					"lifeShipping" => $policy['lifeshipping'],
					"timeLifeShipping" => $policy['timelifeshipping']
			);
			if($dataPolicy['timePointGive']==1){
				$dataPolicy['timePointGive'] = 0;
				$dataPolicy['pointGive'] = 0;
			}
			$policyRecordModel = M('libertyPolicyRecord');
			$resultPolicy = $policyRecordModel->add($dataPolicy);
			if($resultPolicy){
				/*
				 * $data = array(
				 * "vip"=>$result,
				 * "price"=>$policy['lifeshipping'],
				 * "fois"=>$policy['timelifeshipping']
				 * );
				 * M('lifeshipping')->add($data);
				 */
				$operator = session("user");
				$data = array(
						"operator" => $operator['id'],
						"operation" => "会员开卡",
						"content" => "卡号:".I('post.cardNumber'),
						"time" => date("Y-m-d H:i:s"),
						"ip" => get_client_ip()
				);
				M('operationRecord')->add($data);
				$this->success("成功");
			}else{
				$this->error("失败1");
				return;
			}
		}else{
			$operator = session("user");
			$data = array(
					"operator" => $operator['id'],
					"operation" => "会员开卡",
					"content" => "卡号:".I('post.cardNumber'),
					"time" => date("Y-m-d H:i:s"),
					"ip" => get_client_ip()
			);
			M('operationRecord')->add($data);
			$this->success("成功");
		}
		$where = array(
				"client"=>$client['id'],
				"card"=>6
		);
		$data = array(
				"active"=>0
		);
		$vipModel->where($where)->save($data);
		//积分成员邀请积分成员+5分
		if($vip['card'] == 6){
			$client = D('Client')->get($vip['client']['id']);
			if($client['beinvited']){
				$vipsInvited = $vipModel->where(array("client"=>$client['beinvited']))->select();
				if(count($vipsInvited) == 1 && $vipsInvited[0]['card']['id'] == 6){
					$data = array(
							"pointGive"=>$vipsInvited[0]['pointGive']+5
					);
					$vipModel->where(array("client"=>$client['beinvited']))->save($data);
				}
			}
		}
		//积分成员卡升级转存
		$cardNumberOld = I('post.cardNumberOld');
		if(!$cardNumberOld){
			return;
		}
		$vipOld = $vipModel->where(["cardNumber"=>$cardNumberOld])->find();
		if(!$vipOld){
			return;	
		}
		$sum = intval($vipOld['pointgive']/2);
		if($sum > 200){
			$sum = 200;
		}
		$vipModel->where(["id"=>$idVip])->setInc("pointGive",$vipOld['pointgive']-$sum);
		$vipModel->where(["id"=>$idVip])->setInc("pointExchange",$vipOld["pointexchange"]);
		$vipModel->where(["id"=>$idVip])->setInc("pointValue",$vipOld["pointvalue"]);
		$dataVipOld = [
				"pointGive"=>0,
				"pointExchange"=>0,
				"pointValue"=>0,
				"active"=>0,
				"dateExpire"=>date("Y-m-d"),
				"etc"=>"新卡抵扣。新卡卡号".I('post.cardNumber')."，抵扣金额".$sum
		];
		$vipModel->where(["cardNumber"=>$cardNumberOld])->save($dataVipOld);
		
	}
	public function changeVip($id = null){
		if(!testClassified($this->user,"changeVip")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($id);
		if(!$vip){
			$this->error("非法访问");
			return;
		}
		if(!IS_POST){
			$this->assign("vip",$vip);
			$this->display();
			return;
		}
		$data = I('post.');
		if(!$data['cardNumber']){
			$this->error("会员卡号码不能为空");
			return;
		}
		if($vipModel->where(array("id"=>$vip['id']))->save($data)){
			$this->success("成功");
		}else{
			$this->error("失败");
		}
	}
	public function deleteVip($id = null){
		if(!testClassified($this->user,"deleteVip")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($id);
		if(!$vip){
			$this->error("非法访问");
			return;
		}
		if(!IS_POST){
			$this->assign("vip",$vip);
			$this->display();
			return;
		}
		$where = array(
				"id" => $vip['id']
		);
		//$result = $vipModel->where($where)->delete();
		$data = array(
				"credit"=>0,
				"creditGive"=>0,
				"pointRecharger"=>0,
				"pointGive"=>0,
				"pointConso"=>0,
				"pointTourism"=>0,
				"pointHeal"=>0,
				"dateExpire"=>date("Y-m-d"),
				"active"=>0
		);
		$dataRecharger = array(
				array(
						"vip"=>$vip['id'],
						"sum"=>0-$vip['credit'],
						"typeRecharger"=>"credit",
						"raison"=>"退卡",
						"time"=>date("Y-m-d H:i:s"),
						"operator"=>$this->user['id'],
				),
				array(
						"vip"=>$vip['id'],
						"sum"=>0-$vip['creditgive'],
						"typeRecharger"=>"creditGive",
						"raison"=>"退卡",
						"time"=>date("Y-m-d H:i:s"),
						"operator"=>$this->user['id'],
				),
				array(
						"vip"=>$vip['id'],
						"sum"=>0-$vip['pointrecharger'],
						"typeRecharger"=>"pointRecharger",
						"raison"=>"退卡",
						"time"=>date("Y-m-d H:i:s"),
						"operator"=>$this->user['id'],
				),
				array(
						"vip"=>$vip['id'],
						"sum"=>0-$vip['pointgive'],
						"typeRecharger"=>"pointGive",
						"raison"=>"退卡",
						"time"=>date("Y-m-d H:i:s"),
						"operator"=>$this->user['id'],
				),
				array(
						"vip"=>$vip['id'],
						"sum"=>0-$vip['pointconso'],
						"typeRecharger"=>"pointConso",
						"raison"=>"退卡",
						"time"=>date("Y-m-d H:i:s"),
						"operator"=>$this->user['id'],
				),
				array(
						"vip"=>$vip['id'],
						"sum"=>0-$vip['pointtourism'],
						"typeRecharger"=>"pointTourism",
						"raison"=>"退卡",
						"time"=>date("Y-m-d H:i:s"),
						"operator"=>$this->user['id'],
				),
				array(
						"vip"=>$vip['id'],
						"sum"=>0-$vip['pointheal'],
						"typeRecharger"=>"pointHeal",
						"raison"=>"退卡",
						"time"=>date("Y-m-d H:i:s"),
						"operator"=>$this->user['id'],
				),
		);
		$result = $vipModel->where($where)->save($data);
		if($result){
			M('recharger')->addAll($dataRecharger);
			$operator = session("user");
			$data = array(
					"operator" => $operator['id'],
					"operation" => "注销会员卡",
					"content" => "卡号:".$vip['cardnumber'],
					"time" => date("Y-m-d H:i:s"),
					"ip" => get_client_ip()
			);
			M('operationRecord')->add($data);
			$this->success("成功",U('/vip/statistic/listVip'));
		}else{
			$this->error("失败");
		}
	}
	public function detailVip($id = null){
		if(!testClassified($this->user,"detailVip")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($id);
		if(!$vip){
			$this->error("非法访问");
			return;
		}
		if($vip['card']['name']=="自由卡"){
			$model = M('libertyPolicyRecord');
			$vip['policy'] = $model->where(array(
					"vip" => $vip['id']
			))->select();
		}
		$this->assign("vip",$vip);
		$this->display();
	}
	public function activeVip($id = null){
		if(!testClassified($this->user,"activeVip")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($id);
		if(!$vip){
			$this->error("非法访问");
			return;
		}
		if($vip['active']){
			$this->error("卡已激活");
			return;
		}
		$where = array(
				"card" => $vip['card'],
				"client" => $vip['client'],
				"active" => 1
		);
		$result = $vipModel->where($where)->select();
		if(count($result)){
			$this->error("已有激活中的同类型卡");
			return;
		}
		if(!IS_POST){
			$this->assign("vip",$vip);
			$this->display();
			return;
		}
		$data['active'] = 1;
		$data['dateActive'] = date("Y-m-d H:i:s");
		$data['dateExpire'] = I('post.dateExpire');
		if(!I('post.dateExpire')){
			unset($data['dateExpire']);
		}
		$where = array(
				"id" => $id
		);
		$result = $vipModel->where($where)->save($data);
		if(is_numeric($result)){
			//生活卡激活送10%
			if($vip['card']['id'] == 1){
				echo 1;
				$price = I('post.price');
				if($price){
					$pointGive = intval($price*0.1);
					$data = array(
							"pointGive"=>$vip['pointGive']+$pointGive
					);
					$vipModel->where(array("id"=>$vip['id']))->save($data);
					$data = array(
							"vip"=>$vip['id'],
							"sum"=>$pointGive,
							"typeRecharger"=>"pointGive",
							"newSum"=>$vip['pointGive']+$pointGive,
							"raison"=>"生活卡激活赠送10%",
							"time"=>date("Y-m-d H:i:s"),
							"operator"=>$this->user['id']
					);
					M('recharger')->add($data);
				}
			}
			$operator = session("user");
			$data = array(
					"operator" => $operator['id'],
					"operation" => "激活会员卡",
					"content" => "卡号:".$vip['cardnumber'],
					"time" => date("Y-m-d H:i:s"),
					"ip" => get_client_ip()
			);
			M('operationRecord')->add($data);
			$this->success("成功",U('/vip/vip/detailVip/id/'.$id));
		}else{
			$this->error("失败");
		}
	}
	public function transferVip($id = null){
		if(!testClassified($this->user,"transferVip")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($id);
		if(!$vip){
			$this->error("非法访问");
			return;
		}
		if(!IS_POST){
			$this->assign("vip",$vip);
			$this->display();
			return;
		}
		$data['client'] = I('post.client');
		$clientModel = D('Client');
		$client = $clientModel->get($data['client']);
		if(!$client){
			$this->error("转入用户暑促错误");
			return;
		}
		$data['client'] = $client['id'];
		$where = array(
				"id" => $vip['id']
		);
		$result = $vipModel->where($where)->save($data);
		if(is_numeric($result)){
			$operator = session("user");
			$data = array(
					"operator" => $operator['id'],
					"operation" => "会员卡转让",
					"content" => "卡号:".$vip['cardnumber']." 原所属人:".$client['name']." 现所属人:".$vip['client']['name'],
					"time" => date("Y-m-d H:i:s"),
					"ip" => get_client_ip()
			);
			M('operationRecord')->add($data);
			$this->success("成功");
		}else{
			$this->error("失败");
		}
	}
	public function changePwdVip($id = null){
		if(!testClassified($this->user,"changePwdVip")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($id);
		if(!$vip){
			$this->error("非法访问");
			return;
		}
		if(!IS_POST){
			$this->assign("vip",$vip);
			$this->display();
			return;
		}
		$pwdOld = I('post.pwdOld');
		$pwd = I('post.pwd');
		$pwdConfirm = I('post.pwdConfirm');
		if(chiffrement($pwdOld)!=$vip['pwdpay']){
			$this->error("原始密码输入错误");
			return;
		}
		if(!$pwd||$pwdConfirm!=$pwd){
			$this->error("请输入新密码或两次新密码的输入不一致");
			return;
		}
		$data['pwdPay'] = chiffrement($pwd);
		$where = array(
				"id" => $vip['id']
		);
		$result = $vipModel->where($where)->save($data);
		if(is_numeric($result)){
			$operator = session("user");
			$data = array(
					"operator" => $operator['id'],
					"operation" => "会员卡密码修改",
					"content" => "卡号:".$vip['cardnumber'],
					"time" => date("Y-m-d H:i:s"),
					"ip" => get_client_ip()
			);
			M('operationRecord')->add($data);
			$this->success("成功",U('/vip/vip/detailVip/id/'.$vip['id']));
		}else{
			$this->error("失败");
		}
	}
	public function resetPwdVip($id = null){
		if(!testClassified($this->user,"resetPwdVip")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($id);
		if(!$vip){
			$this->error("非法访问");
			return;
		}
		if(!IS_POST){
			$this->display();
			return;
		}
		$data['pwdPay'] = md5("888888".C('CHIFFREMENT_CODE'));
		$where = array(
				"id" => $id
		);
		$result = $vipModel->where($where)->save($data);
		if(is_numeric($result)){
			$operator = session("user");
			$data = array(
					"operator" => $operator['id'],
					"operation" => "会员卡密码重置",
					"content" => "卡号:".$vip['cardnumber'],
					"time" => date("Y-m-d H:i:s"),
					"ip" => get_client_ip()
			);
			M('operationRecord')->add($data);
			$this->success("成功",U('/vip/vip/detailVip/id/'.$vip['id']));
		}else{
			$this->error("失败");
		}
	}
	public function presentDaily(){
		if(!testClassified($this->user,"presentDaily")){
			$this->error("权限不足");
			return;
		}
		$this->display();
	}
	public function listActivity(){
		if(!testClassified($this->user,"listActivity")){
			$this->error("权限不足");
			return;
		}
		$activityModel = D('Activity');
		$list = $activityModel->select();
		$this->assign("list",$list);
		$this->display();
	}
	public function presentActivity(){
		if(!testClassified($this->user,"presentActivity")){
			$this->error("权限不足");
			return;
		}
		$activityModel = D('Activity');
		$where = array(
				"time" => array(
						"ELT",
						date("Y-m-d H:i:s")
				)
		);
		$where['_string'] = "( timeEnd is null ) or ( timeEnd>'".date("Y-m-d H:i:s")."' )";
		// $list = $activityModel->relation(true)->where("time like '%".date("Y-m-d")."%'")->select();
		$list = $activityModel->where($where)->select();
		$this->assign("list",$list);
		$this->display();
	}
	public function listPresentActivity(){
		if(!testClassified($this->user,"listPresentActivity")){
			$this->error("权限不足");
			return;
		}
		$clientActivityModel = D('clientActivity');
		$list = $clientActivityModel->relation(true)->select();
		$this->assign("list",$list);
		$this->display();
	}
	public function desactiveVip($id = null){
		if(!testClassified($this->user,"desactiveVip")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($id);
		if(!$vip){
			$this->error("非法访问");
			return;
		}
		if(!$vip['active']){
			$this->error("此卡未激活");
			return;
		}
		if(!IS_POST){
			$this->assign("vip",$vip);
			$this->display();
			return;
		}
		$data = array(
				"active" => 0
		);
		$where = array(
				"id" => $vip['id']
		);
		$result = $vipModel->where($where)->save($data);
		if(is_numeric($result)){
			$operator = session("user");
			$data = array(
					"operator" => $operator['id'],
					"operation" => "会员卡反激活",
					"content" => "卡号:".$vip['cardnumber'],
					"time" => date("Y-m-d H:i:s"),
					"ip" => get_client_ip()
			);
			M('operationRecord')->add($data);
			$this->success("成功");
		}else{
			$this->error("失败");
		}
	}
	public function signalerLost($id = null){
		if(!testClassified($this->user,"signalerLost")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($id);
		if(!$vip){
			$this->error("非法访问");
			return;
		}
		if(!IS_POST){
			$this->assign("vip",$vip);
			$this->display();
			return;
		}
		$data = array(
				"active" => 0
		);
		$where = array(
				"id" => $vip['id']
		);
		$result = $vipModel->where($where)->save($data);
		if(is_numeric($result)){
			$operator = session("user");
			$data = array(
					"operator" => $operator['id'],
					"operation" => "会员卡挂失",
					"content" => "卡号:".$vip['cardnumber'],
					"time" => date("Y-m-d H:i:s"),
					"ip" => get_client_ip()
			);
			M('operationRecord')->add($data);
			$this->success("成功");
		}else{
			$this->error("失败");
		}
	}
	public function recharger(){
		if(!testClassified($this->user,"recharger")){
			$this->error("权限不足");
			return;
		}
		$this->display();
	}
	public function retirer(){
		if(!testClassified($this->user,"retirer")){
			$this->error("权限不足");
			return;
		}
		$this->display();
	}
	public function listPresent(){
		if(!testClassified($this->user,"listPresent")){
			$this->error("权限不足");
			return;
		}
		$presentModel = D('ClientPresent');
		if(!IS_POST){
			$where = array(
					"timeDebut"=>array(
							"like",
							"%".date("Y-m-d")."%"
					)
			);
			$list = $presentModel->where($where)->relation(true)->order("timeDebut desc")->select();
			$count = count($list);
		}else{
			$where = array();
			$cardNumber = I('post.cardNumber');
			$client = I('post.client');
			$timeBegin = I('post.timeBegin');
			$timeEnd = I('post.timeEnd');
			if($cardNumber){
				$vipModel = D('Vip');
				$vip = $vipModel->get($cardNumber);
				if($vip){
					$where['client'] = 0;
				}else{
					$where['client'] = $vip['client']['id'];
				}
			}elseif($client){
				$clientModel = D('Client');
				$client = $clientModel->get($client);
				if($client){
					$where['client'] = $client['id'];
				}else{
					$where['client'] = 0;
				}
			}
			if($timeBegin){
				$where['timeDebut'] = array(
						"EGT",
						$timeBegin
				);
			}
			if($timeEnd){
				$where['timeFin'] = array(
						"ELT",
						$timeEnd." 23:59:59"
				);
			}
			$list = $presentModel->where($where)->relation(true)->order("timeDebut desc")->select();
			$count = count($list);
		}
		$this->assign("list",$list);
		$this->assign("count",$count);
		$this->display();
	}
	public function vipPointTransfert($id = null){
		if(!testClassified($this->user,"vipPointTransfert")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($id);
		if(!$vip){
			$this->error("非法访问");
			return;
		}
		if($vip['card']['name']!="自由卡"){
			$this->error("只有自由卡可以进行积分互转");
			return;
		}
		if(!IS_POST){
			$this->assign("vip",$vip);
			$this->display();
			return;
		}
		$from = I('post.from');
		$to = I('post.to');
		$sum = I('post.sum');
		switch($from){
			case "pointConso":
				if(!$this->pointconsoTo($vip,$sum,$to)){
					$this->error("失败");
					return;
				}
				break;
			case "pointTourism":
				if(!$this->pointtourismTo($vip,$sum,$to)){
					$this->error("失败");
					return;
				}
				break;
			default:
				dump(I('post.'));
				return;
		}
		$this->success("成功");
	}
	private function pointtourismTo($vip = null,$sum = null,$to = null){
		if(!is_numeric($sum)||$to==null||$vip==null){
			return false;
		}
		$vipModel = D('Vip');
		$rechargerModel = D('Recharger');
		switch($to){
			case "pointHeal":
				$data = array(
						"pointTourism" => $vip['pointtourism']-$sum,
						"pointHeal" => intval($vip['pointheal']+$sum)
				);
				if($data['pointTourism']<0){
					return false;
				}
				$result = $vipModel->where(array(
						"id" => $vip['id']
				))->save($data);
				if(is_numeric($result)){
					$data = array(
							"vip" => $vip['id'],
							"sum" => -1*$sum,
							"typeRecharger" => "pointTourism",
							"raison" => "积分转换",
							"time" => date("Y-m-d H:i:s"),
							"operator" => $this->user['id']
					);
					$rechargerModel->add($data);
					$data = array(
							"vip" => $vip['id'],
							"sum" => intval($sum),
							"typeRecharger" => "pointHeal",
							"raison" => "积分转换",
							"time" => date("Y-m-d H:i:s"),
							"operator" => $this->user['id']
					);
					$rechargerModel->add($data);
					return true;
				}else{
					return false;
				}
				break;
			default:
				return false;
		}
	}
	private function pointconsoTo($vip = null,$sum = null,$to = null){
		if(!is_numeric($sum)||$to==null||$vip==null){
			return false;
		}
		$vipModel = D('Vip');
		$rechargerModel = D('Recharger');
		switch($to){
			case "pointHeal":
				$data = array(
						"pointGive" => $vip['pointgive']-$sum,
						"pointHeal" => intval($vip['pointheal']+$sum*1.2)
				);
				if($data['pointGive']<0){
					return false;
				}
				$result = $vipModel->where(array(
						"id" => $vip['id']
				))->save($data);
				if(is_numeric($result)){
					$data = array(
							"vip" => $vip['id'],
							"sum" => -1*$sum,
							"typeRecharger" => "pointGive",
							"raison" => "积分转换",
							"time" => date("Y-m-d H:i:s"),
							"operator" => $this->user['id']
					);
					$rechargerModel->add($data);
					$data = array(
							"vip" => $vip['id'],
							"sum" => intval($sum*1.2),
							"typeRecharger" => "pointHeal",
							"raison" => "积分转换",
							"time" => date("Y-m-d H:i:s"),
							"operator" => $this->user['id']
					);
					$rechargerModel->add($data);
					return true;
				}else{
					return false;
				}
				break;
			case "pointTourism":
				$data = array(
						"pointGive" => $vip['pointgive']-$sum,
						"pointTourism" => intval($vip['pointtourism']+$sum)
				);
				if($data['pointGive']<0){
					return false;
				}
				$result = $vipModel->where(array(
						"id" => $vip['id']
				))->save($data);
				if(is_numeric($result)){
					$data = array(
							"vip" => $vip['id'],
							"sum" => -1*$sum,
							"typeRecharger" => "pointGive",
							"raison" => "积分转换",
							"time" => date("Y-m-d H:i:s"),
							"operator" => $this->user['id']
					);
					$rechargerModel->add($data);
					$data = array(
							"vip" => $vip['id'],
							"sum" => $sum,
							"typeRecharger" => "pointTourism",
							"raison" => "积分转换",
							"time" => date("Y-m-d H:i:s"),
							"operator" => $this->user['id']
					);
					$rechargerModel->add($data);
					return true;
				}else{
					return false;
				}
				break;
			case "cardLife":
				
				//自由卡积分转入生活卡
				$where = array(
						"client"=>$vip['client']['id'],
						"card"=>1,
						"active"=>"1"
				);
				$cardLife = $vipModel->where($where)->find();
				if(!$cardLife){
					return false;
				}
				$data = array(
						"pointGive"=>$vip['pointgive']-$sum,
				);
				if($data['pointGive']<0){
					return false;
				}
				$dataLife = array(
						"pointRecharger"=>$cardLife['pointrecharger']+$sum
				);
				$result1 = $vipModel->where(array("id"=>$vip['id']))->save($data);
				if($result1){
					$result2 = $vipModel->where(array("id"=>$cardLife['id']))->save($dataLife);
					if($result2){
						return true;
					}else{
						$data = array(
								"pointGive"=>$vip['pointgive']
						);
						$vipModel->where(array("id"=>$vip['id']))->save($data);
						return false;
					}
				}else{
					return false;
				}
				break;
			default:
				return false;
		}
	}
	public function changeCardNumber($id = null){
		
	}
}