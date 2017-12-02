<?php
namespace Api\Controller;
use Think\Controller;
class ConsoController extends Controller {
	public function rembourse(){
		if(!IS_POST){
			apiReturn("false");
			return;
		}
		$id = I('post.id');
		$orderModel = D('Order');
		$order = $orderModel->get($id);
		if(!$order){
			apiReturn("false");
			return;
		}
		$vipModel = D('Vip');
		$recordModel = M('pointconsoRecord');
		$record = $recordModel->where(array("idOrder"=>$order['id']))->find();
		$vip = $vipModel->get($order['cardnumber']);
		$data = array(
				"credit"=>$vip['credit']+$record['credit'],
				"creditGive"=>$vip['creditgive']+$record['creditgive'],
				"pointRecharger"=>$vip['pointrecharger']+$record['pointrecharger'],
				"pointGive"=>$vip['pointgive']+$record['pointgive'],
				"pointConso"=>$vip['pointconso']+$record['pointconso'],
				"pointtourism"=>$vip['pointtourism']+$record['pointtourism'],
				"pointHeal"=>$vip['pointheal']+$record['pointheal']
		);
		/*
		switch($order['typepay']){
			case "credit":
				$data = array(
					"credit"=>$vip['credit']+$order['pricesold'],
				);
				break;
			case "creditGive":
				$data = array(
					"creditGive"=>$vip['creditgive']+$order['pricesold'],
				);
				break;
			case "pointConso":
				$data = array(
					"pointGive"=>$vip['pointgive']+$order['pricesold'],
				);
				break;
			case "pointTourism":
				$data = array(
					"pointTourism"=>$vip['pointtourism']+$order['pricesold'],
				);
				break;
			case "pointHeal":
				$data = array(
					"pointHeal"=>$vip['pointheal']+$order['pricesold'],
				);
				break;
		}
		*/
		$result = $vipModel->where(array("id"=>$vip['id']))->save($data);
		if(is_numeric($result)){
			$orderModel->where(array("id"=>$order['id']))->delete();
			$operator = session("user");
			// 回复库存
			$content = $order['content'];
			if($content && is_array($content) && count($content)){
				$orderOperator = $order['serveur'];
				$orderDep = D('Departement')->get($orderOperator['departement']);
				$dataAll = array();
				$inventoryModel = D('Inventory');
				foreach($content as $c){
					$inventory = $inventoryModel->getShopProductInventory($orderDep['shop']['id'],$c['product']);
					$dataInventory = array(
							"inventory"=>$inventory['inventory']+$c['number']
					);
					$inventoryModel->where(array("id"=>$inventory['id']))->save($dataInventory);
					$temp = array(
							"shop"=>$orderDep['shop']['id'],
							"product"=>$c['product'],
							"nombre"=>$c['number'],
							"time"=>date("Y-m-d H:i:s"),
							"operator"=>$operator['id'],
							"etc"=>"退单"
					);
					array_push($dataAll,$temp);
				}
				D('InventoryInRecord')->addAll($dataAll);
			}
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"退单",
					"content"=>"单号".$order['idorder'],
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			apiReturn("ok");
		}else{
			apiReturn("失败");
		}
	}
	public function newContent(){
		if(!IS_POST){
			echo '{
					"status":"false"
					}';
			return;
		}
		$productCode = I('post.product');
		$cardNumber = I('post.cardNumber');
		$idOffre = I('post.idOffre');
		if(!$productCode){
			echo '{
					"status":"false"
					}';
			return;
		}
		$productModel = D('Product');
		$orderModel = D('Order');
		$contentModel = M('orderContent');
		if(!$cardNumber){
			echo '{
					"status":"false"
					}';
			return;
		}
		if(!$idOffre){
			$idOffre = time().rand(1000,9999);
			$dataOffre = array(
					"idOrder"=>$idOffre,
					"cardNumber"=>$cardNumber,
			);
			if(!$orderModel->add($dataOffre)){
				echo '{
					"status":"false"
					}';
				return;
			}
		}
		$order = $orderModel->get($idOffre);
		if(is_numeric($productCode) && $productCode < 100){
			$result = $contentModel->where(array("idOrder"=>$order['id']))->order("id desc")->find();
			$data = array(
					"number"=>$productCode,
			);
			$result = $contentModel->where(array("id"=>$result['id']))->save($data);
		}else{
			$product = $productModel->get($productCode);
			if(!$product){
				echo '{
					"status":"false"
					}';
				return;
			}
			$result = $contentModel->where("idOrder='".$order['id']."' and product='".$product['id']."'")->find();
			if($result){
				$data = array(
						"number"=>$result['number']+1
				);
				$result = $contentModel->where("idOrder='".$order['id']."' and product='".$product['id']."'")->save($data);
				if(!is_numeric($result)){
					echo '{
					"status":"false"
					}';
					return;
				}
			}else{
				$data = array(
						"idOrder"=>$order['id'],
						"product"=>$product['id'],
						"number"=>1,
				);
				$result = $contentModel->add($data);
				if(!$result){
					echo '{
					"status":"false"
					}';
					return;
				}
			}
		}
		$contents = $contentModel->where("idOrder='".$order['id']."'")->select();
		echo '{
				"status":"ok",
				"idOrder":"'.$order['idorder'].'",
				"content":[';
		for($i = 0;$i < count($contents);$i++){
			$product = $productModel->get($contents[$i]['product']);
			echo '{
					"name":"'.$product['name'].'",
					"code":"'.$product['code'].'",
					"price":"'.$product['priceout'].'",
					"point":"'.$product['pointout'].'",
					"number":"'.$contents[$i]['number'].'"		
			}';
			if($i+1 != count($contents)){
				echo ',';
			}
		}
		echo ']
		}';
	}
	public function getFois(){
		if(!IS_POST){
			apiReturn("false");
			return;
		}
		$cardNumber = I('post.cardNumber');
		$vipModel = D('Vip');
		$vip = $vipModel->get($cardNumber);
		if(!$vip){
			apiReturn("false");
			return;
		}
		$where = array(
				"client"=>$vip['client']['id'],
				"fois"=>array(
						"GT",
						0
				),
		);
		$foisModel = D('Fois');
		$list = $foisModel->relation(true)->where($where)->select();
		if(!count($list)){
			apiReturn("fail");
			return;
		}
		$result = array(
				"status"=>"ok",
				"list"=>$list,
		);
		apiReturn($result);
	}
	public function useFois(){
		if(!IS_POST){
			apiReturn("false1");
			return;
		}
		$cardNumber = I('post.cardNumber');
		$idFois = I('post.id');
		$pwd = I('post.pwd');
		$foisModel = D('Fois');
		$fois = $foisModel->where(array("id"=>$idFois))->relation(true)->find();
		if(!$fois){
			apiReturn("false2");
			return;
		}
		$vipModel = D('Vip');
		$vip = $vipModel->get($cardNumber);
		if(!$vip || $vip['pwdpay'] != chiffrement($pwd) || $fois['client']['id'] != $vip['client']['id']){
			apiReturn("false3");
			return;
		}
		$data = array(
				"fois"=>$fois['fois']-1,
		);
		$result = $foisModel->where(array("id"=>$idFois))->save($data);
		/*
		if($data['fois'] == 0){
			$foisModel->where(array("id"=>$idFois))->delete();
		}
		*/
		if(is_numeric($result)){
			$operator = session("user");
			$recordModel = M('foisRecord');
			$data = array(
					"fois"=>$idFois,
					"time"=>date("Y-m-d H:i:s"),
					"operator"=>$operator["id"]
			);
			$recordModel->add($data);
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"计次消费",
					"content"=>"客户id:".$vip['client']['id']."商品:".$fois['product'],
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			apiReturn("ok");
		}else{
			apiReturn("fail");
		}
	}
	public function newGift(){
		if(!IS_POST){
			echo '{
					"status":"false"
					}';
			return;
		}
		$productCode = I('post.product');
		$cardNumber = I('post.cardNumber');
		$idOffre = I('post.idOffre');
		if(!$productCode){
			echo '{
					"status":"false"
					}';
			return;
		}
		$productModel = D('Product');
		$orderModel = D('Order');
		$contentModel = M('orderContent');
		$product = $productModel->get($productCode);
		if(!$product){
			echo '{
					"status":"false"
					}';
			return;
		}
		if(!$cardNumber){
			echo '{
					"status":"false"
					}';
			return;
		}
		//先检测是否领取过
		$vipModel = D('Vip');
		$vip = $vipModel->get($cardNumber);
		if(!$vip){
			apiReturn("false");
			return;
		}
		$allVip = $vipModel->where(array("client"=>$vip['client']['id']))->select();
		$vipChercher = array();
		foreach($allVip as $key=>$value){
			array_push($vipChercher,$value['cardnumber']);
		}
		$orderChercher = $orderModel->where(array("cardNumber"=>array("in",$vipChercher),"priceSold"=>0,"status"=>"已完成"))->select();
		if(count($orderChercher)){
			$temp = $orderChercher;
			foreach($temp as $key=>$value){
				unset($orderChercher[$key]);
				array_push($orderChercher,$value['id']);
			}
			$contentChercher = $contentModel->where(array("idOrder"=>array("in",$orderChercher)))->select();
			foreach($contentChercher as $value){
				if($product['id'] == $value['product']){
					apiReturn("already");
					return;
				}
			}
		}
			
			
			
		if(!$idOffre){
			//生成新订单
			$idOffre = time().rand(1000,9999);
			$dataOffre = array(
					"idOrder"=>$idOffre,
					"cardNumber"=>$cardNumber,
			);
			if(!$orderModel->add($dataOffre)){
				echo '{
					"status":"false2"
					}';
				return;
			}
		}
		$order = $orderModel->get($idOffre);
		
		$result = $contentModel->where("idOrder='".$order['id']."' and product='".$product['id']."'")->find();
		if($result){
			apiReturn("false4");
		}else{
			$data = array(
					"idOrder"=>$order['id'],
					"product"=>$product['id'],
					"number"=>1,
			);
			$result = $contentModel->add($data);
			if(!$result){
				echo '{
					"status":"false3"
					}';
				return;
			}
		}
		$contents = $contentModel->where("idOrder='".$order['id']."'")->select();
		echo '{
				"status":"ok",
				"idOrder":"'.$order['idorder'].'",
				"content":[';
		for($i = 0;$i < count($contents);$i++){
			$product = $productModel->get($contents[$i]['product']);
			echo '{
					"name":"'.$product['name'].'",
					"code":"'.$product['code'].'",
					"price":"'.$product['priceout'].'",
					"number":"'.$contents[$i]['number'].'"
			}';
			if($i+1 != count($contents)){
				echo ',';
			}
		}
		echo ']
		}';
	}
	public function getOrderByIdOrder(){
		if(!IS_POST){
			apiReturn("false");
			return;
		}
		$idOrder = I('post.idOrder');
		$orderModel = D('Order');
		$order = $orderModel->get($idOrder);
		if(!$order){
			apiReturn("false");
			return;
		}
		$result = array(
				"status"=>"ok",
				"order"=>$order
		);
		apiReturn($result);
		
	}
}