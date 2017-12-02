<?php
namespace Common\Model;
use Think\Model\RelationModel;
/**
 * 
 * @author Administrator
 *互助组模型
 */
class ClientGroupModel extends RelationModel{
	protected $_link = array(
			'Responsable'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'user',
					'foreign_key'=>'responsable',
					'mapping_name'=>'responsable',
			),
			/*
			'Member'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'client',
					'foreign_key'=>'group',
					'mapping_name'=>'member',
			),
			*/
			'Leader'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'client',
					'foreign_key'=>'leader',
					'mapping_name'=>'leader',
			),
			/*
			'Activity'=>array(
					'mapping_type'=>self::MANY_TO_MANY,
					'class_name'=>'activity',
					'mapping_name'=>'activity',
					'foreign_key'=>'group',
					'relation_foreign_key'=>'activity',
					'relation_table'=>'cnfm_group_activity',
			),
			'Position'=>array(
					'mapping_type'=>self::MANY_TO_MANY,
					'class_name'=>'position',
					'mapping_name'=>'position',
					'foreign_key'=>'group',
					'relation_foreign_key'=>'position',
					'relation_table'=>'cnfm_group_position',
			),
			*/
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
		if($result){
			$clientModel = D('Client');
			$where = array(
					"group"=>$result['id']
			);
			$result['member'] = $clientModel->where($where)->select();
		}
		return $result;
	}
}
?>