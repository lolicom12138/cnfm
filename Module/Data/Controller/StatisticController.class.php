<?php
namespace Data\Controller;
use Data\Common\Controller\Base;
class StatisticController extends Base {
	public function pointgive(){
		if(!testClassified($this->user,"lifeshipping")){
			$this->error("权限不足");
			return;
		}
		$lifeshippingModel = D('LibertyPolicyRecord');
		$list = $lifeshippingModel->getListPointgiveMonth();
		$vipModel = D('Vip');
		echo "处理中……";
		foreach($list as $l){
			$data = array(
					"timePointGive"=>$l['timepointgive']-1,
			);
			$lifeshippingModel->where(array("id"=>$l['id']))->save($data);
			$data = array(
					"pointGive"=>$l['vip']['pointgive']+$l['pointgive'],
			);
			$vipModel->where(array("id"=>$l['vip']['id']))->save($data);
		}
		echo "处理完成";
	}
	public function lifeshipping(){
		if(!testClassified($this->user,"lifeshipping")){
			$this->error("权限不足");
			return;
		}
		$lifeshippingModel = D('LibertyPolicyRecord');
		$list = $lifeshippingModel->getListLifeshippingMonth();
		echo "处理中……";
		foreach($list as $l){
			$data = array(
					"timeLifeShipping"=>$l['timelifeshipping']-1,
			);
			$lifeshippingModel->where(array("id"=>$l['id']))->save($data);
		}
		echo "处理完成";
	}
	public function lifeshippingEveryMonth(){
		if(!testClassified($this->user,"lifeshippingEveryMonth")){
			$this->error("权限不足");
			return;
		}
		$lifeshippingModel = D('LibertyPolicyRecord');
		$list = $lifeshippingModel->getListLifeshippingMonth();
		$clientModel = D('Client');
		for($i = 0;$list[$i];$i++){
			$list[$i]['client'] = $clientModel->get($list[$i]['vip']['client']);
		}
		//统计
		$statistic = array();
		for($i = 0;$list[$i];$i++){
			for($j = 0;$statistic[$j];$j++){
				if($statistic[$j]['value'] == $list[$i]['lifeshipping']){
					$statistic[$j]['number']++;
					break;
				}
			}
			if($j == count($statistic)){
				$temp = array(
						"value"=>$list[$i]['lifeshipping'],
						"number"=>1
				);
				array_push($statistic,$temp);
			}
		}
		$this->assign("statistic",$statistic);
		$this->assign("list",$list);
		$this->display();
	}
	public function pointgiveEveryMonth(){
		if(!testClassified($this->user,"pointgiveEveryMonth")){
			$this->error("权限不足");
			return;
		}
		$lifeshippingModel = D('LibertyPolicyRecord');
		$list = $lifeshippingModel->getListPointgiveMonth();
		$clientModel = D('Client');
		for($i = 0;$list[$i];$i++){
			$list[$i]['client'] = $clientModel->get($list[$i]['vip']['client']);
		}
		//统计
		$statistic = array();
		for($i = 0;$list[$i];$i++){
			for($j = 0;$statistic[$j];$j++){
				if($statistic[$j]['value'] == $list[$i]['pointgive']){
					$statistic[$j]['number']++;
					break;
				}
			}
			if($j == count($statistic)){
				$temp = array(
						"value"=>$list[$i]['pointgive'],
						"number"=>1
				);
				array_push($statistic,$temp);
			}
		}
		
		$this->assign("statistic",$statistic);
		$this->assign("list",$list);
		$this->display();
	}
	public function statisticProductIndex(){
		if(!testClassified($this->user,"statisticProduct")){
			$this->error("权限不足");
			return;
		}
		$this->display();
	}
	public function statisticProduct(){
		if(!testClassified($this->user,"statisticProduct")){
			$this->error("权限不足");
			return;
		}
		$producttModel = D('Product');
		$where = "";
		$order = "p.id asc";
		if(I('post.supplier')){
			$where .= "and p.supplier='".I('post.supplier')."' ";
		}
		if(I('post.name')){
			$where .= "and p.name like '%".I('post.name')."%' ";
		}
		if(I('post.class')){
			$where .= "and p.class='".I('post.class')."%' ";
		}
		if(I('post.venduMin')){
			$where .= "and p.nombreVendu>='".I('post.venduMin')."' ";
		}
		if(I('post.venduMax')){
			$where .= "and p.nombreVendu<='".I('post.venduMax')."' ";
		}
		if(I('post.order')){
			$order = I('post.order');
		}
		$where = ltrim("and ",$where);
		$where = $where?$where:"1";
		$list = $producttModel->alias('p')->where($where)->order($order)->select();
		$this->assign("list",$list);
		$this->display();
	}
	/*
	public function statisticStorageIn(){
		
	}
	public function statisticStorageOut(){
		
	}
	public function statisticSell(){
		
	}
	public function statisticInventory(){
		
	}
	public function statisticShop(){
		
	}
	*/
}