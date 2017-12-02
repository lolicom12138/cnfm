<?php
namespace Statistique\Controller;
use Statistique\Common\Controller\Base;
class FoisController extends Base {
	/*
    public function index(){
       if(!testClassified($this->user,"statistiqueVip")){
       		$this->error("权限不足");
       		return;
       }
       $cardModel = D('Card');
       $list = $cardModel->select();
       $this->assign("list",$list);
       $this->display();
    }
    */
	public function already(){
		if(!testClassified($this->user,"statistiqueFoisAlready")){
			$this->error("权限不足");
			return;
		}
		$recordModel = M("foisRecord");
		if(!IS_POST){
			$list = $recordModel->select();
		}else{
			$data = I('post.');
			$where = array();
			if($data['timeBegin']){
				$temp = array(
						"time"=>array(
								"EGT",
								$data['timeBegin']
						)
				);
				array_push($where,$temp);
			}
			if($data['timeEnd']){
				if($where['time']){
					$temp = array(
							"ELT",
							$data['timeEnd']
					);
					array_push($where['time'],$temp);
				}else{
					$temp = array(
							"time"=>array(
									"ELT",
									$data['timeEnd']
							)
					);
					array_push($where,$temp);
				}
			}
			if($data['operator']){
				$userModel = D('User');
				$user = $userModel->get($data['operator']);
				if($user){
					$where['operator'] = $user['id'];
				}else{
					$where['operator'] = -1;
				}
			}
			$list = $recordModel->where($where)->select();
		}
		$result = array();
		$foisModel = D('Fois');
		$userModel = D('User');
		$i = 0;
		while($list[$i]){
			$list[$i]['fois'] = $foisModel->relation(true)->where(array("id"=>$list[$i]['fois']))->find();
			$list[$i]['operator'] = $userModel->get($list[$i]['operator']);
			$i++;
		}
		$this->assign("list",$list);
		$this->display();
	}
	public function rest(){
		if(!testClassified($this->user,"statistiqueFoisRest")){
			$this->error("权限不足");
			return;
		}
		$foisModel = D('Fois');
		if(!IS_POST){
			$list = $foisModel->relation(true)->where(array("fois"=>array("GT",0)))->select();
		}else{
			$where = array(
					"fois"=>array(
							"GT",
							0
					)
			);
			$data = I('post.');
			if($data['client']){
				$clientModel = D('Client');
				$client = $clientModel->get($data['client']);
				if(!$client){
					$where['client'] = -1;
				}else{
					$where['client'] = $client['id'];
				}
			}
			if($data['product']){
				$productModel = D('Product');
				$product = $productModel->get($data['product']);
				if($product){
					$where['product'] = $product['id'];
				}else{
					$where['product'] = -1;
				}
			}
			$list = $foisModel->relation(true)->where($where)->select();
		}
		$this->assign("list",$list);
		$this->display();
	}
}