<?php
namespace Common\Model;
use Think\Model\RelationModel;
class UserModel extends RelationModel{
	protected $_link = array(
			'Classified'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'userClassified',
					'foreign_key'=>'user',
					'mapping_name'=>'classified',
			),
			'OperationRecord'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'operationRecord',
					'foreign_key'=>'operator',
					'mapping_name'=>'operation',
			),
			'Departement'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'departement',
					'foreign_key'=>'departement',
					'mapping_name'=>'departement',
			),
			'Post'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'post',
					'foreign_key'=>'post',
					'mapping_name'=>'post',
			),
			'ClientGroup'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'clientGroup',
					'foreign_key'=>'responsable',
					'mapping_name'=>'clientgroup',
			),
			/*
			'Boss'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'user',
					'foreign_key'=>'boss',
					'mapping_name'=>'boss',
			),
			*/
			'Login'=>array(
					'mapping_type'=>self::HAS_ONE,
					'class_name'=>'login',
					'foreign_key'=>'user',
					'mapping_name'=>'login',
			),
			'Client'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'client',
					'foreign_key'=>'serveur',
					'mapping_name'=>'client',
			),
	);
	public function get($id){
		if(!$id){
			return;
		}
		if(!file_exists("/Runtime/Cache/User/".$id)){
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
									'like',
									'%'.$id.'%'
							),
					);
					$result = $this->relation(true)->where($where)->find();
				}
			}	
		}else{
			$result = unserialize(file_get_contents("/Runtime/Cache/User/".$id));
		}
		if(isset($result['boss']) && $result['boss']){
			$boss = $this->get($result['boss']);
			$result['boss'] = $boss;
		}
		return $result;
	}
	public function getByName($name){
		if(!$name){
			return;
		}
		$where = array(
				"name"=>$name
		);
		$result = $this->relation(true)->where($where)->find();
		if($result){
			$boss = $this->get($result['boss']);
			$result['boss'] = $boss;
		}
		return $result;
	}
	public function changeUser($id = null,$data = null){
		if((!$id) || (!$data)){
			echo 1;
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $this->where($where)->save($data);
		if(is_numeric($result)){
			$where = array(
					"id"=>$id,
			);
			$user = $this->relation(true)->where($where)->find();
			makeCache($id,$user,"/Runtime/Cache/User");
			return true;
		}
		return;
	}
	public function deleteUser($id = null){
		if(!$id){
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $this->where($where)->delete();
		if($result){
			makeCache($id,null,"/Runtime/Cache/User");
		}
		return $result;
	}
	public function select($option = []){
		$result = parent::select($option);
		if(count($result)){
			for($i = 0;$i < count($result);$i++){
				$result[$i]['boss'] = $this->where("id='".$result[$i]['boss']."'")->find();
			}
		}
		return $result;
	}
}
?>