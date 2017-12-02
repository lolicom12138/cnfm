<?php
namespace Common\Model;
use Think\Model\RelationModel;
class InventoryModel extends RelationModel{
	protected $_link = array(
			"Shop"=>array(
					"mapping_type"=>self::BELONGS_TO,
					"class_name"=>"shop",
					"foreign_key"=>"shop",
					"mapping_name"=>"shop",
			),
			"Product"=>array(
					"mapping_type"=>self::BELONGS_TO,
					"class_name"=>"product",
					"foreign_key"=>"product",
					"mapping_name"=>"product",
			),
	);
	public function getProductInventory($id = null){
		if(!$id){
			return false;
		}
		$where = array(
				"product"=>$id
		);
		$result = $this->relation(true)->where($where)->select();
		return $result;
	}
	public function getShopInventory($id = null){
		if(!$id){
			return false;
		}
		$where = array(
				"shop"=>$id
		);
		$result = $this->relation(true)->where($where)->select();
		return $result;
	}
	public function getShopProductInventory($idshop = null,$idproduct = null){
		if(!$idshop || !$idproduct){
			return false;
		}
		$where = array(
				"product"=>$idproduct,
				"shop"=>$idshop
		);
		$result = $this->relation(true)->where($where)->find();
		return $result;
	}
}
?>