<?php
namespace Common\Model;
use Think\Model\RelationModel;
class ProductClassModel extends RelationModel{
	protected $_link = array(
			'Product'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'product',
					'foreign_key'=>'class',
					'mapping_name'=>'product',
			)
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
					"class"=>array(
							"like",
							"%".$id."%"
					),
			);
			$result = $this->relation(true)->where($where)->find();
		}
		return $result;
	}
}
?>