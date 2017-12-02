<?php
namespace Common\Model;
use Think\Model\RelationModel;
class VipModel extends RelationModel{
	protected $_link = array(
			'Client'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'client',
					'foreign_key'=>'client',
					'mapping_name'=>'client',
			),
			'Card'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'card',
					'foreign_key'=>'card',
					'mapping_name'=>'card',
			),
			'Consomation'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'recordConsomation',
					'foreign_key'=>'cardNumber',
					//'mapping_key'=>'cardnumber',
					'parent_key'=>'cardnumber',
					'mapping_name'=>'consomation',
			),
			/*
			'Recharger'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'recharger',
					'foreign_key'=>'cardNumber',
					//'mapping_key'=>'cardnumber',
					'parent_key'=>'cardnumber',
					'mapping_name'=>'recharger',
			),
			*/
			'Recharger'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'recharger',
					'foreign_key'=>'vip',
					'mapping_name'=>'recharger',
			),
	);
	public function get($id){
		if(!$id){
			return;
		}
		if(is_string($id) && strlen($id) == 8){
			$where = array(
					"cardNumber"=>$id,
			);
			$result = $this->relation(true)->where($where)->find();
			if($result){
				$orderModel = D('Order');
				$result['order'] = $orderModel->relation("Content")->where(array("cardNumber"=>$result['cardnumber']))->select();
			}
			return $result;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $this->relation(true)->where($where)->find();
		if(!$result){
			$where = array(
					"cardNumber"=>$id,
			);
			$result = $this->relation(true)->where($where)->find();
		}
		if($result){
			$orderModel = D('Order');
			$result['order'] = $orderModel->relation("Content")->where(array("cardNumber"=>$result['cardnumber']))->select();
		}
		return $result;
	}
}
?>