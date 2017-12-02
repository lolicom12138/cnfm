<?php
namespace Statistique\Controller;
use Statistique\Common\Controller\Base;
class ConsoController extends Base {
    public function index(){
       if(!testClassified($this->user,"statistiqueConso")){
       		$this->error("权限不足");
       		return;
       }
       $cardModel = D('Card');
       $list = $cardModel->select();
       $this->assign("list",$list);
       $this->display();
    }
	public function listOrder(){
		if(!testClassified($this->user,"listOrderStatistic")){
			$this->error("权限不足");
			return;
		}
		$orderModel = D('Order');
		if(!IS_POST){
			$where = array(
					"time"=>array(
							"like",
							"%".date("Y-m-d")."%"
					),
					"serveur"=>$this->user['id']
			);
			$list = $orderModel->relation(true)->order("time desc")->where($where)->select();
		}else{
			$temp = array(
					"idOrder"=>I('post.idOrder'),
					"cardNumber"=>I('post.cardNumber'),
					"time"=>array(),
			);
			if(I("post.timeBegin")){
				$temp2 = array(
						"EGT",
						I('post.timeBegin')
				);
				array_push($temp['time'],$temp2);
			}
			if(I("post.timeEnd")){
				$temp2 = array(
						"ELT",
						I('post.timeEnd')
				);
				array_push($temp['time'],$temp2);
			}
			$where = array(
					"status"=>"已完成"
			);
			foreach($temp as $key=>$value){
				if(!$value ||!count($value)){
					continue;
				}
				$where[$key] = $value;
			}
			if(I('post.type')){
				$type = I('post.type');
				switch($type){
					case "product":
						$orderChercher = M('orderContent')->group("idOrder")->select();
						$temp = array();
						foreach($orderChercher as $key=>$value){
							array_push($temp,$value['idorder']);
						}
						$where['id'] = array(
								"in",
								$temp,
						);
						break;
					case "rapide":
						$orderChercher = M('orderContent')->group("idOrder")->select();
						$temp = array();
						foreach($orderChercher as $key=>$value){
							array_push($temp,$value['idorder']);
						}
						$where['id'] = array(
								"not in",
								$temp
						);
						break;
					case "exchange":
						$where['etc'] = array(
								"like",
								"%积分兑换%"
						);
						break;
					case "gift":
						$where['status'] = "已完成";
						$where['price'] = 0;
						break;
				}
			}
			$list = $orderModel->relation(true)->where($where)->order("time desc")->select();
		}
		$i = 0;
		while($list[$i]){
			$list[$i]['client'] = D('Client')->get($list[$i]['vip']['client']);
			$i++;
		}
		$this->assign("list",$list);
		$this->display();
	}
}