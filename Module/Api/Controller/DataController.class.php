<?php
namespace Api\Controller;
use Think\Controller;
class DataController extends  Controller{
	function deleteProduct(){
		$id = I('post.id');
		if(!$id){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$productModel = D("Product");
		$product = $productModel->get($id);
		if(!$product){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $productModel->where($where)->delete();
		if($result){
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"删除商品",
					"content"=>"商品:".$product['name'],
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
	function deleteSupplier(){
		$id = I('post.id');
		if(!$id){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$supplierModel = D("Supplier");
		$supplier = $supplierModel->get($id);
		if(!$supplier){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $supplierModel->where($where)->delete();
		if($result){
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"删除供货商",
					"content"=>"供货商:".$supplier['name'],
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
	function deleteShop(){
		$id = I('post.id');
		if(!$id){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$shopModel = D("Shop");
		$shop = $shopModel->get($id);
		if(!$shop){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $shopModel->where($where)->delete();
		if($result){
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"删除店铺",
					"content"=>"店铺:".$shop['name'],
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