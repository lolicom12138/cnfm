<?php
namespace Common\Model;
use Think\Model\RelationModel;
class SupplierModel extends RelationModel{
	protected $_link = array(
			'Product'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'product',
					'foreign_key'=>'supplier',
					'mapping_name'=>'product',
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
					"name"=>array(
							"like",
							"%".$id."%",
					),
			);
			$result = $this->relation(true)->where($where)->find();
		}
		return $result;
	}
}
?>