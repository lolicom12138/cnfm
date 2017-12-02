<?php
namespace Common\Model;
use Think\Model\RelationModel;
class InventoryInRecordModel extends RelationModel{
	protected $_link = array(
			'Product'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'product',
					'foreign_key'=>'product',
					'mapping_name'=>'product',
			),
			'Shop'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'shop',
					'foreign_key'=>'shop',
					'mapping_name'=>'shop',
			),
			'operator'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'user',
					'foreign_key'=>'operator',
					'mapping_name'=>'operator',
			),
	);
}
?>