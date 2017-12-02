<?php
namespace ApiNew\Controller;
class ConsoController extends BaseController {
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
	public function newOrder(){
		$dataPost = I('post.');
		$vipModel = M('Vip');
		if(!$vipModel->where(["cardNumber"=>$dataPost['cardNumber']])->find()){
			$this->ajaxReturn(["status"=>false,"message"=>"cardNumber error"]);
			return;
		}
		$dataOrder = [
				"idOrder"=>time().rand(1000,9999),
				"cardNumber"=>$dataPost['cardNumber'],
				"type"=>"product",
				"time"=>date("Y-m-d H:i:s"),
				"status"=>"待支付",
				"serveur"=>$this->user['id'],
		];
		$dataOrder['sold'] = ($dataPost['sold'])?(floatval($dataPost['sold'])/10):1;
		$idRecord = M('Order')->add($dataOrder);
		if(!$idRecord){
			$this->ajaxReturn(["status"=>false,"message"=>"database write error"]);
			return;
		}
		$dataContent = [];
		foreach($dataPost['cart'] as $c){
			$temp = [
					"idOrder"=>$idRecord,
					"product"=>$c['product']['id'],
					"number"=>$c['number']
			];
			array_push($dataContent,$temp);
		}
		M('OrderContent')->addAll($dataContent);
		$this->ajaxReturn(["status"=>true,"id"=>$idRecord]);
	}
	public function getProduct(){
		$model = M('Product');
		$index = I('post.index');
		$where = "id='".$index."' or code='".$index."'";
		$product = $model->where($where)->find();
		if($product){
			$this->ajaxReturn(["status"=>true,"product"=>$product]);
			return;
		}else{
			$this->ajaxReturn(["status"=>false,"message"=>"商品未找到"]);
			return;
		}
	}
	public function getOrder(){
		$id = I('post.id');
		$orderModel = D('Order');
		$contentModel = D('OrderContent');
		$order = $orderModel->where(["id"=>$id])->relation(true)->find();
		if(!$order){
			$this->ajaxReturn(["status"=>false,"message"=>"id error"]);
			return;
		}
		$content = $contentModel->get($id);
		$this->ajaxReturn(["status"=>true,"order"=>$order,"content"=>$content]);
	}
	public function pay(){
		$vip = I('post.vip');
		$order = I('post.order');
		$typePay = I('post.typePay');
		$sum = I('post.sum');
		$pwdPay = I('post.pwdPay');
		if($typePay != "cash" && chiffrement($pwd) != $vip['pwdpay']){
			$this->ajaxReturn(["status"=>false,"message"=>"pwd error"]);
			return;
		}
		if($typePay != "cash"){
			D('Vip')->where(["id"=>$vip["id"]])->setDec($typePay,$sum);
		}
		$data = [
				"idOrder"=>$order["id"],
				$typePay=>$sum,
		];
		M('PointconsoRecord')->add($data);
		$this->ajaxReturn(["status"=>true]);
	}
	public function payFinish(){
		$order = I('post.order');
		$totalPay = I('post.totalPay');
		$data = [
				"status"=>"已完成",
				"priceSold"=>$totalPay['cashPay'],
				"pointExchange"=>$totalPay['pointExchangePay']
		];
		if(in_array($order['vip']['card'], [2,3,4,5])){
			$data['pointExchange'] = 0;
		}
		M('Order')->where(["id"=>$order['id']])->save($data);
		$this->renewProduct($order);
		$this->ajaxReturn(["status"=>true]);
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
}