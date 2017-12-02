<?php
namespace Common\Model;
use Think\Model\RelationModel;
class ShopModel extends RelationModel{
	protected $_link = array(
			'Responsable'=>array(
					'mapping_type'=>self::BELONGS_TO,
					'class_name'=>'user',
					'foreign_key'=>'responsable',
					'mapping_name'=>'responsable',
			),
			'Inventory'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'inventory',
					'foreign_key'=>'shop',
					'mapping_name'=>'inventory',
			),
			'In'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'inventoryInRecord',
					'foreign_key'=>'shop',
					'mapping_name'=>'In',
			),
			'Out'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'inventoryOutRecord',
					'foreign_key'=>'shop',
					'mapping_name'=>'out',
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
							'like',
							'%'.$id.'%'
					),
			);
			$result = $this->relation(true)->where($where)->find();
		}
		return $result;
	}
}
?>