<?php
namespace Common\Model;
use Think\Model\RelationModel;
class LoginModel extends RelationModel{
	protected $_link = array(
			'User'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'user',
					'foreign_key'=>'user',
					'mapping_name'=>'user',
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
					"login"=>$id,
			);
			$result = $this->relation(true)->where($where)->find();
		}
		return $result;
	}
	public function changeLogin($id = null,$data = null){
		if(!$id){
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $this->where($where)->save($data);
		if(is_numeric($result)){
			$login = $this->where($where)->find();
			makeCache($login['login'],$login,"/Runtime/Cache/Login");
			return true;
		}
		return;
	}
	public function deleteLogin($id = null){
		if(!$id){
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$login = $this->where($where)->find();
		$result = $this->where($where)->delete();
		if($result){
			makeCache($login['login'],null,"/Runtime/Cache/Login");
		}
		return $result;
	}
}
?>