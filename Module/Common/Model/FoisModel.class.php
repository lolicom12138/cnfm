<?php
namespace Common\Model;
use Think\Model\RelationModel;
/**
 * 
 * @author LI Yuan
 *“活动”模型。
 */
class FoisModel extends RelationModel{
	protected $_link = array(
			'Client'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'client',
					'mapping_name'=>'client',
					'foreign_key'=>'client',
			),
			'Product'=>array(
					"mapping_type"=>self::BELONGS_TO,
					"class_name"=>"product",
					"foreign_key"=>"product",
					"mapping_name"=>"product",
			),
	);
	public function getByClientId($id){
		if(!$id){
			return;
		}
		$where = array(
				"client"=>$id,
		);
		$result = $this->relation(true)->where($where)->find();
		return $result;
	}
	public function get($id){
		if(!$id){
			return;
		}
		$where = array(
				"id"=>$id
		);
		return $this->relation(true)->where($where)->find();
	}
}
?>