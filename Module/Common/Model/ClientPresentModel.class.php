<?php
namespace Common\Model;
use Think\Model\RelationModel;
class ClientPresentModel extends RelationModel{
	protected $_link = array(
			"Client"=>array(
					"mapping_type"=>self::BELONGS_TO,
					"class_name"=>"client",
					"foreign_key"=>"client",
					"mapping_name"=>"client",
			),
	);
}