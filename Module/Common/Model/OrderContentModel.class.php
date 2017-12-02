<?php
namespace Common\Model;
use Think\Model\RelationModel;
class OrderContentModel extends RelationModel{
	protected $_link = array(
			/*
			'Content'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'orderContent',
					'foreign_key'=>'idOrder',
					'mapping_name'=>'content',
			),
			'Vip'=>array(
					'mapping_type'=>self::HAS_ONE,
					'class_name'=>'vip',
					'foreign_key'=>'cardNumber',
					'mapping_key'=>'cardnumber',
					//'parent_key'=>'cardnumber',
					'mapping_name'=>'vip',
			),
			*/
			'Order'=>array(
					"mapping_type"=>self::BELONGS_TO,
					"class_name"=>"order",
					"foreign_key"=>"idorder",
					"mapping_name"=>"order",
			),
			"Product"=>array(
					"mapping_type"=>self::BELONGS_TO,
					"class_name"=>"product",
					"foreign_key"=>"product",
					"mapping_name"=>"product",
			),
	);
	public function get($id){
		if(!$id){
			return;
		}
		$where = array(
				"idOrder"=>$id,
		);
		$result = $this->relation(true)->where($where)->select();
		return $result;
	}
	public function getById($id){
		if(!$id){
			return;
		}
		$where = array(
				"id"=>$id,
		);
		return $this->relation(true)->where($where)->find();
	}
}
?>