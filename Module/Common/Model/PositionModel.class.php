<?php
namespace Common\Model;
use Think\Model\RelationModel;
class PositionModel extends RelationModel{
	protected $_link = array(
			'Responsable'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'user',
					'foreign_key'=>'responsable',
					'mapping_name'=>'responsable',
			),
			'Activity'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'activity',
					'foreign_key'=>'position',
					'mapping_name'=>'activity',
			),
			'ClientGroup'=>array(
					'mapping_type'=>self::MANY_TO_MANY,
					'class_name'=>'clientGroup',
					'mapping_name'=>'clientgroup',
					'foreign_key'=>'position',
					'relation_foreign_key'=>'group',
					'relation_table'=>'cnfm_group_position',
			),
			'Client'=>array(
					'mapping_type'=>self::MANY_TO_MANY,
					'class_name'=>'client',
					'mapping_name'=>'client',
					'foreign_key'=>'position',
					'relation_foreign_key'=>'client',
					'relation_table'=>'cnfm_client_go_position',
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
		return $result;
	}
}
?>