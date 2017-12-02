<?php
namespace Api\Controller;
use Think\Controller;
class UserController extends Controller {
	public function deleteDepartement(){
		$id = I('post.id');
		if(!$id){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$departementModel = D("Departement");
		$departement = $departementModel->get($id);
		if(!$departement){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $departementModel->where($where)->delete();
		if($result){
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"删除部门",
					"content"=>"部门:".$departement['departement'],
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			echo '{
    			"status":"ok"
    		}';
		}else{
			echo '{
    			"status":"fail"
    		}';
		}
	}
	public function deletePost(){
		$id = I('post.id');
		if(!$id){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$postModel = D("Post");
		$post = $postModel->get($id);
		if(!$post){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $postModel->where($where)->delete();
		if($result){
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"删除职位",
					"content"=>"职位:".$post['post'],
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			echo '{
    			"status":"ok"
    		}';
		}else{
			echo '{
    			"status":"fail"
    		}';
		}
	}
	public function deleteActivity(){
		$id = I('post.id');
		if(!$id){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$activityModel = D("Activity");
		$activity = $activityModel->get($id);
		if(!$activity){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $activityModel->where($where)->delete();
		if($result){
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"删除活动",
					"content"=>"活动:".$activity['name'],
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			echo '{
    			"status":"ok"
    		}';
		}else{
			echo '{
    			"status":"fail"
    		}';
		}
	}
	public function deletePosition(){
		$id = I('post.id');
		if(!$id){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$positionModel = D("Position");
		$position = $positionModel->get($id);
		if(!$position){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $positionModel->where($where)->delete();
		if($result){
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"删除地点",
					"content"=>"地点:".$position['position'],
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			echo '{
    			"status":"ok"
    		}';
		}else{
			echo '{
    			"status":"fail"
    		}';
		}
	}
	public function deleteGroup(){
		$id = I('post.id');
		if(!$id){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$groupModel = D("clientGroup");
		$group = $groupModel->get($id);
		if(!$group){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$where = array(
				"id"=>$id,
		);
		$result = $groupModel->where($where)->delete();
		if($result){
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"删除互助组",
					"content"=>"互助组:".$group['name'],
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			echo '{
    			"status":"ok"
    		}';
		}else{
			echo '{
    			"status":"fail"
    		}';
		}
	}
	public function getDepartement(){
		$departementModel = D('Departement');
		$list = $departementModel->relation(true)->select();
		$str = "";
		foreach($list as $l){
			$str .= '{"id":"'.$l['id'].'","name":"'.$l['departement'].'"},';
		}
		$str = rtrim($str,",");
		$str = '{"departement":['.$str.']}';
		echo $str;
	}
	public function bindShop(){
		if(!IS_POST){
			apiReturn("false");
			return;
		}
		$idDepart = I('post.departement');
		$idShop = I('post.shop');
		$departementModel = D('Departement');
		$shopModel = D('Shop');
		if(!$idDepart){
			apiReturn("false");
			return;
		}
		if(!$idShop){
			apiReturn("false");
			return;
		}
		$departement = $departementModel->get($idDepart);
		if(!$departement){
			apiReturn("false");
			return;
		}
		$shop = $shopModel->get($idShop);
		if(!$shop){
			apiReturn("false");
		}
		$data['shop'] = $shop['id'];
		$result = $departementModel->where(array("id"=>$departement['id']))->save($data);
		if(is_numeric($result)){
			$operator = session("user");
			$data = array(
					"operator"=>$operator['id'],
					"operation"=>"关联部门与店铺",
					"content"=>"部门:".$departement['departement']." 店铺:".$shop['name'],
					"time"=>date("Y-m-d H:i:s"),
					"ip"=>get_client_ip(),
			);
			M('operationRecord')->add($data);
			apiReturn("ok");
		}else{
			apiReturn("fail");
		}
	}
	public function listOperation(){
		if(!IS_POST){
			apiReturn("false");
			return;
		}
		$model = D('OperationRecord');
		$user = I('post.user');
		$operation = I('post.operation');
		$content = I('post.content');
		$timeBegin = I('post.timeBegin');
		$timeEnd = I('post.timeEnd');
		$page = I('post.page');
		$where = array(
				"time"=>array()
		);
		if($user){
			$userModel = D('User');
			$user = $userModel->get($user);
			$where['user'] = $user?$user['id']:0;
		}
		if($operation){
			$where['operation'] = array(
					"like",
					"%".$operation."%"
			);
		}
		if($content){
			$where['content'] = array(
					"like",
					"%".$content."%"
			);
		}
		if($timeBegin){
			$temp = array(
					"EGT",
					$timeBegin
			);
			array_push($where['time'],$temp);
		}
		if($timeEnd){
			$date = explode("-",$timeEnd);
    			$date[2]++;
    			$temp = array(
    					"ELT",
    					$date[0]."-".$date[1]."-".$date[2],
    			);
    			array_push($where['time'],$temp);
		}
		if(!count($where['time'])){
			unset($where['time']);
		}
		if(!$page || !is_numeric($page)){
			$page = 1;
		}
		if(!count($where)){
			$list = $model->relation(true)->page($page,30)->select();
		}else{
			$list = $model->relation(true)->where($where)->page($page,30)->select();
		}
		$result = array(
				"status"=>"ok",
				"list"=>$list,
		);
		apiReturn($result);
	}
	public function getUserClassified(){
		if(!IS_POST){
			apiReturn("false");
			return;
		}
		$user = I('post.user');
		$userModel = D('User');
		$user = $userModel->getByName($user);
		if(!$user){
			apiReturn("false");
			return;
		}
		$model = M('userClassified');
		
		$list = $model->where("user='".$user['id']."'")->field("classified")->select();
		$result = array(
				"status"=>"ok",
				"list"=>$list
		);
		apiReturn($result);
	}
	public function deletePresenttime(){
		if(!IS_POST){
			apiReturn("false1");
			return;
		}
		$id = I('post.id');
		if(!$id){
			apiReturn("false2");
			return;
		}
		$model = M('presentPoint');
		$where = array("id"=>$id);
		$presentPolicy = $model->where($where)->find();
		if(!$presentPolicy){
			apiReturn("false3");
			return;
		}
		$result = $model->where($where)->delete();
		if($result){
			apiReturn("ok");
		}else{
			apiReturn("fail");
		}
	}
}