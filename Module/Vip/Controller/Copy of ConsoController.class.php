<?php
namespace Vip\Controller;
use Vip\Common\Controller\Base;
class ConsoController extends Base {
	public function rapideConso(){
		if(!testClassified($this->user,"rapideConso")){
			$this->error("权限不足");
			return;
		}
		if(!IS_POST){
			$this->display();
			return;
		}
		$data['cardNumber'] = I('post.cardNumber');
		$data['price'] = I('post.price');
		$data['priceSold'] = $data['price'];
		$data['etc'] = I('post.etc');
		$data['idOrder'] = time().rand(1000,9999);
		$orderModel = M('order');
		$result = $orderModel->add($data);
		if($result){
			header("location: ".U('/vip/conso/payConsoRapide/id/'.$result));
		}else{
			$this->error("订单提交失败");
		}
	}
	public function payConsoRapide($id = null){
		if(!testClassified($this->user,"rapideConso")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$orderModel = D('Order');
		$order = $orderModel->get($id);
		if(!$order){
			$this->error("非法访问");
			return;
		}
		if(!IS_POST){
			$this->assign("order",$order);
			$this->display();
			return;
		}
		$pwdPay = I('post.pwdPay');
		if(chiffrement($pwdPay) != $order['vip']['pwdpay']){
			$this->error("密码输入错误");
			return;
		}
		//扣款
		$typePay = I('post.typePay');
		if(!$typePay){
			$this->error("请选择支付方式");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($order['vip']['id']);
		$result = $this->renewVip($vip,$typePay,$order['pricesold'],$order['id']);
		if(!$result){
			$this->error("支付失败，请重试");
			return;
		}
		//更新记录
		$time = date("Y-m-d H:i:s");
		$data = array(
				"typePay"=>$typePay,
				"time"=>$time,
				"status"=>"已完成",
				"serveur"=>$this->user['id'],
				"type"=>"rapide"
		);
		$result = $orderModel->where("id='".$order['id']."'")->save($data);
		if(is_numeric($result)){
			switch($typePay){
				case "credit":
					$this->assign("typePay","储值");
					break;
				case "creditGive":
					$this->assign("typePay","赠送金额");
					break;
				case "pointConso":
					$this->assign("typePay","通用积分");
					break;
				case "pointHeal":
					$this->assign("typePay","调养积分");
					break;
				case "pointTourism":
					$this->assign("typePay","旅游积分");
					break;
			}
			$clientModel = D('Client');
			$client = $clientModel->where(array("id"=>$order['vip']['client']))->find();
			$this->assign("client",$client);
			$this->assign("time",$time);
			$this->assign("order",$order);
			$this->assign("operator",$this->user);
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"快速消费",
					"content"=>"单号:".$order['idorder'],
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			$this->display("rapideConsoFinish");
		}else{
			$this->renewVip($vip, $typePay, $order['pricesold']-$order['pricesold']*2,$order['id']);
			$this->error("失败");
		}
	}
	private function renewVip($vip,$typePay,$fee,$idOrder){
		if($fee >= 0){
			$vipModel = D('Vip');
			switch($typePay){
				case "credit":
					if($vip['credit'] < $fee){
						return false;
					}
					$dataRecord = array(
							"idOrder"=>$idOrder,
					);
					$data['credit'] = $vip['credit']-$fee;
					$dataRecord['credit'] = $fee;
					break;
				case "creditGive":
					if($vip['creditgive'] < $fee){
						return false;
					}
					$data['creditGive'] = $vip['creditgive']-$fee;
					$dataRecord = array(
							"idOrder"=>$idOrder,
					);
					$data['creditGive'] = $vip['creditgive']-$fee;
					$dataRecord['creditGive'] = $fee;
					break;
				case "pointConso":
					if($vip['pointrecharger']+$vip['pointgive']+$vip['pointconso']+$vip['credit'] < $fee){
						return false;
					}
					$dataRecord = array(
							"idOrder"=>$idOrder,
							"pointRecharger"=>0,
							"pointGive"=>0,
							"pointConso"=>0,
					);
					$data['pointRecharger'] = $vip['pointrecharger']-$fee;
					if($data['pointRecharger'] < 0){
						$data['pointRecharger'] = 0;
						$fee -= $vip['pointrecharger'];
						$dataRecord['pointRecharger'] = $vip['pointrecharger'];
					}else{
						$dataRecord['pointRecharger'] = $fee;
						$fee = 0;
					}
					$data['pointGive'] = $vip['pointgive']-$fee;
					if($data['pointGive'] < 0){
						$data['pointGive'] = 0;
						$fee -= $vip['pointgive'];
						$dataRecord['pointGive'] = $vip['pointgive'];
					}else{
						$dataRecord['pointGive'] = $fee;
						$fee = 0;
					}
					$data['pointConso'] = $vip['pointconso']-$fee;
					if($data['pointConso'] < 0){
						$data['pointConso'] = 0;
						$fee -= $vip['pointconso'];
						$dataRecord['pointConso'] = $vip['pointconso'];
					}else{
						$dataRecord['pointConso'] = $fee;
						$fee = 0;
					}
					$data['credit'] = $vip['credit']-$fee;
					if($data['credit'] < 0){
						$data['credit'] = 0;
						$fee -= $vip['credit'];
						$dataRecord['credit'] = $vip['credit'];
					}else{
						$dataRecord['credit'] = $fee;
						$fee = 0;
					}
					break;
				case "pointHeal":
					if($vip['pointheal']+$vip['credit']+$vip['pointrecharger']+$vip['pointgive']+$vip['pointconso'] < $fee){
						return false;
					}
					$dataRecord = array(
							"idOrder"=>$idOrder,
					);
					$data['pointHeal'] = $vip['pointheal']-$fee;
					if($data['pointHeal'] < 0){
						$data['pointHeal'] = 0;
						$fee -= $vip['pointheal'];
						$dataRecord['pointHeal'] = $vip['pointheal'];
					}else{
						$dataRecord['pointHeal'] = $fee;
						$fee = 0;
					}
					$data['pointRecharger'] = $vip['pointrecharger']-$fee;
					if($data['pointRecharger'] < 0){
						$data['pointRecharger'] = 0;
						$fee -= $vip['pointrecharger'];
						$dataRecord['pointRecharger'] = $vip['pointrecharger'];
					}else{
						$dataRecord['pointRecharger'] = $fee;
						$fee = 0;
					}
					$data['pointGive'] = $vip['pointgive']-$fee;
					if($data['pointGive'] < 0){
						$data['pointGive'] = 0;
						$fee -= $vip['pointgive'];
						$dataRecord['pointGive'] = $vip['pointgive'];
					}else{
						$dataRecord['pointGive'] = $fee;
						$fee = 0;
					}
					$data['pointConso'] = $vip['pointconso']-$fee;
					if($data['pointConso'] < 0){
						$data['pointConso'] = 0;
						$fee -= $vip['pointconso'];
						$dataRecord['pointConso'] = $vip['pointconso'];
					}else{
						$dataRecord['pointConso'] = $fee;
						$fee = 0;
					}
					$data['credit'] = $vip['credit']-$fee;
					if($data['credit'] < 0){
						$data['credit'] = 0;
						$fee -= $vip['credit'];
						$dataRecord['credit'] = $vip['credit'];
					}else{
						$dataRecord['credit'] = $fee;
						$fee = 0;
					}
					break;
				case "pointTourism":
					if($vip['pointtourism']+$vip['credit']+$vip['pointrecharger']+$vip['pointgive']+$vip['pointconso'] < $fee){
						return false;
					}
					$dataRecord = array(
							"idOrder"=>$idOrder,
					);
					$data['pointTourism'] = $vip['pointtourism']-$fee;
					if($data['pointTourism'] < 0){
						$data['pointTourism'] = 0;
						$fee -= $vip['pointtourism'];
						$dataRecord['pointTourism'] = $vip['pointtourism'];
					}else{
						$dataRecord['pointTourism'] = $fee;
						$fee = 0;
					}
					$data['pointRecharger'] = $vip['pointrecharger']-$fee;
					if($data['pointRecharger'] < 0){
						$data['pointRecharger'] = 0;
						$fee -= $vip['pointrecharger'];
						$dataRecord['pointRecharger'] = $vip['pointrecharger'];
					}else{
						$dataRecord['pointRecharger'] = $fee;
						$fee = 0;
					}
					$data['pointGive'] = $vip['pointgive']-$fee;
					if($data['pointGive'] < 0){
						$data['pointGive'] = 0;
						$fee -= $vip['pointgive'];
						$dataRecord['pointGive'] = $vip['pointgive'];
					}else{
						$dataRecord['pointGive'] = $fee;
						$fee = 0;
					}
					$data['pointConso'] = $vip['pointconso']-$fee;
					if($data['pointConso'] < 0){
						$data['pointConso'] = 0;
						$fee -= $vip['pointconso'];
						$dataRecord['pointConso'] = $vip['pointconso'];
					}else{
						$dataRecord['pointConso'] = $fee;
						$fee = 0;
					}
					$data['credit'] = $vip['credit']-$fee;
					if($data['credit'] < 0){
						$data['credit'] = 0;
						$fee -= $vip['credit'];
						$dataRecord['credit'] = $vip['credit'];
					}else{
						$dataRecord['credit'] = $fee;
						$fee = 0;
					}
					break;
			}
			$result = $vipModel->where("id='".$vip['id']."'")->save($data);
			M('pointconsoRecord')->add($dataRecord);
			if(is_numeric($result)){
				$newVip = $vipModel->where(array("id"=>$vip['id']))->find();
				$data = array(
						"idOrder"=>$idOrder,
						"credit"=>$newVip['credit'],
						"creditGive"=>$newVip['creditgive'],
						"pointRecharger"=>$newVip['pointrecharger'],
						"pointGive"=>$newVip['pointgive'],
						"pointConso"=>$newVip['pointconso'],
						"pointTourism"=>$newVip['pointtourism'],
						"pointHeal"=>$newVip['pointheal'],
				);
				M('pointRest')->add($data);
				return true;
			}else{
				return false;
			}
		}
	}
	public function exchange(){
		if(!testClassified($this->user,"exchange")){
			$this->error("权限不足");
			return;
		}
		if(!IS_POST){
			$this->display();
			return;
		}
		$idOrder = I('post.idOrder');
		$orderModel = D('Order');
		$order = $orderModel->get($idOrder);
		if(!$order){
			$this->error("错误");
			return;
		}
		$productModel = D('Product');
		$priceTotal = 0;
		$priceSold = 0;
		$vipModel = D('Vip');
		$vip = $vipModel->get($order['cardnumber']);
		if($vip['card']['name'] != "积分成员"){
			echo "只有积分成员可使用此功能!";
			return;
		}
		foreach($order['content'] as $c){
			$product = $productModel->get($c['product']);
			$priceTotal += $product['pointout'] * $c['number'];
		}
		$data = array(
				"price"=>$priceTotal,
				"priceSold"=>$priceTotal
		);
		$result = $orderModel->where(array("id"=>$order['id']))->save($data);
		if(is_numeric($result)){
			header("location: ".U('/vip/conso/payExchange/id/'.$order['id']));
		}else{
			$this->error("错误");
		}
	}
	public function payExchange($id = null){
		if(!testClassified($this->user,"exchange")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$orderModel = D('Order');
		$order = $orderModel->get($id);
		if(!$order){
			$this->error("非法访问");
			return;
		}
		$productModel = D('Product');
		for($i = 0;$i < count($order['content']);$i++){
			$product = $productModel->get($order['content'][$i]['product']);
			$order['content'][$i]['product'] = $product;
		}
		if(!IS_POST){
			$this->assign("order",$order);
			$this->display();
			return;
		}
		$pwdPay = I('post.pwdPay');
		if(chiffrement($pwdPay) != $order['vip']['pwdpay']){
			$this->error("密码输入错误");
			return;
		}
		//扣款
		$typePay = "pointConso";
		$vipModel = D('Vip');
		$vip = $vipModel->get($order['vip']['id']);
		$result = $this->renewVip($vip,$typePay,$order['pricesold'],$order['id']);
		if(!$result){
			$this->error("支付失败，请重试");
			return;
		}
		//更新记录
		$time = date("Y-m-d H:i:s");
		$data = array(
				"typePay"=>$typePay,
				"time"=>$time,
				"status"=>"已完成",
				"etc"=>$order['etc'],
				"serveur"=>$this->user['id'],
				"type"=>"exchange"
		);
		$result = $orderModel->where("id='".$order['id']."'")->save($data);
		if(is_numeric($result)){
			$clientModel = D('Client');
			$client = $clientModel->where(array("id"=>$order['vip']['client']))->find();
			$this->assign("client",$client);
			$this->renewProduct($order);
			$this->assign("time",$time);
			$this->assign("order",$order);
			$this->assign("operator",$this->user);
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"商品兑换",
					"content"=>"单号:".$order['idorder'],
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			$this->display("productExchangeFinish");
		}else{
			$this->renewVip($vip, $typePay, $order['pricesold']-$order['pricesold']*2,$order['id']);
			$this->error("失败");
		}
	}
	public function productConso(){
		if(!testClassified($this->user,"productConso")){
			$this->error("权限不足");
			return;
		}
		if(!IS_POST){
			$this->display();
			return;
		}
		$idOrder = I('post.idOrder');
		$orderModel = D('Order');
		$order = $orderModel->get($idOrder);
		if(!$order){
			$this->error("错误");
			return;
		}
		$productModel = D('Product');
		$priceTotal = 0;
		$priceSold = 0;
		$vipModel = D('Vip');
		$vip = $vipModel->get($order['cardnumber']);
		foreach($order['content'] as $c){
			$product = $productModel->get($c['product']);
			$priceTotal += $product['priceout'] * $c['number'];
			if($vip['card']['name'] == "自由卡" && $product['ifsold']){
				$priceSold += $product['priceout'] * $c['number'] * $vip['card']['sold'];
			}else{
				$priceSold += $product['priceout'] * $c['number'];
			}
		}
		$data = array(
				"price"=>$priceTotal,
				"priceSold"=>$priceSold
		);
		$result = $orderModel->where(array("id"=>$order['id']))->save($data);
		if(is_numeric($result)){
			header("location: ".U('/vip/conso/payConsoProduct/id/'.$order['id']));
		}else{
			$this->error("错误");
		}
	}
	public function payConsoProduct($id = null){
		if(!testClassified($this->user,"productConso")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$orderModel = D('Order');
		$order = $orderModel->get($id);
		if(!$order){
			$this->error("非法访问");
			return;
		}
		$productModel = D('Product');
		for($i = 0;$i < count($order['content']);$i++){
			$product = $productModel->get($order['content'][$i]['product']);
			$order['content'][$i]['product'] = $product;
		}
		if(!IS_POST){
			$this->assign("order",$order);
			$this->display();
			return;
		}
		$pwdPay = I('post.pwdPay');
		if(chiffrement($pwdPay) != $order['vip']['pwdpay']){
			$this->error("密码输入错误");
			return;
		}
		//扣款
		$typePay = I('post.typePay');
		if(!$typePay){
			$this->error("请选择支付方式");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($order['vip']['id']);
		$result = $this->renewVip($vip,$typePay,$order['pricesold'],$order['id']);
		if(!$result){
			$this->error("支付失败，请重试");
			return;
		}
		//更新记录
		$time = date("Y-m-d H:i:s");
		$data = array(
				"typePay"=>$typePay,
				"time"=>$time,
				"status"=>"已完成",
				"serveur"=>$this->user['id'],
				"type"=>"product"
		);
		$result = $orderModel->where("id='".$order['id']."'")->save($data);
		if(is_numeric($result)){
			switch($typePay){
				case "credit":
					$this->assign("typePay","储值");
					break;
				case "creditGive":
					$this->assign("typePay","赠送金额");
					break;
				case "pointConso":
					$this->assign("typePay","通用积分");
					break;
				case "pointHeal":
					$this->assign("typePay","调养积分");
					break;
				case "pointTourism":
					$this->assign("typePay","旅游积分");
					break;
			}
			$clientModel = D('Client');
			$client = $clientModel->where(array("id"=>$order['vip']['client']))->find();
			$this->assign("client",$client);
			$this->renewProduct($order);
			$this->assign("time",$time);
			$this->assign("order",$order);
			$this->assign("operator",$this->user);
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"商品消费",
					"content"=>"单号:".$order['idorder'],
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			$this->display("productConsoFinish");
		}else{
			$this->renewVip($vip, $typePay, $order['pricesold']-$order['pricesold']*2,$order['id']);
			$this->error("失败");
		}
	}
	private function renewProduct($order){
		$contentModel = D('OrderContent');
		$listContent = $contentModel->get($order['id']);
		$shopModel = D('Shop');
		$shop = $shopModel->get($this->user['departement']['shop']);
		if(!$shop){
			$shop = $shopModel->get(1);
		}
		$inventoryModel = D("Inventory");
		$invRecordModel = M('inventoryOutRecord');
		$foisModel = M('fois');
		foreach($listContent as $l){
			//更改库存
			$inventory = $inventoryModel->where(array("shop"=>$shop['id'],"product"=>$l['product']['id']))->find();
			if($inventory){
				$data = array(
						"inventory"=>$inventory['inventory']-$l['number'],
				);
				$inventoryModel->where(array("id"=>$inventory['id']))->save($data);
			}else{
				$data = array(
						"shop"=>$shop['id'],
						"product"=>$l['product']['id'],
						"inventory"=>0-$l['number'],
				);
				$inventoryModel->add($data);
			}
			//记录库存的更改
			$data = array(
					"shop"=>$shop['id'],
					"product"=>$l['product']['id'],
					"nombre"=>$l['number'],
					"priceOut"=>$l['product']['priceout'],
					"time"=>date("Y-m-d H:i:s"),
					"operator"=>$this->user['id'],
					"confirmer"=>"系统自动",
			);
			$invRecordModel->add($data);
			if($l['product']['iffois']){
				$where = array(
						"client"=>$order['vip']['client'],
						"product"=>$l['product']['id'],
				);
				$result = $foisModel->where($where)->find();
				if($result){
					$data = array(
							"fois"=>$l['number']+$result['fois'],
					);
					$foisModel->where($where)->save($data);
				}else{
					$data = array(
							"client"=>$order['vip']['client'],
							"product"=>$l['product']['id'],
							"fois"=>$l['number'],
					);
					$foisModel->add($data);
				}
			}
		}
	}
	public function listOrder(){
		if(!testClassified($this->user,"listOrder")){
			$this->error("权限不足");
			return;
		}
		$orderModel = D('Order');
		if(!IS_POST){
			$where = array(
					"time"=>array(
							"like",
							"%".date("Y-m-d")."%"
					),
					"serveur"=>$this->user['id']
			);
			$list = $orderModel->relation(true)->order("time desc")->where($where)->select();
		}else{
			$temp = array(
					"idOrder"=>I('post.idOrder'),
					"cardNumber"=>I('post.cardNumber'),
					"time"=>array(),
			);
			if(I("post.timeBegin")){
				$temp2 = array(
						"EGT",
						I('post.timeBegin')
				);
				array_push($temp['time'],$temp2);
			}
			if(I("post.timeEnd")){
				$temp2 = array(
						"ELT",
						I('post.timeEnd')
				);
				array_push($temp['time'],$temp2);
			}
			$where = array(
					"status"=>"已完成"
			);
			foreach($temp as $key=>$value){
				if(!$value ||!count($value)){
					continue;
				}
				$where[$key] = $value;
			}
			if(I('post.type')){
				$type = I('post.type');
				switch($type){
					case "product":
						$orderChercher = M('orderContent')->group("idOrder")->select();
						$temp = array();
						foreach($orderChercher as $key=>$value){
							array_push($temp,$value['idorder']);
						}
						$where['id'] = array(
								"in",
								$temp,
						);
						break;
					case "rapide":
						$orderChercher = M('orderContent')->group("idOrder")->select();
						$temp = array();
						foreach($orderChercher as $key=>$value){
							array_push($temp,$value['idorder']);
						}
						$where['id'] = array(
								"not in",
								$temp
						);
						break;
					case "exchange":
						$where['etc'] = array(
								"like",
								"%积分兑换%"
						);
						break;
					case "gift":
						$where['status'] = "已完成";
						$where['price'] = 0;
						break;
				}
			}
			$where["serveur"] = $this->user['id'];
			$list = $orderModel->relation(true)->where($where)->order("time desc")->select();
		}
		$i = 0;
		while($list[$i]){
			$list[$i]['client'] = D('Client')->get($list[$i]['vip']['client']);
			$i++;
		}
		$this->assign("list",$list);
		$this->display();
	}
	public function foisConso(){
		if(!testClassified($this->user,"listOrder")){
			$this->error("权限不足");
			return;
		}
		$this->display();
	}
	public function rembourse($id = null){
		if(!testClassified($this->user,"rembourse")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$orderModel = D('Order');
		$order = $orderModel->get($id);
		if(!$order){
			$this->error("非法访问");
			return;
		}
		if($order['status'] != "已完成"){
			$this->error("订单不符合退单标准");
			return;
		}
		$this->assign("order",$order);
		$this->display();
	}
	public function detailOrder($id = null){
		if(!testClassified($this->user,"rembourse")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$orderModel = D('Order');
		$order = $orderModel->get($id);
		if(!$order){
			$this->error("非法访问");
			return;
		}
		$contentModel = D('OrderContent');
		for($i = 0;$order['content'][$i];$i++){
			$order['content'][$i] = $contentModel->getById($order['content'][$i]['id']);
		}
		$client = D('Client')->get($order['vip']['client']);
		$this->assign("client",$client);
		$this->assign("order",$order);
		switch($order['typepay']){
			case "credit":
				$typePay = "储值";
				break;
			case "creditGive":
				$typePay = "赠送金额";
				break;
			case "pointConso":
				$typePay = "通用积分";
				break;
			case "pointHeal":
				$typePay = "调养积分";
				break;
			case "pointTourism":
				$typePay = "旅游积分";
				break;
		}
		$this->assign("typePay",$typePay);
		if($order['type'] == "rapide"){
			$this->display("detailOrderRapide");
		}else{
			$this->display("detailOrderProduct");
		}
	}
	public function listFoisConso(){
		if(!testClassified($this->user,"listFoisConso")){
			$this->error("权限不足");
			return;
		}
		$recordModel = M("foisRecord");
		if(!IS_POST){
			$where = array(
					"time"=>array(
							"like",
							"%".date("Y-m-d")."%"
					)
			);
			$list = $recordModel->where($where)->order("time desc")->select();
		}else{
			$data = I('post.');
			$where = array();
			if($data['timeBegin']){
				$temp = array(
						"time"=>array(
								"EGT",
								$data['timeBegin']
						)
				);
				array_push($where,$temp);
			}
			if($data['timeEnd']){
				if($where['time']){
					$temp = array(
							"ELT",
							$data['timeEnd']
					);
					array_push($where['time'],$temp);
				}else{
					$temp = array(
							"time"=>array(
									"ELT",
									$data['timeEnd']
							)
					);
					array_push($where,$temp);
				}
			}
			$list = $recordModel->where($where)->order("time desc")->select();
		}
		$result = array();
		$foisModel = D('Fois');
		$userModel = D('User');
		$i = 0;
		while($list[$i]){
			$list[$i]['fois'] = $foisModel->relation(true)->where(array("id"=>$list[$i]['fois']))->find();
			$list[$i]['operator'] = $userModel->get($list[$i]['operator']);
			$i++;
		}
		$this->assign("list",$list);
		$this->display();
	}
	public function gift(){
		if(!testClassified($this->user,"gift")){
			$this->error("权限不足");
			return;
		}
		if(!IS_POST){
			$this->display();
			return;
		}
		$idOrder = I('post.idOrder');
		$orderModel = D('Order');
		$order = $orderModel->get($idOrder);
		if(!$order){
			$this->error("错误");
			return;
		}
		$productModel = D('Product');
		$priceTotal = 0;
		$priceSold = 0;
		$vipModel = D('Vip');
		$vip = $vipModel->get($order['cardnumber']);
		$data = array(
				"price"=>0,
				"priceSold"=>0
		);
		$result = $orderModel->where(array("id"=>$order['id']))->save($data);
		if(is_numeric($result)){
			header("location: ".U('/vip/conso/payGift/id/'.$order['id']));
		}else{
			$this->error("错误");
		}
	}
	public function payGift($id = null){
		if(!testClassified($this->user,"gift")){
			$this->error("权限不足");
			return;
		}
		if(!$id){
			$this->error("非法访问");
			return;
		}
		$orderModel = D('Order');
		$order = $orderModel->get($id);
		if(!$order){
			$this->error("非法访问");
			return;
		}
		$productModel = D('Product');
		for($i = 0;$i < count($order['content']);$i++){
			$product = $productModel->get($order['content'][$i]['product']);
			$order['content'][$i]['product'] = $product;
		}
		if(!IS_POST){
			$this->assign("order",$order);
			$this->display();
			return;
		}
		$pwdPay = I('post.pwdPay');
		if(chiffrement($pwdPay) != $order['vip']['pwdpay']){
			$this->error("密码输入错误");
			return;
		}
		//扣款
		$typePay = "pointConso";
		$vipModel = D('Vip');
		$vip = $vipModel->get($order['vip']['id']);
		$result = $this->renewVip($vip,$typePay,$order['pricesold'],$order['id']);
		if(!$result){
			$this->error("支付失败，请重试");
			return;
		}
		//更新记录
		$time = date("Y-m-d H:i:s");
		$data = array(
				"typePay"=>$typePay,
				"time"=>$time,
				"status"=>"已完成",
				"serveur"=>$this->user['id'],
				"type"=>"gift"
		);
		$result = $orderModel->where("id='".$order['id']."'")->save($data);
		if(is_numeric($result)){
			$this->renewProduct($order);
			$this->assign("time",$time);
			$this->assign("order",$order);
			$this->assign("operator",$this->user);
			$clientModel = D('Client');
			$client = $clientModel->where(array("id"=>$order['vip']['client']))->find();
			$this->assign("client",$client);
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"礼品领取",
					"content"=>"单号:".$order['idorder'],
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			$this->display("productGiftFinish");
		}else{
			$this->renewVip($vip, $typePay, $order['pricesold']-$order['pricesold']*2,$order['id']);
			$this->error("失败");
		}
	}
	public function rembourseDirect(){
		if(!testClassified($this->user,"rembourseDirect")){
			$this->error("权限不足");
			return;
		}
		if(!IS_POST){
			$this->display();
			return;
		}
		$post = I('post.');
		if(!$post['cardNumber']){
			$this->error("请输入会员卡号码");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($post['cardNumber']);
		if(!$vip){
			$this->error("会员卡号码输入错误");
			return;
		}
		if(!$post['sum'] || !is_numeric($post['sum'])){
			$this->error("请输入退款金额或输入的退款金额格式有误");
			return;
		}
		$post['sum'] = abs($post['sum']);
		if(!$post['type'] || !in_array($post['type'],array("credit","creditGive","pointRecharger","pointGive","pointConso","pointTourism","pointHeal"))){
			$this->error("请输入类型");
		}
		$data = array(
				"idOrder"=>time().rand(1000,9999),
				"cardNumber"=>$post['cardNumber'],
				"type"=>"rembourse",
				"typePay"=>$post['type'],
				"price"=>(0-$post['sum']),
				"priceSold"=>(0-$post['sum']),
				"time"=>date("Y-m-d H:i:s"),
				"status"=>"已完成",
				"serveur"=>$this->user['id'],
				"etc"=>$post['etc']
		);
		$orderModel = D('Order');
		$recordId = $orderModel->add($data);
		if($recordId){
			$data = array();
			switch($post['type']){
				case "credit":
					$data['credit'] = $vip['credit']+$post['sum'];
					break;
				case "creditGive":
					$data['creditGive'] = $vip['creditgive']+$post['sum'];
					break;
				case "pointRecharger":
					$data['pointRecharger'] = $vip['pointrecharger']+$post['sum'];
					break;
				case "pointGive":
					$data['pointGive'] = $vip['pointgive']+$post['sum'];
					break;
				case "pointConso":
					$data['pointConso'] = $vip['pointconso']+$post['sum'];
					break;
				case "pointTourism":
					$data['pointTourism'] = $vip['pointtourism']+$post['sum'];
					break;
				case "pointHeal":
					$data['pointHeal'] = $vip['pointheal']+$post['sum'];
					break;
			}
			$result = $vipModel->where(array("id"=>$vip['id']))->save($data);
			if($result){
				$this->success("成功");
			}else{
				$this->error("失败1");
			}
		}else{
			$this->error("失败2");
		}
	}
}