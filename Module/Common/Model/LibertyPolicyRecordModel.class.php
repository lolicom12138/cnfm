<?php
namespace Common\Model;
use Think\Model\RelationModel;
class LibertyPolicyRecordModel extends RelationModel{
	protected $_link = array(
			"Vip"=>array(
					"mapping_type"=>self::BELONGS_TO,
					"class_name"=>"vip",
					"foreign_key"=>"vip",
					"mapping_name"=>"vip",
			),
	);
	public function getListLifeshippingMonth(){
		$where = array(
				"timeLifeShipping"=>array(
						"gt",
						0
				),
		);
		$list = $this->where($where)->group("vip")->relation(true)->select();
		return $list;
	}
	public function getListPointgiveMonth(){
		$where = array(
				"timePointGive"=>array(
						"gt",
						0
				),
		);
		$list = $this->where($where)->group("vip")->relation(true)->select();
		return $list;
	}
}
?>