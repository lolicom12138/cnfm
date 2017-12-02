<?php
namespace Common\Model;
use Think\Model\RelationModel;
class DepartementModel extends RelationModel{
	protected $_link = array(
			'Worker'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'user',
					'foreign_key'=>'departement',
					'mapping_name'=>'worker',
			),
			"Shop"=>array(
					"mapping_type"=>self::BELONGS_TO,
					"class_name"=>"shop",
					"foreign_key"=>"shop",
					"mapping_name"=>"shop"
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
					"departement"=>array(
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