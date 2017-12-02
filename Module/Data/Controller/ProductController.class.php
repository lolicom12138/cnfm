<?php
namespace Data\Controller;
use Data\Common\Controller\Base;
class ProductController extends Base {
    public function addSupplier(){
    	if(!testClassified($this->user,"addSupplier")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!IS_POST){
    		$this->display();
    		return;
    	}
    	$supplierModel = D('Supplier');
    	$data['name'] = I('post.name');
    	if(!$data['name']){
    		$this->error("请输入供货商名称");
    		return;
    	}
    	$supplier = $supplierModel->get($data['name']);
    	if($supplier){
    		$this->error("供货商已存在");
    		return;
    	}
    	$data['adresse'] = I('post.adresse');
    	if(!$data['adresse']){
    		$this->error("请输入供货商地址");
    		return;
    	}
    	$data['responsable'] = I('post.responsable');
    	if(!$data['responsable']){
    		$this->error("请输入供货商负责人");
    		return;
    	}
    	$data['tel'] = I('post.tel');
    	if(!$data['tel']){
    		$this->error("请输入供货商联系电话");
    		return;
    	}
    	$data['etc'] = I('post.etc');
    	$result = $supplierModel->add($data);
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"添加供货商",
    				"content"=>"供货商:".$data['name'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function deleteSupplier($id = null){
    	if(!testClassified($this->user,"deleteSupplier")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$supplierModel = D('Supplier');
    	$supplier = $supplierModel->get($id);
    	if(!$supplier){
    		$this->error("非法访问");
    		return;
    	}
    	if(!IS_POST){
    		$this->assign("supplier",$supplier);
    		$this->display();
    		return;
    	}
    	$where = array(
    			"id"=>$supplier['id'],
    	);
    	$result = $supplierModel->where($where)->delete();
    	if($result){
    		$this->success("成功",U('/data/product/listSupplier'));
    		return;
    	}else{
    		$this->error("失败");
    	}
    }
    public function changeSupplier($id = null){
    	if(!testClassified($this->user,"changeSupplier")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$supplierModel = D('Supplier');
    	$supplier = $supplierModel->get($id);
    	if(!$supplier){
    		$this->error("非法访问");
    		return;
    	}
    	if(!IS_POST){
    		$this->assign("supplier",$supplier);
    		$this->display();
    		return;
    	}
    	$data['name'] = I('post.name');
    	if(!$data['name']){
    		$this->error("请输入供货商名称");
    		return;
    	}
    	$where = array(
    			"id"=>array(
    					"neq",
    					$supplier['id']
    			),
    			"name"=>$data['name'],
    	);
    	$result = $supplierModel->where($where)->find();
    	if($result){
    		$this->error("供货商已存在");
    		return;
    	}
    	$data['adresse'] = I('post.adresse');
    	if(!$data['adresse']){
    		$this->error("请输入供货商地址");
    		return;
    	}
    	$data['responsable'] = I('post.responsable');
    	if(!$data['responsable']){
    		$this->error("请输入供货商负责人");
    		return;
    	}
    	$data['tel'] = I('post.tel');
    	if(!$data['tel']){
    		$this->error("请输入供货商联系电话");
    		return;
    	}
    	$data['etc'] = I('post.etc');
    	$where = array(
    			"id"=>$supplier['id']
    	);
    	$result = $supplierModel->where($where)->save($data);
    	if(is_numeric($result)){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"修改供货商",
    				"content"=>"供货商:".$supplier['name'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功",U('/data/product/listSupplier'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function listSupplier(){
    	if(!testClassified($this->user,"listSupplier")){
    		$this->error("权限不足");
    		return;
    	}
    	$supplierModel = D('Supplier');
    	if(IS_POST){
    		$data['name'] = I('post.name');
    		$data['responsable'] = I('post.responsable');
    		$data['tel'] = I('post.tel');
    		$where = array();
    		if($data['name']){
    			$where['name'] = $data['name'];
    		}
    		if($data['responsable']){
    			$where['responsable'] = array("like",$data['responsable']);
    		}
    		if($data['tel']){
    			$where['tel'] = $data['tel'];
    		}
    		$list = $supplierModel->where($where)->relation(true)->select();
    		$this->assign("data",$data);
    	}else{
    		$list = $supplierModel->relation(true)->select();
    	}
    	$this->assign("list",$list);
    	$this->display();
    }
    public function detailSupplier($id = null){
    	if(!testClassified($this->user,"detailSupplier")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$supplierModel = D('Supplier');
    	$supplier = $supplierModel->get($id);
    	if(!$supplier){
    		$this->error("非法访问");
    		return;
    	}
    	$numberProduct = count($supplier['product']);
    	$this->assign("numberProduct",$numberProduct);
    	$this->assign("supplier",$supplier);
    	$this->display();
    }
    public function addProduct(){
    	if(!testClassified($this->user,"addProduct")){
    		$this->error("权限不足");
    		return;
    	}
    	$classModel = D('ProductClass');
    	$supplierModel = D('Supplier');
    	if(!IS_POST){
    		$listClass = $classModel->select();
    		$listSupplier = $supplierModel->select();
    		$this->assign("class",$listClass);
    		$this->assign("supplier",$listSupplier);
    		$this->display();
    		return;
    	}
    	$productModel = D('Product');
    	$data['name'] = I('post.name');
    	$data['class'] = I('post.class');
    	$data['supplier'] = I('post.supplier');
    	$data['code'] = I('post.code');
    	$data['priceNormal'] = I('post.priceNormal')?I('post.priceNormal'):0;
    	$data['pointExchange'] = I('post.pointExchange')?I('post.pointExchange'):0;
    	$data['priceOut'] = I('post.priceOut');
    	$data['priceOut'] = $data['priceOut']?$data['priceOut']:0;
    	$data['pointOut'] = I('post.pointOut');
    	$data['pointOut'] = $data['pointOut']?$data['pointOut']:0;
    	$data['ifFois'] = I('post.ifFois')?1:0;
    	$data['ifSold'] = I('post.ifSold')?1:0;
    	$data['etc'] = I('post.etc');
    	if(!$data['name']){
    		$this->error("请输入商品名称");
    		return;
    	}
    	if(!$data['class']){
    		$this->error("请选择商品分类");
    		return;
    	}
    	if(!$data['supplier']){
    		$this->error("请输入商品供应商");
    		return;
    	}
    	if(!$data['code']){
    		$this->error("请输入商品编码");
    		return;
    	}
    	$where = array(
    			"name"=>$data['name'],
    	);
    	if($productModel->where($where)->find()){
    		$this->error("商品名称已存在");
    		return;
    	}
    	$where = array(
    			"code"=>$data['code'],
    	);
    	if($productModel->where($where)->find()){
    		$this->error("商品编码已存在");
    		return;
    	}
    	$where = array(
    			"name"=>$data['supplier']
    	);
    	$supplier = $supplierModel->where($where)->find();
    	if(!$supplier){
    		$this->error("供货商信息输入错误");
    		return;
    	}
    	$data['supplier'] = $supplier['id'];
    	$data['recorder'] = $this->user['id'];
    	$data['timeRecord'] = date('Y-m-d H:i:s');
    	$data['dimension'] = I('post.dimension');
    	$data['unit'] = I('post.unit');
    	if(!$data['dimension']){
    		unset($data['dimension']);
    	}
    	if(!$data['unit']){
    		unset($data['unit']);
    	}
    	$result = $productModel->add($data);
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"添加商品",
    				"content"=>"商品:".$data['name'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function deleteProduct($id = null){
    	if(!testClassified($this->user,"deleteProduct")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$productModel = D('Product');
    	$product = $productModel->get($id);
    	if(!$product){
    		$this->error("非法访问");
    		return;
    	}
    	/*
    	if(!IS_POST){
    		$this->assign("product",$product);
    		return;
    	}
    	*/
    	$where = array(
    			"id"=>$product['id'],
    	);
    	$result = $productModel->where($where)->delete();
    	if($result){
    		$this->success("成功",U('/data/product/listProduct'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function changeProduct($id = null){
    	if(!testClassified($this->user,"deleteProduct")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$productModel = D('Product');
    	$classModel = D('ProductClass');
    	$supplierModel = D('Supplier');
    	$product = $productModel->get($id);
    	if(!$product){
    		$this->error("非法访问");
    		return;
    	}
    	if(!IS_POST){
    		$listClass = $classModel->select();
    		$listSupplier = $supplierModel->select();
    		$this->assign("product",$product);
    		$this->assign("class",$listClass);
    		$this->assign("supplier",$listSupplier);
    		$this->display();
    		return;
    	}
    	// TODO
    	$data['name'] = I('post.name');
    	$data['class'] = I('post.class');
    	$data['supplier'] = I('post.supplier');
    	$data['code'] = I('post.code');
    	$data['priceOut'] = I('post.priceOut');
    	$data['pointOut'] = I('post.pointOut');
    	$data['priceNormal'] = I('post.priceNormal');
    	$data['pointExchange'] = I('post.pointExchange');
    	$data['ifSold'] = I('post.ifSold')?I('post.ifSold'):0;
    	$data['ifFois'] = I('post.ifFois')?I('post.ifFois'):0;
    	$data['etc'] = I('post.etc');
    	$data['dimension'] = I('post.dimension');
    	$data['unit'] = I('post.unit');
    	if(!$data['name']){
    		$this->error("请输入商品名称");
    		return;
    	}
    	if(!$data['class']){
    		$this->error("请选择商品分类");
    		return;
    	}
    	if(!$data['supplier']){
    		$this->error("请输入商品供应商");
    		return;
    	}
    	if(!$data['code']){
    		$this->error("请输入商品编码");
    		return;
    	}
    	$where = array(
    			"name"=>$data['name'],
    			"id"=>array(
    					"neq",
    					$product['id']
    			),
    	);
    	if($productModel->where($where)->find()){
    		$this->error("商品名称已存在");
    		return;
    	}
    	$where = array(
    			"code"=>$data['code'],
    			"id"=>array(
    					"neq",
    					$product['id']
    			),
    	);
    	if($productModel->where($where)->find()){
    		$this->error("商品编码已存在");
    		return;
    	}
    	$where = array(
    			"name"=>$data['supplier']
    	);
    	$supplier = $supplierModel->where($where)->find();
    	if(!$supplier){
    		$this->error("供货商信息输入错误");
    		return;
    	}
    	$data['supplier'] = $supplier['id'];
    	$result = $productModel->where(array("id"=>$product['id']))->save($data);
    	if(is_numeric($result)){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"修改商品",
    				"content"=>"商品:".$product['name'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功",U('/data/product/listProduct'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function listProduct(){
    	if(!testClassified($this->user,"listProduct")){
    		$this->error("权限不足");
    		return;
    	}
    	$productModel = D('Product');
    	$classList = M('productClass')->select();
    	$supplierList = M('supplier')->select();
    	if(IS_POST){
    		$where = array(
    				"code"=>I('post.code'),
    				"name"=>array(
    						"like",
    						"%".I('post.name')."%"
    				),
    				"class"=>I('post.class'),
    				"supplier"=>I('post.supplier'),
    		);
    		/*
    		if(!$where['code']){
    			unset($where['code']);
    		}
    		*/
    		foreach($where as $key=>$value){
    			if(!$value){
    				unset($where[$key]);
    			}
    		}
    		$list = $productModel->relation(true)->where($where)->select();
    	}else{
    		$list = array();
    	}
    	$inventoryModel = D('Inventory');
    	for($i = 0;$list[$i];$i++){
    		$list[$i]['taux'] = ($list[$i]['priceout']-$list[$i]['pricein'])/$list[$i]['pricein'];
    		$list[$i]['inventory'] = $inventoryModel->where(array("product"=>$list[$i]['id']))->sum("inventory");
    		$ifInventory = I('post.ifInventory');
  			if(is_numeric($ifInventory)){
  				switch($ifInventory){
  					case 0:
  						if($list[$i]['inventory']){
  							unset($list[$i]);
  						}
  						break;
  					case 1:
  						if(!$list[$i]['inventory']){
  							unset($list[$i]);
  						}
  						break;
  					default:
  						break;
  				}
  			}
    	}
    	$this->assign("numberProduct",count($list));
    	$this->assign("class",$classList);
    	$this->assign("supplier",$supplierList);
    	$this->assign("list",$list);
    	$this->display();
    }
    public function detailProduct($id = null){
    	if(!testClassified($this->user,"detailProduct")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$productModel = D('Product');
    	$product = $productModel->get($id);
    	if(!$product){
    		$this->error("非法访问");
    		return;
    	}
    	$inventoryModel = D('Inventory');
    	$inventory = $inventoryModel->getProductInventory($product['id']);
    	$this->assign("inventory",$inventory);
    	$this->assign("product",$product);
    	$this->display();
    }
    public function addShop(){
    	if(!testClassified($this->user,"addShop")){
    		$this->error("权限不足");
    		return;
    	}
    	$userModel = D('User');
    	if(!IS_POST){
    		$listUser = $userModel->relation(true)->select();
    		$this->assign("user",$listUser);
    		$this->display();
    		return;
    	}
    	$shopModel = D('Shop');
    	$data['name'] = I('post.name');
    	if(!$data['name']){
    		$this->error("请输入店铺名称");
    		return;
    	}
    	$shop = $shopModel->get($data['name']);
    	if($shop){
    		$this->error("已存在的店铺名称");
    		return;
    	}
    	$data['adresse'] = I('post.adresse');
    	if(!$data['adresse']){
    		$this->error("请输入店铺地址");
    		return;
    	}
    	$data['responsable'] = I('post.responsable');
    	if(!$data['responsable']){
    		$this->error("请输入店铺负责人");
    		return;
    	}
    	
    	$responsable = $userModel->get($data['responsable']);
    	if(!$responsable){
    		$this->error("负责人信息输入错误");
    		return;
    	}
    	$data['responsable'] = $responsable['id'];
    	$data['tel'] = I('post.tel');
    	if(!$data['tel']){
    		$this->error("请输入店铺电话");
    		return;
    	}
    	$data['etc'] = I('post.etc');
    	$result = $shopModel->add($data);
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"添加店铺",
    				"content"=>"店铺:".$data['name'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功");
    		return;
    	}else{
    		$this->error("失败");
    	}
    }
    public function deleteShop($id = null){
    	if(!testClassified($this->user,"deleteShop")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$shopModel = D('Shop');
    	$shop = $shopModel->get($id);
    	if(!$shop){
    		$this->error("非法访问");
    		return;
    	}
    	if(!IS_POST){
    		$this->assign("shop",$shop);
    		$this->display();
    		return;
    	}
    	$where = array(
    			"id"=>$shop['id'],
    	);
    	$result = $shopModel->where($where)->delete();
    	if($result){
    		$this->success("成功",U('/data/product/listShop'));
    		return;
    	}else{
    		$this->error("失败");
    	}
    }
    public function changeShop($id = null){
    	if(!testClassified($this->user,"changeShop")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$shopModel = D('Shop');
    	$userModel = D('User');
    	$shop = $shopModel->get($id);
    	if(!$shop){
    		$this->error("非法访问");
    		return;
    	}
    	if(!IS_POST){
    		$listUser = $userModel->relation(true)->select();
    		$this->assign("user",$listUser);
    		$this->assign("shop",$shop);
    		$this->display();
    		return;
    	}
   	 	$data['name'] = I('post.name');
    	if(!$data['name']){
    		$this->error("请输入店铺名称");
    		return;
    	}
    	$where = array(
    			"id"=>array(
    					"neq",
    					$shop['id'],
    			),
    			"name"=>$data['name'],
    	);
    	$result = $shopModel->where($where)->find();
    	if($result){
    		$this->error("已存在的店铺名称");
    		return;
    	}
    	$data['adresse'] = I('post.adresse');
    	if(!$data['adresse']){
    		$this->error("请输入店铺名称");
    		return;
    	}
    	$data['responsable'] = I('post.responsable');
    	if(!$data['responsable']){
    		$this->error("请输入店铺名称");
    		return;
    	}
    	$responsable = $userModel->get($data['responsable']);
    	if(!$responsable){
    		$this->error("负责人信息输入错误");
    		return;
    	}
    	$data['responsable'] = $responsable['id'];
    	$data['tel'] = I('post.tel');
    	if(!$data['tel']){
    		$this->error("请输入店铺名称");
    		return;
    	}
    	$data['etc'] = I('post.etc');
    	$where = array(
    			"id"=>$shop['id']
    	);
    	$result = $shopModel->where($where)->save($data);
    	if(is_numeric($result)){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"修改店铺",
    				"content"=>"店铺:".$shop['name'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功");
    		return;
    	}else{
    		$this->error("失败");
    	}
    }
    public function listShop(){
    	if(!testClassified($this->user,"listShop")){
    		$this->error("权限不足");
    		return;
    	}
    	$shopModel = D('Shop');
    	if(IS_POST){
    		$data['name'] = I('post.name');
    		$data['responsable'] = I('post.responsable');
    		$data['tel'] = I('post.tel');
    		$where = array();
    		if($data['name']){
    			$where['name'] = $data['name'];
    		}
    		if($data['responsable']){
    			$where['responsable'] = array("like","%".$data['responsable']."%");
    		}
    		if($data['tel']){
    			$where['tel'] = $data['tel'];
    		}
    		$list = $shopModel->relation(true)->where($where)->select();
    		$this->assign("data",$data);
    	}else{
    		$list = $shopModel->relation(true)->select();
    	}
    	$this->assign("list",$list);
    	$this->display();
    }
    public function detailShop($id = null){
    	if(!testClassified($this->user,"detailShop")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$shopModel = D('Shop');
    	$shop = $shopModel->get($id);
    	if(!$shop){
    		$this->error("非法访问");
    		return;
    	}
    	$productModel = D('Product');
    	if(I('post.product')){
    		$product = $productModel->get(I('post.product'));
    		if(!$product){
    			unset($shop['inventory']);
    		}
    		foreach($shop['inventory'] as $key=>$value){
    			if($product['id'] != $value['product']){
    				unset($shop['inventory'][$key]);
    			}
    		}
    	}
    	if(I('post.inventoryMin')){
    		foreach($shop['inventory'] as $key=>$value){
    			if($value['inventory'] < I('post.inventoryMin')){
    				unset($shop['inventory'][$key]);
    			}
    		}
    	}
    	if(I('post.inventoryMax')){
    		foreach($shop['inventory'] as $key=>$value){
    			if($value['inventory'] > I('post.inventoryMax')){
    				unset($shop['inventory'][$key]);
    			}
    		}
    	}
    	foreach($shop['inventory'] as $key=>$value){
    		$product = $productModel->get($value['product']);
    		$shop['inventory'][$key]['product'] = $product;
    		$i++;
    	}
    	$numberProduct = 0;
    	$totalPriceOut = 0;
    	foreach($shop['inventory'] as $s){
    		if($s['product']){
    			$numberProduct++;
    			$totalPriceOut += $s['product']['priceout']*$s['inventory'];
    		}
    	}
    	$this->assign("total",$totalPriceOut);
    	$this->assign("numberProduct",$numberProduct);
    	$this->assign("shop",$shop);
    	$this->display();
    }
    public function detailSelfShop(){
    	if(!testClassified($this->user,"detailSelfShop")){
    		$this->error("权限不足");
    		return;
    	}
    	$id = $this->user['departement']['shop'];
    	if(!$id){
    		$this->error("所属的部门没有绑定店铺");
    	}
    	$shopModel = D('Shop');
    	$shop = $shopModel->get($id);
    	if(!$shop){
    		$this->error("非法访问");
    		return;
    	}
    	$productModel = D('Product');
    	if(I('post.product')){
    		$product = $productModel->get(I('post.product'));
    		if(!$product){
    			unset($shop['inventory']);
    		}
    		foreach($shop['inventory'] as $key=>$value){
    			if($product['id'] != $value['product']){
    				unset($shop['inventory'][$key]);
    			}
    		}
    	}
    	if(I('post.inventoryMin')){
    		foreach($shop['inventory'] as $key=>$value){
    			if($value['inventory'] < I('post.inventoryMin')){
    				unset($shop['inventory'][$key]);
    			}
    		}
    	}
    	if(I('post.inventoryMax')){
    		foreach($shop['inventory'] as $key=>$value){
    			if($value['inventory'] > I('post.inventoryMax')){
    				unset($shop['inventory'][$key]);
    			}
    		}
    	}
    	foreach($shop['inventory'] as $key=>$value){
    		$product = $productModel->get($value['product']);
    		$shop['inventory'][$key]['product'] = $product;
    		$i++;
    	}
    	$numberProduct = 0;
    	$totalPriceOut = 0;
    	foreach($shop['inventory'] as $s){
    		if($s['product']){
    			$numberProduct++;
    			$totalPriceOut += $s['product']['priceout']*$s['inventory'];
    		}
    	}
    	$this->assign("total",$totalPriceOut);
    	$this->assign("numberProduct",$numberProduct);
    	$this->assign("shop",$shop);
    	$this->display();
    }
    public function storageProduct(){
    	if(!testClassified($this->user,"storageProduct")){
    		$this->error("权限不足");
    		return;
    	}
    	$shopModel = D('Shop');
    	
    	if(!IS_POST){
    		$listShop = $shopModel->relation(true)->select();
    		$this->assign("shop",$listShop);
    		$this->display();
    		return;
    	}
    	$data['product'] = I('post.product');
    	
    	$productModel = D('Product');
    	$product = $productModel->get($data['product']);
    	if(!$product){
    		$this->error("商品信息输入错误");
    		return;
    	}
    	$data['product'] = $product['id'];
    	$type = I('post.type');
    	$data['nombre'] = I('post.nombre');
    	$data['etc'] = I('post.etc');
    	$data['time'] = date("Y-m-d H:i:s");
    	$data['operator'] = $this->user['id'];
    	$data['confirmer'] = I('post.confirmer');
    	
    	switch($type){
    		case "in":
    			$recordModel = M('inventoryInRecord');
    			$data['shop'] = 1;
    			$data['priceIn'] = I('post.priceIn');
    			if(!$data['priceIn']){
    				$data['priceIn'] = 0;
    			}
    			$recordResult = $recordModel->add($data);
    			if($recordResult){
    				$inventoryModel = M('inventory');
    				$where = array(
    						"shop"=>$data['shop'],
    						"product"=>$data['product'],
    				);
    				$inventory = $inventoryModel->where($where)->find();
    				if($inventory){
    					$inventoryData = array(
    							"inventory"=>$inventory['inventory']+$data['nombre'],
    					);
    					$where = array(
    							"id"=>$inventory['id'],
    					);
    					$result = $inventoryModel->where($where)->save($inventoryData);
    					if($result){
    						$this->success("成功");
    					}else{
    						$recordModel->where(array("id"=>$recordResult))->delete();
    						$this->error("失败");
    					}
    				}else{
    					$inventoryData = array(
    							"shop"=>$data['shop'],
    							"product"=>$data['product'],
    							"inventory"=>$data['nombre'],
    					);
    					$result = $inventoryModel->add($inventoryData);
    					if($result){
    						$this->success("成功");
    					}else{
    						$recordModel->where(array("id"=>$recordResult))->delete(); 
    						$this->error("失败");
    					}
    				}
    			}else{
    				$this->error("失败");
    			}
    			break;
    		case "out":
    			$recordModel = M('inventoryOutRecord');
    			$data['shop'] = I('post.shop');
    			$data['priceOut'] = $product['priceout'];
    			$inventoryModel = M('inventory');
    			$where = array(
    					"shop"=>$data['shop'],
    					"product"=>$data['product'],
    			);
    			$inventory = $inventoryModel->where($where)->find();
    			if(!$inventory){
    				$this->error("该店铺中没有此商品");
    				return;
    			}
    			/*
    			if($inventory['inventory'] < $data['nombre']){
    				$this->error("该店铺中此商品的库存不足");
    				return;
    			}
    			*/
    			$inventoryData['inventory'] = $inventory['inventory']-$data['nombre'];
    			$result = $inventoryModel->where($where)->save($inventoryData);
    			if(is_numeric($result)){
    				$result = $recordModel->add($data);
    				if($result){
    					$this->success("成功");
    				}else{
    					$inventoryData['inventory'] = $inventory['inventory'];
    					$inventoryModel->where($where)->save($inventoryData);
    					$this->error("失败");
    				}
    			}else{
    				$this->error("失败");
    			}
    			break;
    	}
    }
    public function addProductClass(){
    	if(!testClassified($this->user,"addProductClass")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!IS_POST){
    		$this->display();
    		return;
    	}
    	$data['class'] = I('post.class');
    	if(!$data['class']){
    		$this->error("请输入分类名称");
    	}
    	$productClassModel = D('ProductClass');
    	$where = array(
    			"class"=>$data['class']
    	);
    	$result = $productClassModel->where($where)->find();
    	if($result){
    		$this->error("分类名重复");
    		return;
    	}
    	$result = $productClassModel->add($data);
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"添加商品分类",
    				"content"=>"分类:".$data['class'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function listProductClass(){
    	if(!testClassified($this->user,"listProductClass")){
    		$this->error("权限不足");
    		return;
    	}
    	$productClassModel = D('ProductClass');
    	$list = $productClassModel->select();
    	$this->assign("list",$list);
    	$this->display();
    }
    public function changeProductClass($id = null){
    	if(!testClassified($this->user,"changeProductClass")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$productClassModel = D('ProductClass');
    	$where = array(
    			"id"=>$id
    	);
    	$class = $productClassModel->where($where)->find();
    	if(!$class){
    		$this->error("非法访问");
    		return;
    	}
    	if(!IS_POST){
    		$this->assign("class",$class);
    		$this->display();
    		return;
    	}
    	$data['class'] = I('post.class');
    	if(!$data['class']){
    		$this->error("请输入分类名称");
    	}
    	$productClassModel = D('ProductClass');
    	$where = array(
    			"id"=>array(
    					"neq",
    					$class['id']
    			),
    			"class"=>$data['class']
    	);
    	$result = $productClassModel->where($where)->find();
    	if($result){
    		$this->error("分类名重复");
    		return;
    	}
    	$where = array(
    			"id"=>$class['id']
    	);
    	$result = $productClassModel->where($where)->save($data);
    	if(is_numeric($result)){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"修改商品分类",
    				"content"=>"分类:".$class['class'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function deleteProductClass($id = null){
    	if(!testClassified($this->user,"deleteProductClass")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$productClassModel = D('ProductClass');
    	$where = array(
    			"id"=>$id
    	);
    	$class = $productClassModel->where($where)->find();
    	if(!$class){
    		$this->error("非法访问");
    		return;
    	}
    	$result = $productClassModel->where($where)->delete();
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"删除商品分类",
    				"content"=>"分类:".$class['class'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功",U('/data/product/listProductClass'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function detailProductClass($id = null){
    	if(!testClassified($this->user,"detailProductClass")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$productClassModel = D('ProductClass');
    	$where = array(
    			"id"=>$id
    	);
    	$class = $productClassModel->where($where)->find();
    	if(!$class){
    		$this->error("非法访问");
    		return;
    	}
    	$productModel = D('Product');
    	$class['product'] = $productModel->where("class='".$class['id']."'")->select();
    	$numberProduct = count($class['product']);
    	$this->assign("numberProduct",$numberProduct);
    	$this->assign("class",$class);
    	$this->display();
    }
    public function listInventoryIn(){
    	if(!testClassified($this->user,"listInventoryIn")){
    		$this->error("权限不足");
    		return;
    	}
    	$recordModel = D('InventoryInRecord');
    	if(!IS_POST){
    		/*
    		$list = $recordModel->relation(true)->select();
    		*/
    	}else{
    		$shop = I('post.shop');
    		$product = I('post.product');
    		$timeBegin = I('post.timeBegin');
    		$timeEnd = I('post.timeEnd');
    		$operator = I('post.operator');
    		$confirmer = I('post.confirmer');
    		$where = array(
    				"time"=>array()
    		);
    		if($shop){
    			$shopModel = D('Shop');
    			$shop = $shopModel->get($shop);
    			$where['shop'] = $shop?$shop['id']:0;
    		}
    		if($product){
    			$productModel = D('Product');
    			$product = $productModel->get($product);
    			$where['product'] = $product?$product['id']:0;
    		}
    		if($timeBegin){
    			$temp = array(
    					"EGT",
    					$timeBegin,
    			);
    			array_push($where['time'],$temp);
    		}
    		if($timeEnd){
    			$date = explode("-",$timeEnd);
    			$date[2]++;
    			$temp = array(
    					"ELT",
    					$date[0]."-".$date[1]."-".$date[2],
    			);
    			array_push($where['time'],$temp);
    		}
    		if($timeBegin && $timeEnd && $timeBegin == $timeEnd){
    			$where['time'] = array(
    					"like",
    					"%".$timeBegin."%"
    			);
    		}
    		if($operator){
    			$userModel = D('User');
    			$user = $userModel->get($operator);
    			$where['operator'] = $user?$user['id']:0;
    		}
    		if($confirmer){
    			$where['confirmer'] = array(
    					"like",
    					"%".$confirmer."%"
    			);
    		}
    		if(!count($where['time'])){
    			unset($where['time']);
    		}
    		$list = $recordModel->relation(true)->where($where)->select();
    	}
    	if($list){
    		$total = 0;
    		$totalNombre = 0;
    		foreach($list as $l){
    			$total += $l['nombre']*$l['product']['priceout'];
    			$totalNombre += $l['nombre'];
    		}
    		$this->assign("total",$total);
    		$this->assign("totalNombre",$totalNombre);
    	}
    	$this->assign("list",$list);
    	$this->display();
    }
    public function listInventoryInSelf(){
    	if(!testClassified($this->user,"listInventoryInSelf")){
    		$this->error("权限不足");
    		return;
    	}
    	$recordModel = D('InventoryInRecord');
    	if(!IS_POST){
    		/*
    		 $list = $recordModel->relation(true)->select();
    		 */
    	}else{
    		$shop = I('post.shop');
    		$product = I('post.product');
    		$timeBegin = I('post.timeBegin');
    		$timeEnd = I('post.timeEnd');
    		$operator = I('post.operator');
    		$confirmer = I('post.confirmer');
    		$where = array(
    				"time"=>array()
    		);
    		if($shop){
    			$shopModel = D('Shop');
    			$shop = $shopModel->get($shop);
    			$where['shop'] = $shop?$shop['id']:0;
    		}
    		if($product){
    			$productModel = D('Product');
    			$product = $productModel->get($product);
    			$where['product'] = $product?$product['id']:0;
    		}
    		if($timeBegin){
    			$temp = array(
    					"EGT",
    					$timeBegin,
    			);
    			array_push($where['time'],$temp);
    		}
    		if($timeEnd){
    			$date = explode("-",$timeEnd);
    			$date[2]++;
    			$temp = array(
    					"ELT",
    					$date[0]."-".$date[1]."-".$date[2],
    			);
    			array_push($where['time'],$temp);
    		}
    		if($timeBegin && $timeEnd && $timeBegin == $timeEnd){
    			$where['time'] = array(
    					"like",
    					"%".$timeBegin."%"
    			);
    		}
    		$where['operator'] = $this->user['id'];
    		if($confirmer){
    			$where['confirmer'] = array(
    					"like",
    					"%".$confirmer."%"
    			);
    		}
    		if(!count($where['time'])){
    			unset($where['time']);
    		}
    		$list = $recordModel->relation(true)->where($where)->select();
    	}
    	if($list){
    		$total = 0;
    		$totalNombre = 0;
    		foreach($list as $l){
    			$total += $l['nombre']*$l['product']['priceout'];
    			$totalNombre += $l['nombre'];
    		}
    		$this->assign("total",$total);
    		$this->assign("totalNombre",$totalNombre);
    	}
    	$this->assign("list",$list);
    	$this->display();
    }
    public function listInventoryOut(){
    	if(!testClassified($this->user,"listInventoryOut")){
    		$this->error("权限不足");
    		return;
    	}
    	$recordModel = D('InventoryOutRecord');
    	if(!IS_POST){
    		/*
    		$list = $recordModel->relation(true)->select();
    		*/
    	}else{
    		$shop = I('post.shop');
    		$product = I('post.product');
    		$timeBegin = I('post.timeBegin');
    		$timeEnd = I('post.timeEnd');
    		$operator = I('post.operator');
    		$confirmer = I('post.confirmer');
    		$where = array(
    				"time"=>array()
    		);
    		if($shop){
    			$shopModel = D('Shop');
    			$shop = $shopModel->get($shop);
    			$where['shop'] = $shop?$shop['id']:0;
    		}
    		if($product){
    			$productModel = D('Product');
    			$product = $productModel->get($product);
    			$where['product'] = $product?$product['id']:0;
    		}
    		if($timeBegin){
    			$temp = array(
    					"EGT",
    					$timeBegin,
    			);
    			array_push($where['time'],$temp);
    		}
    		if($timeEnd){
    			$date = explode("-",$timeEnd);
    			$date[2]++;
    			$temp = array(
    					"ELT",
    					$date[0]."-".$date[1]."-".$date[2],
    			);
    			array_push($where['time'],$temp);
    		}
    		if($timeBegin && $timeEnd && $timeBegin == $timeEnd){
    			$where['time'] = array(
    					"like",
    					"%".$timeBegin."%"
    			);
    		}
    		if($operator){
    			$userModel = D('User');
    			$user = $userModel->get($operator);
    			$where['operator'] = $user?$user['id']:0;
    		}
    		if($confirmer){
    			$where['confirmer'] = array(
    					"like",
    					"%".$confirmer."%"
    			);
    		}
    		if(!count($where['time'])){
    			unset($where['time']);
    		}
    		$list = $recordModel->relation(true)->where($where)->select();
    		
    	}
    	if($list){
    		$total = 0;
    		$totalNombre = 0;
    		foreach($list as $l){
    			$total += $l['nombre']*$l['product']['priceout'];
    			$totalNombre += $l['nombre'];
    		}
    		$this->assign("total",$total);
    		$this->assign("totalNombre",$totalNombre);
    	}
    	$this->assign("list",$list);
    	$this->display();
    }
    public function listInventoryOutSelf(){
    	if(!testClassified($this->user,"listInventoryOutSelf")){
    		$this->error("权限不足");
    		return;
    	}
    	$recordModel = D('InventoryOutRecord');
    	if(!IS_POST){
    		/*
    		 $list = $recordModel->relation(true)->select();
    		 */
    	}else{
    		$shop = I('post.shop');
    		$product = I('post.product');
    		$timeBegin = I('post.timeBegin');
    		$timeEnd = I('post.timeEnd');
    		$operator = I('post.operator');
    		$confirmer = I('post.confirmer');
    		$where = array(
    				"time"=>array()
    		);
    		if($shop){
    			$shopModel = D('Shop');
    			$shop = $shopModel->get($shop);
    			$where['shop'] = $shop?$shop['id']:0;
    		}
    		if($product){
    			$productModel = D('Product');
    			$product = $productModel->get($product);
    			$where['product'] = $product?$product['id']:0;
    		}
    		if($timeBegin){
    			$temp = array(
    					"EGT",
    					$timeBegin,
    			);
    			array_push($where['time'],$temp);
    		}
    		if($timeEnd){
    			$date = explode("-",$timeEnd);
    			$date[2]++;
    			$temp = array(
    					"ELT",
    					$date[0]."-".$date[1]."-".$date[2],
    			);
    			array_push($where['time'],$temp);
    		}
    		if($timeBegin && $timeEnd && $timeBegin == $timeEnd){
    			$where['time'] = array(
    					"like",
    					"%".$timeBegin."%"
    			);
    		}
    		$where['operator'] = $this->user['id'];
    		if($confirmer){
    			$where['confirmer'] = array(
    					"like",
    					"%".$confirmer."%"
    			);
    		}
    		if(!count($where['time'])){
    			unset($where['time']);
    		}
    		$list = $recordModel->relation(true)->where($where)->select();
    
    	}
    	if($list){
    		$total = 0;
    		$totalNombre = 0;
    		foreach($list as $l){
    			$total += $l['nombre']*$l['product']['priceout'];
    			$totalNombre += $l['nombre'];
    		}
    		$this->assign("total",$total);
    		$this->assign("totalNombre",$totalNombre);
    	}
    	$this->assign("list",$list);
    	$this->display();
    }
    public function allocationProduct(){
    	if(!testClassified($this->user,"allocationProduct")){
    		$this->error("权限不足");
    		return;
    	}
    	$shopModel = D('Shop');
    	if(!IS_POST){
    		$listShop = $shopModel->select();
    		$this->assign("shop",$listShop);
    		$this->display();
    		return;
    	}
    	$confirmer = I('post.confirmer');
    	$sum = I('post.sum');
    	$idProduct = I('post.product');
    	$productModel = D('Product');
    	$product = $productModel->get($idProduct);
    	if(!$product){
    		$this->error("商品信息输入错误");
    		return;
    	}
    	$idShopIn = I('post.shopIn');
    	$idShopOut = I('post.shopOut');
    	
    	$shopIn = $shopModel->get($idShopIn);
    	$shopOut = $shopModel->get($idShopOut);
    	if(!$shopIn || !$shopOut){
    		$this->error("店铺选择有误");
    		return;
    	}
    	$inventoryModel = D('Inventory');
    	$inventoryInShop = $inventoryModel->where(array("shop"=>$shopIn['id'],"product"=>$product['id']))->relation(true)->find();
    	$inventoryOutShop = $inventoryModel->where(array("shop"=>$shopOut['id'],"product"=>$product['id']))->relation(true)->find();
    	if(!$inventoryOutShop || $inventoryOutShop['inventory'] < $sum){
    		$this->error("出库店铺没有此商品或存量不足");
    		return;
    	}
    	if($inventoryInShop){
    		$data = array(
    				"inventory"=>$inventoryInShop['inventory']+$sum
    		);
    		$ok = $inventoryModel->where(array("id"=>$inventoryInShop['id']))->save($data);
    	}else{
    		$data = array(
    				"shop"=>$shopIn['id'],
    				"product"=>$product['id'],
    				"inventory"=>$sum
    		);
    		$ok = $inventoryModel->add($data);
    	}
    	if(!$ok){
    		$this->error("错误");
    		return;
    	}
    	$data = array(
    			"inventory"=>$inventoryOutShop['inventory']-$sum,
    	);
    	$ok2 = $inventoryModel->where(array("id"=>$inventoryOutShop['id']))->save($data);
    	if(!is_numeric($ok2)){
    		if($inventoryInShop){
    			$data = array(
    					"inventory"=>$inventoryInShop['inventory']
    			);
    			$inventoryModel->where(array("id"=>$inventoryInShop['id']))->save($data);
    		}else{
    			$inventoryModel->where(array("id"=>$ok))->delete();
    		}
    		$this->error("错误2");
    		return;
    	}
    	$recordInModel = D('InventoryInRecord');
    	$recordOutModel = D('InventoryOutRecord');
    	$dataOut = array(
    			"shop"=>$shopOut['id'],
    			"product"=>$product['id'],
    			"nombre"=>$sum,
    			"operator"=>$this->user['id'],
    			"confirmer"=>$confirmer,
    			"time"=>date("Y-m-d H:i:s"),
    			"etc"=>"库存调拨,入:".$shopIn['name']
    	);
    	$dataIn = array(
    			"shop"=>$shopIn['id'],
    			"product"=>$product['id'],
    			"nombre"=>$sum,
    			"time"=>date("Y-m-d H:i:s"),
    			"operator"=>$this->user['id'],
    			"confirmer"=>$confirmer,
    			"etc"=>"库存调拨，出:".$shopOut['name']
    	);
    	$recordInModel->add($dataIn);
    	$recordOutModel->add($dataOut);
    	$this->success("成功");
    }
    public function listAllocation(){
    	if(!testClassified($this->user,"listAllocation")){
    		$this->error("权限不足");
    		return;
    	}
    	$shopModel = D('Shop');
    	$type = I('post.type');
    	$timeBegin = I('post.timeBegin');
    	$timeEnd = I('post.timeEnd');
    	$shop = I('post.shop');
    	$operator = I('post.operator');
    	$product = I('post.product');
    	if($shop){
    		$shop = $shopModel->get($shop);
    		if(!$shop){
    			$this->error("店铺信息输入错误");
    			return;
    		}
    	}
    	if($product){
    		$productModel = D('Product');
    		$product = $productModel->get($product);
    		if(!$product){
    			$this->error("商品信息输入错误");
    			return;
    		}
    	}
    	switch($type){
    		case "in":
    			$list = $this->listAllocationIn($timeBegin, $timeEnd, $shop,$product,$operator);
    			break;
    		case "out":
    			$list = $this->listAllocationOut($timeBegin, $timeEnd, $shop,$product,$operator);
    			break;
    	}
    	$total = 0;
    	$totalNombre = 0;
    	if($list){
    		foreach($list as $key=>$value){
    			$totalNombre += $value['nombre'];
    			$list[$key]['total'] = $value['product']['priceout']*$value['nombre'];
    			$total += $list[$key]['total'];
    		}
    	}
    	$count = count($list);
    	$this->assign("count",$count);
    	$this->assign("list",$list);
    	$this->assign("total",$total);
    	$this->assign("totalNombre",$totalNombre);
    	$this->display();
    }
    private function listAllocationIn($timeBegin,$timeEnd,$shop,$product,$operator){
    	$where = array(
    			"etc"=>array(
    					"like",
    					"%库存调拨%"
    			),
    			"time"=>array()
    	);
    	if($operator){
    		$userModel = D('User');
    		$userList = $userModel->where(array("name"=>$operator))->select();
    		if(!count($userList)){
    			return array();
    		}
    		if(count($userList) == 1){
    			$where['operator'] = $userList[0]['id'];
    		}else{
    			$temp = array();
    			foreach($userList as $u){
    				array_push($temp,$u['id']);
    			}
    			$where['operator'] = array(
    					"in",
    					$temp
    			);
    		}
    	}
    	if($product){
    		$where['product'] = $product['id'];
    	}
    	if($shop){
    		$where['shop'] = $shop['id'];
    	}
    	if($timeBegin){
    		$temp = array(
    				"EGT",
    				$timeBegin
    		);
    		array_push($where['time'],$temp);
    	}
    	if($timeEnd){
    		$date = explode("-",$timeEnd);
    			$date[2]++;
    			$temp = array(
    					"ELT",
    					$date[0]."-".$date[1]."-".$date[2],
    			);
    			array_push($where['time'],$temp);
    	}
    	if($timeEnd == $timeBegin){
    		$where['time'] = array(
    				"like",
    				"%".$timeBegin."%"
    		);
    	}
    	return D('InventoryInRecord')->where($where)->relation(true)->select();
    }
    private function listAllocationOut($timeBegin,$timeEnd,$shop,$product,$operator){
    	$where = array(
    			"etc"=>array(
    					"like",
    					"%库存调拨%"
    			),
    			"time"=>array()
    	);
    	if($operator){
    		$userModel = D('User');
    		$userList = $userModel->where(array("name"=>$operator))->select();
    		if(!count($userList)){
    			return array();
    		}
    		if(count($userList) == 1){
    			$where['operator'] = $userList[0]['id'];
    		}else{
    			$temp = array();
    			foreach($userList as $u){
    				array_push($temp,$u['id']);
    			}
    			$where['operator'] = array(
    					"in",
    					$temp
    			);
    		}
    	}
    	if($shop){
    		$where['shop'] = $shop['id'];
    	}
    	if($product){
    		$where['product'] = $product['id'];
    	}
    	if($timeBegin){
    		$temp = array(
    				"EGT",
    				$timeBegin
    		);
    		array_push($where['time'],$temp);
    	}
    	if($timeEnd){
    		$date = explode("-",$timeEnd);
    			$date[2]++;
    			$temp = array(
    					"ELT",
    					$date[0]."-".$date[1]."-".$date[2],
    			);
    			array_push($where['time'],$temp);
    	}
    	if($timeEnd == $timeBegin){
    		$where['time'] = array(
    				"like",
    				"%".$timeBegin."%"
    		);
    	}
    	return D('InventoryOutRecord')->where($where)->relation(true)->select();
    }
}