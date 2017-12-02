<?php
namespace Common\Model;
use Think\Model\RelationModel;
class OrderModel extends RelationModel{
	protected $_link = array(
			
			'Content'=>array(
					'mapping_type'=>self::HAS_MANY,
					'class_name'=>'orderContent',
					'foreign_key'=>'idOrder',
					'mapping_name'=>'content',
			),
			'Vip'=>array(
					'mapping_type'=>self::HAS_ONE,
					'class_name'=>'vip',
					'foreign_key'=>'cardNumber',
					'mapping_key'=>'cardnumber',
					//'parent_key'=>'cardnumber',
					'mapping_name'=>'vip',
			),
			"Serveur"=>array(
					"mapping_type"=>self::BELONGS_TO,
					"class_name"=>"user",
					"foreign_key"=>"serveur",
					"mapping_name"=>"serveur"
			),
			"Rest"=>array(
					"mapping_type"=>self::HAS_ONE,
					"class_name"=>"pointRest",
					"foreign_key"=>"idOrder",
					"mapping_name"=>"pointRest",
			),
			"Conso"=>array(
					"mapping_type"=>self::HAS_ONE,
					"class_name"=>"pointconsoRecord",
					"foreign_key"=>"idOrder",
					"mapping_name"=>"pointconsoRecord",
			)
	);
	public function get($id){
		if(!$id || !is_numeric($id)){
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $this->relation(true)->where($where)->find();
		if(!$result){
			$where = array(
					"idOrder"=>$id,
			);
			$result = $this->relation(true)->where($where)->find();
		}
		return $result;
	}
}
?>