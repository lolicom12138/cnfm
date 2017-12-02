<?php
namespace Common\Model;
use Think\Model\RelationModel;
class RetirerModel extends RelationModel{
	protected $_link = array(
			"Vip"=>array(
					"mapping_type"=>self::BELONGS_TO,
					"class_name"=>"vip",
					"foreign_key"=>"vip",
					"mapping_name"=>"vip",
			),
			"Operator"=>array(
					"mapping_type"=>self::BELONGS_TO,
					"class_name"=>"user",
					"foreign_key"=>"operator",
					"mapping_name"=>"operator",
			),
	);
}
?>