<?php

namespace Api\Controller;

use Think\Controller;

class StatisticController extends Controller{
	public function statisticRecharger(){
		if(!IS_POST){
			apiReturn("false");
			return;
		}
		$data = I('post.');
		$where = array(
				"time"=>array(),
				"sum"=>array(),
		);
		$vipModel = D('Vip');
		$clientModel = D('Client');
		if($data['cardNumber']){
			$vip = $vipModel->get($data['cardNumber']);
			if(!$vip){
				$where['vip'] = 0;
			}else{
				$where['vip'] = $vip['id'];
			}
		}elseif($data['client']){
			$client = $clientModel->get($data['client']);
			if(!$client){
				$where['vip'] = 0;
			}else{
				$vips = $vipModel->where(array("client"=>$client['id']))->select();
				$temp = array();
				if(count($vips)){
					foreach($vips as $v){
						array_push($temp,$v['id']);
					}
					$where['vip'] = array(
							"in",$temp
					);
				}else{
					$where['vip'] = 0;
				}
			}
		}
		if($data['timeBegin']){
			$temp = array(
					"EGT",
					$data['timeBegin']
			);
			array_push($where['time'],$temp);
		}
		if($data['timeEnd']){
			$date = explode("-",$data['timeEnd']);
    		$date[2]++;
    		$temp = array(
    				"ELT",
    				$date[0]."-".$date[1]."-".$date[2],
    		);
    		array_push($where['time'],$temp);
		}
		if($data['timeBegin'] && $data['timeEnd'] && $data['timeBegin'] == $data['timeEnd']){
			$where['time'] = array(
					"like",
					"%".$data['timeBegin']."%"
			);
		}
		if($data['sumMin']){
			$temp = array(
					"EGT",
					$data['sumMin']
			);
			array_push($where['sum'],$temp);
		}
		if($data['sumMax']){
			$temp = array(
					"ELT",
					$data['sumMax']
			);
			array_push($where['sum'],$temp);
		}
		if($data['typeRecharger']){
			$where['typeRecharger'] = $data['typeRecharger'];
		}
		if($data['operator']){
			$userModel = D('User');
			$user = $userModel->get($data['operator']);
			if(!$user){
				$where['operator'] = 0;
			}else{
				$where['operator'] = $user['id'];
			}
		}
		$temp = $where;
		foreach($temp as $key=>$value){
			if(!$value || !count($value)){
				unset($where[$key]);
			}
		}
		$data['page'] = (!$data['page'] || !is_numeric($data['page']))?1:$data['page'];
		$rechargerModel = D('Recharger');
		$list = $rechargerModel->where($where)->relation(true)->page($data['page'],30)->order("id desc")->select();
		$result['sql'] = $rechargerModel->getLastSql();
		$listAll = $rechargerModel->where($where)->relation(true)->select();
		$result['status'] = "ok";
		$result['list'] = $list;
		$result['total'] = 0;
		$result['count'] = count($listAll);
		$allPage = $result['count']/30.0;
		$result['allPage'] = (intval($allPage) != $allPage)?intval($allPage)+1:$allPage;
		foreach($listAll as $l){
			$result['total'] += $l['sum'];
		}
		apiReturn($result);
	}
	public function statisticRetirer(){
		if(!IS_POST){
			apiReturn("false1");
			return;
		}
		$where = array(
				"sum"=>array(),
				"time"=>array(),
		);
		$page = I('post.page');
		if(!$page||!is_numeric($page)){
			$page = 1;
		}
		$data = I('post.');
		if($data['sumMin']){
			$temp = array(
					"EGT",
					$data['sumMin']
			);
			array_push($where['sum'],$temp);
		}
		if($data['sumMax']){
			$temp = array(
					"ELT",
					$data['sumMax']
			);
			array_push($where['sum'],$temp);
		}
		if($data['timeBegin']){
			$temp = array(
					"EGT",
					$data['timeBegin'],
			);
			array_push($where['time'],$temp);
		}
		if($data['timeEnd']){
			$date = explode("-",$data['timeEnd']);
    		$date[2]++;
    		$temp = array(
    				"ELT",
    				$date[0]."-".$date[1]."-".$date[2],
    		);
    		array_push($where['time'],$temp);
		}
		if($data['timeBegin'] == $data['timeEnd']){
			$where['time'] = array(
					"like",
					"%".$timeBegin."%"
			);
		}
		if($data['operator']){
			$userModel = D('User');
			$operator = $userModel->get($data['operator']);
			if($operator){
				$where['operator'] = $operator['id'];
			}
		}
		
		
		
		
		if($data['cardNumber']){
			$vipModel = D('Vip');
			$vip = $vipModel->get($data['cardNumber']);
			if($vip){
				$where['vip'] = $vip['id'];
			}else{
				$where['vip'] = -1;
			}
		}elseif($data['client']){
			$clientModel = D('Client');
			$client = $clientModel->get($data['client']);
			if($client){
				$vipModel = D('Vip');
				$vip = $vipModel->where(array(
						"client" => $client['id'],
						"active" => 1
				))->select();
				$temp = array(-1);
				for($i = 0;$vip[$i];$i++){
					array_push($temp,$vip['id']);
				}
			}else{
				$where['vip'] = -1;
			}
		}
		if($data['typeRetirer']){
			$where['typeRetirer'] = $data['typeRetirer'];
		}
		foreach($where as $key=>$value){
			if(!$value || !count($value)){
				unset($where[$key]);
			}
		}
		$retirerModel = D('Retirer');
		$list = $retirerModel->where($where)->relation(true)->order("id desc")->page($page,30)->select();
		$clientModel = D('Client');
		foreach($list as $key=>$value){
			$list[$key]['vip']['client'] = $clientModel->get($value['vip']['client']);
		}
		$listAll = $retirerModel->where($where)->relation(true)->select();
		$count = count($listAll);
		$allPage = $count/30.0;
		$result = array(
				"status" => "ok",
				"list" => $list
		);
		$result['allPage'] = (intval($allPage) != $allPage)?intval($allPage) + 1:$allPage;
		$result['total'] = 0;
		
		foreach($listAll as $l){
			$result['total'] += $l['sum'];
		}
		apiReturn($result);
	}
	public function statisticVip(){
		if(!IS_POST){
			apiReturn("false1");
			return;
		}
		$page = I('post.page');
		if(!$page||!is_numeric($page)){
			$page = 1;
		}
		$cardNumber = I('post.cardNumber');
		$client = I('post.client');
		$dateCardBegin = I('post.dateCardBegin');
		$dateCardEnd = I('post.dateCardEnd');
		$dateActiveBegin = I('post.dateActiveBegin');
		$dateActiveEnd = I('post.dateActiveEnd');
		$dateExpireBegin = I('post.dateExpireBegin');
		$dateExpireEnd = I('post.dateExpireEnd');
		$creditMin = I('post.creditMin');
		$creditMax = I('post.creditMax');
		$creditGiveMin = I('post.creditGiveMin');
		$creditGiveMax = I('post.creditGiveMax');
		$pointRechargerMin = I('post.pointRechargerMin');
		$pointRechargerMax = I('post.pointRechargerMax');
		$pointGiveMin = I('post.pointGiveMin');
		$pointGiveMax = I('post.pointGiveMax');
		$pointConsoMin = I('post.pointConsoMin');
		$pointConsoMax = I('post.pointConsoMax');
		$pointHealMin = I('post.pointHealMin');
		$pointHealMax = I('post.pointHealMax');
		$pointTourismMin = I('post.pointTourismMin');
		$pointTourismMax = I('post.pointTourismMax');
		$active = I('post.active');
		$card = I('post.card');
		$departement = I('post.departement');
		$serveur = I('post.serveur');
		$where = array();
		if($cardNumber){
			$where['cardNumber'] = $cardNumber;
		}elseif($client){
			$clientModel = D('Client');
			$client = $clientModel->get($client);
			if(!$client){
				apiReturn("false");
				return;
			}
			$where['client'] = $client['id'];
		}elseif($serveur){
			$serveur = D('User')->get($serveur);
			if(!$serveur){
				apiReturn("false");
				return;
			}
			$clientModel = D('Client');
			$whereClient = array(
					"serveur"=>$serveur['id']
			);
			$client = $clientModel->where($whereClient)->select();
			if(!count($client)){
				apiReturn("false");
				return;
			}
			$temp = array();
			foreach($client as $c){
				array_push($temp,$c['id']);
			}
			$where['client'] = array(
					"in",
					$temp
			);
		}elseif($departement){
			$departement = D('Departement')->get($departement);
			if(!$departement){
				apiReturn("false");
				return;
			}
			if(!count($departement['worker'])){
				apiReturn("false");
				return;
			}
			$temp = array();
			foreach($departement['worker'] as $w){
				array_push($temp,$w['id']);
			}
			$whereClient = array(
					"serveur"=>array(
							"in",
							$temp
					)
			);
			$client = D('Client')->where($whereClient)->select();
			if(!count($client)){
				apiReturn("false");
				return;
			}
			$temp = array();
			foreach($client as $c){
				array_push($temp,$c['id']);
			}
			$where['client'] = array(
					"in",
					$temp
			);
		}
		$where['dateCard'] = array();
		if($dateCardBegin){
			$temp = array(
					"EGT",
					$dateCardBegin
			);
			array_push($where['dateCard'],$temp);
		}
		if($dateCardEnd){
			$date = explode("-",$dateCardEnd);
			$date[2]++;
			$temp = array(
					"LT",
					$date[0]."-".$date[1]."-".$date[2],
			);
			array_push($where['dateCard'],$temp);
		}
		$where['dateActive'] = array();
		if($dateActiveBegin){
			$temp = array(
					"EGT",
					$dateActiveBegin
			);
			array_push($where['dateActive'],$temp);
		}
		if($dateActiveEnd){
			$date = explode("-",$dateActiveEnd);
			$date[2]++;
			$temp = array(
					"ELT",
					$date[0]."-".$date[1]."-".$date[2],
			);
			array_push($where['dateActive'],$temp);
		}
		$where['dateExpire'] = array();
		if($dateExpireBegin){
			$temp = array(
					"EGT",
					$dateExpireBegin
			);
			array_push($where['dateExpire'],$temp);
		}
		if($dateExpireEnd){
			$date = explode("-",$dateExpireEnd);
			$date[2]++;
			$temp = array(
					"ELT",
					$date[0]."-".$date[1]."-".$date[2]
			);
			array_push($where['dateExpire'],$temp);
		}
		$where['credit'] = array();
		if($creditMin){
			$temp = array(
					"EGT",
					$creditMin
			);
			array_push($where['credit'],$temp);
		}
		if($creditMax){
			$temp = array(
					"ELT",
					$creditMax,
			);
			array_push($where['credit'],$temp);
		}
		$where['creditGive'] = array();
		if($creditGiveMin){
			$temp = array(
					"EGT",
					$creditGiveMin
			);
			array_push($where['creditGive'],$temp);
		}
		if($creditGiveMax){
			$temp = array(
					"ELT",
					$creditGiveMax,
			);
			array_push($where['creditGive'],$temp);
		}
		$where['pointRecharger'] = array();
		if($pointRechargerMin){
			$temp = array(
					"EGT",
					$pointRechargerMin
			);
			array_push($where['pointRecharger'],$temp);
		}
		if($pointRechargerMax){
			$temp = array(
					"ELT",
					$pointRechargerMax,
			);
			array_push($where['pointRecharger'],$temp);
		}
		$where['pointGive'] = array();
		if($pointGiveMin){
			$temp = array(
					"EGT",
					$pointGiveMin
			);
			array_push($where['pointGive'],$temp);
		}
		if($pointGiveMax){
			$temp = array(
					"ELT",
					$pointGiveMax,
			);
			array_push($where['pointGive'],$temp);
		}
		$where['pointConso'] = array();
		if($pointConsoMin){
			$temp = array(
					"EGT",
					$pointConsoMin
			);
			array_push($where['pointConso'],$temp);
		}
		if($pointConsoMax){
			$temp = array(
					"ELT",
					$pointConsoMax,
			);
			array_push($where['pointConso'],$temp);
		}
		$where['pointHeal'] = array();
		if($pointHealMin){
			$temp = array(
					"EGT",
					$pointHealMin
			);
			array_push($where['pointHeal'],$temp);
		}
		if($pointHealMax){
			$temp = array(
					"ELT",
					$pointHealMax,
			);
			array_push($where['pointHeal'],$temp);
		}
		$where['pointTourism'] = array();
		if($pointTourismMin){
			$temp = array(
					"EGT",
					$pointTourismMin
			);
			array_push($where['pointTourism'],$temp);
		}
		if($pointTourismMax){
			$temp = array(
					"ELT",
					$pointTourismMax,
			);
			array_push($where['pointTourism'],$temp);
		}
		if(is_numeric($active)){
			$where['active'] = $active;
		}
		if($card){
			$where['card'] = $card;
		}
		foreach($where as $key=>$value){
			if($value == null || !count($value)){
				unset($where[$key]);
			}
		}
		$vipModel = D('Vip');
		
		$list = $vipModel->where($where)->relation(true)->order("id desc")->page($page,30)->select();
		$allList = $vipModel->where($where)->relation(true)->select();
		$total = $vipModel->where($where)->relation(true)->count();
		$allPage = $total/30.0;
		if(intval($allPage) != $allPage){
			$allPage = intval($allPage)+1;
		}
		$totalMoney = array(
				"credit"=>0,
				"creditGive"=>0,
				"pointRecharger"=>0,
				"pointGive"=>0,
				"pointConso"=>0,
				"pointTourism"=>0,
				"pointHeal"=>0,
		);
		for($i = 0;$allList[$i];$i++){
			$totalMoney['credit'] += $allList[$i]['credit'];
			$totalMoney['creditGive'] += $allList[$i]['creditgive'];
			$totalMoney['pointRecharger'] += $allList[$i]['pointrecharger'];
			$totalMoney['pointGive'] += $allList[$i]['pointgive'];
			$totalMoney['pointConso'] += $allList[$i]['pointconso'];
			$totalMoney['pointTourism'] += $allList[$i]['pointtourism'];
			$totalMoney['pointHeal'] += $allList[$i]['pointheal'];
		}
		$result = array(
				"status" => "ok",
				"list" => $list,
				"allPage"=>$allPage,
				"total"=>$total,
				"totalMoney"=>$totalMoney,
		);
		apiReturn($result);
	}
	public function statisticClient(){
		if(!IS_POST){
			apiReturn("false1");
			return;
		}
		
		
		$clientZone = array();
		$tallMin = I('post.tallMin');
		$tallMax = I('post.tallMax');
		$amountPerMonthMin = I('post.amoutnPerMonthMin');
		$amountPerMonthMax = I('post.amoutnPerMonthMax');
		$sonMin = I('post.sonMin');
		$sonMax = I('post.sonMax');
		$daughterMin = I('post.daughterMin');
		$daughterMax = I('post.daughterMax');
		
		$whereProfile = array(
				"marriage" => I('post.marriage'),
				"tall" => array(),
				"amountPerMonth" => array(),
				"majorAreaBuy" => I('post.majorAreaBuy'),
				"majorLocationBuy" => I('post.majorLocationBuy'),
				"son" => array(),
				"daughter" => array(),
				"typeLive" => I('post.typeLive'),
				"politicalStatus" => I('post.politicalStatus')
		);
		if($tallMin){
			$temp = array(
					"EGT",
					$tallMin
			);
			array_push($whereProfile['tall'],$temp);
		}
		if($tallMax){
			$temp = array(
					"ELT",
					$tallMax
			);
			array_push($whereProfile['tall'],$temp);
		}
		if($tallMin && $tallMax && $tallMin == $tallMax){
			$whereProfile['tall'] = $tallMin;
		}
		if($amountPerMonthMin){
			$temp = array(
					"EGT",
					$amountPerMonthMin
			);
			array_push($whereProfile['amountPerMonth'],$temp);
		}
		if($amountPerMonthMax){
			$temp = array(
					"ELT",
					$amountPerMonthMax
			);
			array_push($whereProfile['amountPerMonth'],$temp);
		}
		if($amountPerMonthMax && $amountPerMonthMin && $amountPerMonthMax == $amountPerMonthMin){
			$whereProfile['amountPerMonth'] = $amountPerMonthMax;
		}
		if($sonMin){
			$temp = array(
					"EGT",
					$sonMin
			);
			array_push($whereProfile['son'],$temp);
		}
		if($sonMax){
			$temp = array(
					"ELT",
					$sonMax
			);
			array_push($whereProfile['son'],$temp);
		}
		if($sonMin && $sonMax && $sonMin == $sonMax){
			$whereProfile['son'] = $sonMax;
		}
		if($daughterMin){
			$temp = array(
					"EGT",
					$daughterMin
			);
			array_push($whereProfile['daughter'],$temp);
		}
		if($daughterMax){
			$temp = array(
					"ELT",
					$daughterMax
			);
			array_push($whereProfile['daughter'],$temp);
		}
		if($daughterMin && $daughterMax && $daughterMin == $daughterMax){
			$whereProfile['daughter'] = $daughterMax;
		}
		foreach($whereProfile as $key => $value){
			if(is_array($value)){
				if(!count($value)){
					unset($whereProfile[$key]);
				}
			}else{
				if(!$value){
					unset($whereProfile[$key]);
				}
			}
		}
		if(count($whereProfile)){
			$profileModel = M('clientProfile');
			$temp = $profileModel->where($whereProfile)->field("client")->select();
			$rProfile = array();
			if(count($temp)){
				$i = 0;
				foreach($temp as $key => $value){
					$rProfile[$i] = $value['client'];
					$i++;
				}
			}
			array_push($clientZone,$rProfile);
		}
		
		if(I('post.pharm')){
			$wherePharm = array(
					"pharm" => I('post.pharm')
			);
			$model = M('clientPharm');
			$temp = $model->where($wherePharm)->field("client")->select();
			$rPharm = array();
			if(count($temp)){
				$i = 0;
				foreach($temp as $key => $value){
					$rPharm[$i] = $value['client'];
					$i++;
				}
			}
			array_push($clientZone,$rPharm);
		}
		if(I('post.disease')){
			$whereDisease = array(
					"disease" => I('post.disease')
			);
			$model = M('clientDisease');
			$temp = $model->where($whereDisease)->field("client")->select();
			$rDisease = array();
			if(count($temp)){
				$i = 0;
				foreach($temp as $key => $value){
					$rDisease[$i] = $value['client'];
					$i++;
				}
			}
			array_push($clientZone,$rDisease);
		}
		if(I('post.healthProduct')){
			$whereHealthProduct = array(
					"healthProduct" => I('post.healthProduct')
			);
			$model = M('clientHealthProduct');
			$temp = $model->where($whereHealthProduct)->field("client")->select();
			$rHealthProduct = array();
			if(count($temp)){
				$i = 0;
				foreach($temp as $key => $value){
					$rHealthProduct[$i] = $value['client'];
					$i++;
				}
			}
			array_push($clientZone,$rHealthProduct);
		}
		if(I('post.habbit')){
			$whereHabbit = array(
					"habbit" => I('post.habbit')
			);
			$model = M('clientHabbit');
			$temp = $model->where($whereHabbit)->field("client")->select();
			$rHabbit = array();
			if(count($temp)){
				$i = 0;
				foreach($temp as $key => $value){
					$rHabbit[$i] = $value['client'];
					$i++;
				}
			}
			array_push($clientZone,$rHabbit);
		}
		if(I('post.financing')){
			$whereFinancing = array(
					"financing" => I('post.financing')
			);
			$model = M('clientFinancing');
			$temp = $model->where($whereFinancing)->field("client")->select();
			$rFinancing = array();
			if(count($temp)){
				$i = 0;
				foreach($temp as $key => $value){
					$rFinancing[$i] = $value['client'];
					$i++;
				}
			}
			array_push($clientZone,$rFinancing);
		}
		
		if(I('post.presentFois')){
			$wherePresent = array(
					"timeDebut"=>array(),
			);
			if(I('post.presentTimeDebut')){
				$temp = array(
						"EGT",
						I('post.presentTimeDebut'),
				);
				array_push($wherePresent['timeDebut'],$temp);
			}
			if(I('post.presentTimeEnd')){
				$date = explode("-",I('post.presentTImeEnd'));
    			$date[2]++;
    			$temp = array(
    					"ELT",
    					$date[0]."-".$date[1]."-".$date[2],
    			);
    			array_push($wherePresent['timeDebut'],$temp);
			}
			$presentModel = M('clientPresent');
			$temp = $presentModel->where($where)->field("client")->select();
			$rPresent = array();
			if(count($temp)){
				$temp2 = array();
				foreach($temp as $t){
					if(isset($temp2[$t['client']])){
						$temp2[$t['client']]++;
					}else{
						$temp2[$t['client']] = 1;
					}
				}
				$i = 0;
				foreach($temp2 as $key=>$value){
					switch(I('post.typePresent')){
						case "moins":
							if($value <= I('post.presentFois')){
								$rPresent[$i] = $key;
								$i++;
							}
							break;
						default:
							if($value >= I('post.presentFois')){
								$rPresent[$i] = $key;
								$i++;
							}
							break;
					}
				}
			}
			array_push($clientZone,$rPresent);
		}
		if(count($clientZone)){
			$result = $clientZone[0];
			for($i = 1;$i < count($clientZone);$i++){
				$result = array_intersect($result, $clientZone[$i]);
			}
		}
		$dateBirthBegin = I('post.dateBirthBegin');
		$dateBirthEnd = I('post.dateBirthEnd');
		
		$where = array(
				"name"=>I('post.name'),
				"nation"=>I('post.nation'),
				"dateBirth"=>array(),
				"sex"=>I('post.sex'),
				"identity"=>I('post.identity'),
		);
		if(I('post.departement')){
			$departementModel = D('Departement');
			$departement = $departementModel->get(I('post.departement'));
			if($departement){
				$userModel = D('User');
				$listUser = $userModel->where(array("departement"=>$departement['id']))->relation(true)->select();
				if(count($listUser)){
					$temp = array();
					foreach($listUser as $l){
						array_push($temp,$l['id']);
					}
					$where['serveur'] = array(
							"in",
							$temp
					);
				}
			}else{
				$where['serveur'] = I('post.serveur');
			}
		}
		if($dateBirthBegin){
			$temp = array(
					"EGT",
					$dateBirthBegin
			);
			array_push($where['dateBirth'],$temp);
		}
		if($dateBirthEnd){
			$date = explode("-",$dateBirthEnd);
			$date[2]++;
			$temp = array(
					"ELT",
					$date[0]."-".$date[1]."-".$date[2]
			);
			array_push($where['dateBirth'],$temp);
		}
		if($dateBirthBegin && $dateBirthEnd && $dateBirthBegin == $dateBirthEnd){
			$where['dateBirth'] = $dateBirthBegin;
		}
		foreach($where as $key => $value){
			if(is_array($value)){
				if(!count($value)){
					unset($where[$key]);
				}
			}else{
				if(!$value){
					unset($where[$key]);
				}
			}
		}
		if(isset($result)){
			if(count($result)){
				$where['id'] = array(
						"in",
						$result
				);
			}else{
				apiReturn("false");
				return;
			}
		}
		$page = I('post.page');
		if(!$page || !is_numeric($page)){
			$page = 1;
		}
		$clientModel = D("Client");
		$list = $clientModel->where($where)->relation(true)->order("id desc")->page($page,30)->select();
		$total = $clientModel->where($where)->relation(true)->count();
		$allPage = $total/30.0;
		if(intval($allPage) != $allPage){
			$allPage = intval($allPage)+1;
		}
		$result = array(
				"status"=>"ok",
				"list"=>$list,
				"allPage"=>$allPage,
				"total"=>$total,
		);
		apiReturn($result);
	}
	public function statisticConso(){
		if(!IS_POST){
			apiReturn("false");
			return;
		}
		$page = I('post.page');
		if(!$page || !is_numeric($page)){
			$page = 1;
		}
		$dateBegin = I('post.dateBegin');
		$dateEnd = I('post.dateEnd');
		$client = I('post.client');
		$cardNumber = I('post.cardNumber');
		$sumMin = I('post.sumMin');
		$sumMax = I('post.sumMax');
		$type = I('post.type');
		$idOrder = I('post.idOrder');
		$typePay = I('post.typePay');
		$serveur = I('post.serveur');
		$where = array(
				"status"=>"已完成",
				"time"=>array(),
				"priceSold"=>array()
		);
		if($dateBegin){
			$temp = array(
					"EGT",
					$dateBegin
			);
			array_push($where['time'],$temp);
		}
		if($dateEnd){
			$date = explode("-",$dateEnd);
			$date[2]++;
			$temp = array(
					"ELT",
					$date[0]."-".$date[1]."-".$date[2]
			);
			array_push($where['time'],$temp);
		}
		if($dateBegin && $dateEnd && $dateBegin == $dateEnd){
			$where['time'] = array(
					"like",
					"%".$dateBegin."%"
			);
		}
		if($cardNumber){
			$where['cardNumber'] = $cardNumber;
		}elseif($client){
			$clientModel = D('Client');
			$client = $clientModel->get($client);
			if(!$client){
				$where['cardNumber'] = "0";
			}else{
				$vips = D('Vip')->where(array("client"=>$client['id']))->select();
				$temp = array();
				foreach($vips as $v){
					array_push($temp,$v['id']);
				}
				$where['cardNumber'] = array(
						"in",
						$temp
				);
			}
		}
		if($sumMin){
			$temp = array(
					"EGT",
					$sumMin
			);
			array_push($where['priceSold'],$temp);
		}
		if($sumMax){
			$temp = array(
					"ELT",
					$sumMax
			);
			array_push($where['priceSold'],$temp);
		}
		if($sumMax && $sumMin && $sumMax == $sumMin){
			$where['priceSold'] = $sumMax;
		}
		if($idOrder){
			$where['idOrder'] = $idOrder;
		}
		if($typePay){
			$where['typePay'] = $typePay;
		}
		if($type){
			/*
			$listContent = M('orderContent')->group("idOrder")->select();
			$temp = array();
			foreach($listContent as $l){
				array_push($temp,$l['idorder']);
			}
			switch($type){
				case "rapide":
					$where['id'] = array(
						"not in",
						$temp
					);
					break;
				case "product":
					$where['id'] = array(
						"in",
						$temp
					);
					$where['priceSold'] = array(
							"NEQ",
							0
					);
					break;
				case "exchange":
					$where['etc'] = array(
						"like",
						"%积分兑换%"
					);
					break;
				case "gift":
					$where['priceSold'] = array(
						"EQ",
						0
					);
					break;
			}
			*/
			$where['type'] = $type;
		}
		if(!count($where['time']) || !$where['time']){
			unset($where['time']);
		}
		if(!count($where['priceSold']) || !$where['priceSold']){
			unset($where['priceSold']);
		}
		if($serveur){
			$userModel = D('User');
			$user = $userModel->getByName($serveur);
			if(!$user){
				$where['serveur'] = 0;
			}else{
				$where['serveur'] = $user['id'];
			}
		}
		$model = D('Order');
		$list = $model->relation(true)->where($where)->order("id desc")->page($page,30)->select();
		$allList = $model->relation(true)->where($where)->select();
		$count = count($allList);
		$allPage = $count/30.0;
		if(intval($allPage) < $allPage){
			$allPage = intval($allPage)+1;
		}
		$total = 0;
		foreach($allList as $l){
			$total += $l['pricesold'];
		}
		$result = array(
				"status"=>"ok",
				"list"=>$list,
				"allPage"=>$allPage,
				"count"=>$count,
				"total"=>$total,
				"sql"=>$model->getLastSql()
		);
		apiReturn($result);
	}
	public function statisticProduct(){
		if(!IS_POST){
			apiReturn("false");
			return;
		}
		$data = I('post.');
		if(!$data['page'] || !is_numeric($data['page'])){
			$data['page'] = 1;
		}
		$where = array(
				"confirmer"=>"系统自动",
				"time"=>array()
		);
		if($data['dateBegin']){
			$temp = array(
					"EGT",
					$dateBegin
			);
			array_push($where['time'],$data['dateBegin']);
		}
		if($data['dateEnd']){
			$date = explode("-",$data['dateEnd']);
			$temp = array(
					"ELT",
					$date[0]."-".$date[1]."-".$date[2]
			);
			array_push($where['time'],$temp);
		}
		if($data['shop']){
			$where['shop'] = $data['shop'];
		}
		if($data['supplier']){
			$productsSupplier = array();
			$supplierModel = D('Supplier');
			$supplier = $supplierModel->get($data['supplier']);
			foreach($supplier['product'] as $value){
				if(!in_array($value['id'], $productsSupplier)){
					array_push($productsSupplier,$value['id']);
				}
			}
		}
		if($data['class']){
			$productsClass = array();
			$productModel = D('Product');
			$whereProduct = array(
					"class"=>$data['class']
			);
			$list = $productModel->where($whereProduct)->select();
			foreach($list as $value){
				if(!in_array($value['id'],$productsClass)){
					array_push($productsClass,$value['id']);
				}
			}
		}
		if(isset($productsClass) && isset($productsSupplier)){
			$products = array_intersect($productsClass, $productsSupplier);
			//dump($products);
		}else{
			if(isset($productsClass)){
				$products = $productsClass;
			}
			if(isset($productsSupplier)){
				$products = $productsSupplier;
			}
		}
		if($data['product']){
			$productModel = D('Product');
			$product = $productModel->get($data['product']);
			if(!$product){
				$where['product'] = -1;
			}else{
				if(isset($products)){
					if(in_array($product['id'],$products)){
						$where['product'] = $product['id'];
					}else{
						$where['product'] = -1;
					}
				}else{
					$where['product'] = $product['id'];
				}
			}
		}else{
			if(isset($products) && count($products)){
				$where['product'] = array(
						"in",
						$products
				);
			}elseif(isset($products)){
				$where['product'] = -1;
			}
		}
		foreach($where as $key=>$value){
			if(!$value || !count($value)){
				unset($where[$key]);
			}
		}
		$model = D('InventoryOutRecord');
		$listOut = $model->where($where)->relation(true)->select();
		if(intval($allPage) != $allPage){
			$allPage = intval($allPage+1);
		}
		$list = array();
		foreach($listOut as $key=>$value){
			for($i = 0;$list[$i];$i++){
				if($list[$i]['product']['id'] == $value['product']['id']){
					$list[$i]['number'] += $value['nombre'];
					break;
				}
			}
			if($i == count($list)){
				$temp = array(
						"product"=>$value['product'],
						"number"=>$value['nombre']
				);
				array_push($list,$temp);
			}
		}
		$result = array(
				"status"=>"ok",
				"list"=>$list,
				"count"=>count($list),
		);
		apiReturn($result);
	}
	public function statisticPointConso(){
		if(!IS_POST){
			apiReturn("false1");
			return;
		}
		$data = I('post.');
		if(!$data['type']){
			apiReturn("false2");
			return;
		}
		$model = M('pointconsoRecord');
		if($data['timeBegin'] || $data['timeEnd']){
			$whereOrder = array(
					"time"=>array(),
					"typePay"=>"pointConso"
			);
			if($data['timeBegin']){
				$temp = array(
						"EGT",
						$data['timeBegin']
				);
				array_push($whereOrder['time'], $temp);
			}
			if($data['timeEnd']){
				$time = explode("-", $data['timeEnd']);
				$time[2]++;
				$temp = array(
						"ELT",
						$time['0']."-".$time[1]."-".$time[2]
				);
				array_push($whereOrder['time'],$temp);
			}
			if($data['timeBegin'] == $data['timeEnd']){
				$whereOrder['time'] = array(
						"like",
						"%".$data['timeBegin']."%"
				);
			}
			$result = D('Order')->where($whereOrder)->select();
			if($result){
				$temp = array();
				foreach($result as $r){
					array_push($temp,$r['id']);
				}
				$where = array(
						"idOrder"=>array(
								"in",
								$temp
						),
				);
			}else{
				$where = array(
						"idOrder"=>-1
				);
			}
			$list = $model->where($where)->select();
		}else{
			$list = $model->select();
		}
		$total = 0;
		foreach($list as $l){
			switch($data['type']){
				case "pointRecharger":
					$total += $l['pointrecharger'];
					break;
				case "pointGive":
					$total += $l['pointgive'];
					break;
				case "pointConso":
					$total += $l['pointconso'];
					break;
			}
		}
		$result = array(
				"status"=>"ok",
				"total"=>$total
		);
		apiReturn($result);
	}
	public function statisticClientGroup(){
		if(!IS_POST){
			apiReturn("false");
			return;
		}
		$post = I('post.');
		$where = array(
				"time"=>array(),
		);
		if($post['timeBegin']){
			$temp = array(
					"EGT",
					$post['timeBegin']
			);
			array_push($where['time'],$temp);
		}
		if($post['timeEnd']){
			$date = explode("-",$post['timeEnd']);
			$date[2]++;
			$temp = array(
					"ELT",
					$date[0]."-".$date[1]."-".$date[2]
			);
			array_push($where['time'],$temp);
		}
		if($post['timeBegin'] && $post['timeEnd'] && $post['timeBegin'] == $post['timeEnd']){
			$where['time'] == $post['timeBegin'];
		}
		if($post['name']){
			$where['name'] = array(
					"like",
					"%".$post['name']."%"
			);
		}
		if($post['serveur']){
			$serveur = D('User')->get($post['serveur']);
			if(!$serveur){
				$where['responsable'] = -1;
			}else{
				$where['responsable'] = $serveur['id'];
			}
		}
		if($post['leader']){
			$leader = D('Client')->get($post['leader']);
			if(!$leader){
				$where['leader'] = -1;
			}else{
				$where['leader'] = $leader['id'];
			}
		}
		if(!$where['time']){
			unset($where['time']);
		}
		$model = D('ClientGroup');
		$list = $model->where($where)->relation(true)->select();
		$result = array(
				"status"=>"ok",
				"list"=>$list,
				"count"=>count($list),
		);
		apiReturn($result);
	}
}