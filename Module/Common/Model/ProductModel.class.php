<?php
namespace Common\Model;
use Think\Model\RelationModel;
class ProductModel extends RelationModel{
	protected $_link = array(
			'Recorder'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'user',
					'foreign_key'=>'recorder',
					'mapping_name'=>'recorder',
			),
			'Class'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'productClass',
					'foreign_key'=>'class',
					'mapping_name'=>'class',
			),
			'Inventory'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'inventory',
					'foreign_key'=>'product',
					'mapping_name'=>'inventory',
			),
			'In'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'inventoryInRecord',
					'foreign_key'=>'product',
					'mapping_name'=>'in',
			),
			'Out'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'inventoryOutRecord',
					'foreign_key'=>'product',
					'mapping_name'=>'out',
			),
			'Supplier'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'supplier',
					'foreign_key'=>'supplier',
					'mapping_name'=>'supplier',
			),
	);
	public function get($id){
		if(!$id){
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $this->relation(true)->where($where)->find();
		if(!$result){
			$where = array(
					"code"=>$id,
			);
			$result = $this->relation(true)->where($where)->find();
		}
		return $result;
	}
}
?>