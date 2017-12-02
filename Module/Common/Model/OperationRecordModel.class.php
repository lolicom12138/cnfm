<?php
namespace Common\Model;
use Think\Model\RelationModel;
class OperationRecordModel extends RelationModel{
	protected $_link = array(
			'operator'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'user',
					'foreign_key'=>'operator',
					'mapping_name'=>'operator',
			),
	);
}
?>