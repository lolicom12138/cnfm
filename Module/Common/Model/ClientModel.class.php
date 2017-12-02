<?php
namespace Common\Model;
use Think\Model\RelationModel;
/**
 * 
 * @author Administrator
 *客户模型
 */
class ClientModel extends RelationModel{
	protected $_link = array(
			'Disease'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'clientDisease',
					'foreign_key'=>'client',
					'mapping_name'=>'disease',
			),
			'Pharm'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'clientPharm',
					'foreign_key'=>'client',
					'mapping_name'=>'pharm',
			),
			'HealthProduct'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'clientHealthProduct',
					'foreign_key'=>'client',
					'mapping_name'=>'healthproduct',
			),
			'Financing'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'clientFinancing',
					'foreign_key'=>'client',
					'mapping_name'=>'financing',
			),
			'Present'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'clientPresent',
					'foreign_key'=>'client',
					'mapping_name'=>'present',
			),
			'Consomation'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'recordConsomation',
					'foreign_key'=>'client',
					'mapping_name'=>'consomation',
			),
			'Vip'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'vip',
					'foreign_key'=>'client',
					'mapping_name'=>'vip',
			),
			'Beinvited'=>array(
					'mapping_type'=>self::HAS_ONE,
					'class_name'=>'clientInvitation',
					'foreign_key'=>'client',
					'mapping_name'=>'beinvited',
			),
			'Invite'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'clientInvitation',
					'foreign_key'=>'introducer',
					'mapping_name'=>'invite',
			),
			'Serveur'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'User',
					'foreign_key'=>'serveur',
					'mapping_name'=>'serveur',
			),
			'Visit'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'recordVisit',
					'foreign_key'=>'client',
					'mapping_name'=>'visit',
			),
			'Group'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'clientGroup',
					'foreign_key'=>'group',
					'mapping_name'=>'group',
			),
			'Position'=>array(
					'mapping_type'=>self::MANY_TO_MANY,
					'class_name'=>'position',
					'mapping_name'=>'position',
					'foreign_key'=>'client',
					'relation_foreign_key'=>'position',
					'relation_table'=>'cnfm_client_go_position',
			),
			'Activity'=>array(
					'mapping_type'=>self::MANY_TO_MANY,
					'class_name'=>'activity',
					'mapping_name'=>'activity',
					'foreign_key'=>'client',
					'relation_foreign_key'=>'activity',
					'relation_table'=>'cnfm_client_activity',
			),
			'Habbit'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'clientHabbit',
					'foreign_key'=>'client',
					'mapping_name'=>'habbit',
			),
			'Profile'=>array(
					'mapping_type'=>self::HAS_ONE,
					'class_name'=>'clientProfile',
					'foreign_key'=>'client',
					'mapping_name'=>'profile',
			),
			'Visit'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'recordVisit',
					'foreign_key'=>"client",
					"mapping_name"=>"visit",
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
					"identity"=>$id,
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
		}
		return $result;
	}
	public function getListByName($name){
		$where = array(
				"name"=>array(
						"like",
						"%".$name."%"
				)
		);
		return $this->relation(true)->where($where)->select();
	}
}
?>