<?php

namespace Statistique\Controller;

use Statistique\Common\Controller\Base;

class PointController extends Base{
	public function index(){
		if(!testClassified($this->user,"statistiquePoint")){
			$this->error("权限不足");
			return;
		}
		$rechargerModel = D('Recharger');
		$where = array(
				"time" => array(
						"between",
						array(
								date("Y-m-")."01 00:00:00",
								date("Y-m-d H:i:s"),
						)
				),
				"raison" => array(
						"like",
						"%送%"
				),
				"typeRecharger" => "pointGive"
		);
		$list = $rechargerModel->where($where)->select();
		$total = 0;
		if(count($list)){
			foreach($list as $l){
				$total += $l['sum'];
			}
		}
		$givable = M('pointgive')->find();
		$rest = $givable['pointgive']-$total;
		$this->assign("total",$total);
		$this->assign("rest",$rest);
		$this->display();
	}
	public function changePointgive(){
		if(!testClassified($this->user,"changePointgive")){
			$this->error("权限不足");
			return;
		}
		if(!IS_POST){
			$this->display();
			return;
		}
		$newPoint = I('post.point');
		if(!is_numeric($newPoint) || $newPoint <= 0){
			$this->error("积分额度输入错误");
			return;
		}
		$result = M('pointgive')->where("id=1")->save(array("pointGive"=>$newPoint));
		if(is_numeric($result)){
			$this->success("成功");
		}else{
			$this->error("失败");
		}
	}
	public function addPointgive(){
		if(!testClassified($this->user,"addPointgive")){
			$this->error("权限不足");
			return;
		}
		if(!IS_POST){
			$this->display();
			return;
		}
		$addPoint = I('post.point');
		if(!is_numeric($addPoint) || $addPoint <= 0){
			$this->error("积分额度输入错误");
			return;
		}
		$model = M('pointgive');
		$record = $model->find();
		$data = array(
				"pointGive"=>$record['pointgive']+$addPoint,
		);
		$result = $model->where("id=1")->save($data);
		if(is_numeric($result)){
			$this->success("成功");
		}else{
			$this->error("失败");
		}
	}
	public function minusPointgive(){
		if(!testClassified($this->user,"minusPointgive")){
			$this->error("权限不足");
			return;
		}
		if(!IS_POST){
			$this->display();
			return;
		}
		$addPoint = I('post.point');
		if(!is_numeric($addPoint) || $addPoint <= 0){
			$this->error("积分额度输入错误");
			return;
		}
		$model = M('pointgive');
		$record = $model->find();
		$data = array(
				"pointGive"=>$record['pointgive']-$addPoint,
		);
		$result = $model->where("id=1")->save($data);
		if(is_numeric($result)){
			$this->success("成功");
		}else{
			$this->error("失败");
		}
	}
}