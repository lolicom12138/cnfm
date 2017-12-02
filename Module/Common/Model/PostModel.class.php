<?php
namespace Common\Model;
use Think\Model\RelationModel;
class PostModel extends RelationModel{
	protected $_link = array(
			'Worker'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'user',
					'foreign_key'=>'post',
					'mapping_name'=>'worker',
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
					"post"=>array(
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