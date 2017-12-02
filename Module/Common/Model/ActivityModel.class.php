<?php
namespace Common\Model;
use Think\Model\RelationModel;
/**
 * 
 * @author LI Yuan
 *“活动”模型。
 */
class ActivityModel extends RelationModel{
	protected $_link = array(
			'Position'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'position',
					'foreign_key'=>'position',
					'mapping_name'=>'position',
			),
			'Tag'=>array(
					'mapping_type'=>self::MANY_TO_MANY,
					'class_name'=>'tag',
					'foreign_key'=>'activity',
					'mapping_name'=>'tag',
					'relation_foreign_key'=>'tag',
					'relation_table'=>'cnfm_activity_tag',
			),
			'ClientGroup'=>array(
					'mapping_type'=>self::MANY_TO_MANY,
					'class_name'=>'clientGroup',
					'mapping_name'=>'clientgroup',
					'foreign_key'=>'activity',
					'relation_foreign_key'=>'group',
					'relation_table'=>'cnfm_group_activity',
			),
			'Client'=>array(
					'mapping_type'=>self::MANY_TO_MANY,
					'class_name'=>'client',
					'mapping_name'=>'client',
					'foreign_key'=>'activity',
					'relation_foreign_key'=>'client',
					'relation_table'=>'cnfm_client_activity',
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