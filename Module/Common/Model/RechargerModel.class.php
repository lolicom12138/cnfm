<?php
namespace Common\Model;
use Think\Model\RelationModel;
class RechargerModel extends RelationModel{
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
	public function get($id){
		if(!$id){
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $this->relation(true)->where($where)->find();
		return $result;
	}
	public function getListByVip($vip){
		if(!$vip){
			return;
		}
		$where = array(
				"vip"=>$vip['id'],
		);
		$result = $this->relation(true)->where($where)->select();
		return $result;
	}
	public function getListByClient($client){
		if(!$client){
			return;
		}
		$vipModel = D('Vip');
		$vips = $vipModel->where(array("client"=>$client['id']))->select();
		if(!count($vips)){
			return;
		}
		$str = "";
		foreach($vips as $v){
			$str .= $v['id'].",";
		}
		$str = trim($str,",");
		$where = array(
				"vip"=>array(
						"in",
						$str,
				),
		);
		$result = $this->relation(true)->where($where)->select();
		return $result;
	}
	public function getListByCondition($condition){
		$page = $condition['page'];
		$timeBegin = $condition['timeBegin'];
		$timeEnd = $condition['timeEnd'];
		$vip = $condition['vip'];
		$client = $condition['client'];
		$sumMin = $condition['sumMin'];
		$sumMax = $condition['sumMax'];
		$typeRecharger = $condition['typeRecharger'];
		$raison = $condition['raison'];
		$operator = $condition['operator'];
		$etc = $condition['etc'];
		$where = array();
		if($vip){
			$where = array(
					"vip"=>$vip['id'],
			);
		}elseif($client){
			$vipModel = D('Vip');
			$vips = $vipModel->where(array("client"=>$client['id']))->select();
			if(!count($vips)){
				return;
			}
			$str = "";
			foreach($vips as $v){
				$str .= $v['id'].",";
			}
			$str = trim($str,",");
			$where = array(
					"vip"=>array(
							"in",
							$str,
					),
			);
		}
		$where['time'] = array(
				"between",
				array(
						$timeBegin,
						$timeEnd
				),
		);
		$where['sum'] = array(
				"between",
				array(
						$sumMin,
						$sumMax,
				),
		);
		if($raison){
			$where['raison'] = array(
					"like",
					"%".$raison."%"
			);
		}
		if($etc){
			$where['etc'] = array(
					"like",
					"%".$etc."%"
			);
		}
		if($typeRecharger){
			$where['typeRecharger'] = $typeRecharger;
		}
		if($operator){
			$where['operator'] = $operator['id'];
		}
		$result = $this->relation(true)->where($where)->page($condition['page'],30)->select();
		return $result;
	}
}
?>