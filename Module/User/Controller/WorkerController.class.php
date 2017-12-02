<?php
namespace User\Controller;
use Think\Exception;
use User\Common\Controller\Base;
class WorkerController extends Base {
    public function projectWork(){
    	if(!IS_POST){
    		$this->display();
    		return;
    	}
    	$data['user'] = $this->user['id'];
    	$data['time'] = date('Y-m-d H:i:s');
    	$data['title'] = I('post.title');
    	$data['content'] = I('post.content');
    	$model = M('userProject');
    	$result = $model->add($data);
    	if($result){
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function dailyReport(){
    	if(!IS_POST){
    		$this->display();
    		return;
    	}
    	$data['user'] = $this->user['id'];
    	$data['time'] = date('Y-m-d H:i:s');
    	$data['title'] = I('post.title');
    	$data['content'] = I('post.content');
    	$model = M('userReport');
    	$result = $model->add($data);
    	if($result){
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function reportClient($id = null){
    	if(!testClassified($this->user,"reportClient")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$clientModel = D('Client');
    	$client = $clientModel->get($id);
    	if(!$client){
    		$this->error("非法访问1");
    		return;
    	}
    	if($client['serveur']['id'] != $this->user['id']){
    		$this->error("非法访问2");
    		return;
    	}
    	if(!IS_POST){
    		$this->assign("client",$client);
    		$this->display();
    		return;
    	}
    	$temp['marriage'] = I("post.marriage");
    	$temp['marriageTime'] = I("post.marriageTime");
    	$temp['tall'] = I("post.tall");
    	$temp['amountPerMonth'] = I("post.amountPerMonth");
    	$temp['majorAreaBuy'] = I("post.majorAreaBuy");
    	$temp['majorLocationBuy'] = I("post.majorLocationBuy");
    	$temp['son'] = I("post.son");
    	$temp['daughter'] = I("post.daughter");
    	$temp['typeLive'] = I("post.typeLive");
    	$temp['politicalStatus'] = I("post.politicalStatus");
    	$temp['etc'] = I("post.etc");
    	$data = array();
    	foreach($temp as $key=>$value){
    		if($value){
    			$data[$key] = $value;
    		}
    	}
    	$model = M('clientProfile');
    	$where = array("client"=>$id);
    	$result = $model->where($where)->find();
    	if($result){
    		if(is_numeric($model->where(array("client"=>$id))->save($data))){
    			$this->success("成功");
    		}else{
    			$this->error("失败");
    		}
    	}else{
    		$data['client'] = $id;
    		if($model->add($data)){
    			$this->success("成功");
    		}else{
    			$this->error("失败");
    		}
    	}
    }
    public function transferClient($id = null){
    	if(!testClassified($this->user,"transferClient")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$clientModel = D('Client');
    	$client = $clientModel->get($id);
    	if(!$client){
    		$this->error("非法访问1");
    		return;
    	}
    	if($client['serveur']['id'] != $this->user['id']){
    		$this->error("非法访问2");
    		return;
    	}
    	if(!IS_POST){
    		$userModel = D('User');
    		$list = $userModel->retlation(true)->select();
    		$this->assign("client",$client);
    		$this->display();
    	}
    	$idServeur = I('post.serveur');
    	$data = array(
    			"serveur"=>$idServeur,
    	);
    	if(is_numeric($clientModel->where(array("id"=>$client['id']))->save($data))){
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function listReport(){
    	if(!IS_POST){
    		$reportModel = M('userReport');
    		$where = array("user"=>$this->user['id']);
    		$list = $reportModel->where($where)->select();
    		$userModel = D('User');
    		$listUser = $userModel->where("boss='".$this->user['id']."'")->select();
    		if($listUser){
    			foreach($listUser as $u){
    				$result = $reportModel->where("user='".$u['id']."'")->select();
    				if(count($result)){
    					foreach($result as $r){
    						$temp = $r;
    						$temp['user'] = $u;
    						array_push($list,$temp);
    					}
    				}
    			}
    		}
    		if(count($list)){
    			for($i = 0;$list[$i];$i++){
    				$list[$i]['user'] = $userModel->get($list[$i]['user']);
    			}
    		}
    	}
    	/*
    	else{
    		$timeBegin = I('post.timeBegin');
    		$timeEnd = I('post.timeEnd');
    		$user = I('post.user');
    		$title = I('post.title');
    		if($user){
    			$userModel = D('User');
    			$user = $userModel->get($user);
    			if($user['boss']['id'] != $this->user['id']){
    				$where['user'] = 0;
    			}else{
    				$where['user']
    			}
    		}
    		
    	}
    	*/
    	$this->assign("list",$list);
    	$this->display();
    }
    public function detailReport($id = null){
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$reportModel = M('userReport');
    	$report = $reportModel->where("id='".$id."'")->find();
    	if(!$report){
    		$this->error("非法访问");
    		return;
    	}
    	$userModel = D('User');
    	$report['user'] = $userModel->get($report['user']);
    	$this->assign("report",$report);
    	$this->display();
    }
    public function listProject(){
    	$projectModel = M('userProject');
    	$userModel = D('User');
    	$listUser = $userModel->where("boss='".$this->user['id']."'")->select();
    	if(!$listUser){
    		$this->error("你没有下属");
    		return;
    	}
    	$listProject = array();
    	foreach($listUser as $u){
    		$result = $projectModel->where("user='".$u['id']."'")->select();
    		if(count($result)){
    			foreach($result as $r){
    				$temp = $r;
    				$temp['user'] = $u;
    				array_push($listProject,$temp);
    			}
    		}
    	}
    	if(!count($listProject)){
    		$this->error("下属没有计划");
    		return;
    	}
    	$this->assign("list",$listProject);
    	$this->display();
    }
    public function detailProject($id = null){
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$projectModel = M('userProject');
    	$project = $projectModel->where("id='".$id."'")->find();
    	if(!$project){
    		$this->error("非法访问");
    		return;
    	}
    	$userModel = D('User');
    	$project['user'] = $userModel->get($project['user']);
    	$this->assign("project",$project);
    	$this->display();
    }
    public function changePwd(){
    	if(!IS_POST){
    		$this->display();
    		return;
    	}
    	$pwdOld = I("post.pwdOld");
    	$pwdConfirm = I("post.pwdConfirm");
    	$pwd = I("post.pwd");
    	if(chiffrement($pwdOld) != $this->user['login']['pwd']){
    		$this->error("原始密码输入错误");
    		return;
    	}
    	if($pwd != $pwdConfirm || (!$pwd)){
    		$this->error("两次输入的密码不一致或没有输入新密码");
    		return;
    	}
    	$data['pwd'] = chiffrement($pwd);
    	$loginModel = D('Login');
    	$result = $loginModel->changeLogin($this->user['login']['id'],$data);
    	if($result){
    		$userModel = D('User');
    		$user = $userModel->get($this->user['id']);
    		session("user",$user);
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function selfDetail(){
    	$userModel = D('User');
    	$listXiashu = $userModel->where(array("boss"=>$this->user['id']))->select();
    	$this->assign("list",$listXiashu);
    	$this->assign("user",$this->user);
    	$this->display();
    }
    public function addActivity(){
    	if(!testClassified($this->user,"addActivity")){
    		$this->error("权限不足");
    		return;
    	}
    	$positionModel = D('Position');
    	if(!IS_POST){
    		$listPosition = $positionModel->select();
    		$this->assign("position",$listPosition);
    		$this->display();
    		return;
    	}
    	$activityModel = D('Activity');
    	$data['name'] = I('post.name');
    	if(!$data['name']){
    		$this->error("请输入活动名称");
    		return;
    	}
    	$where = array(
    			"name"=>$data['name'],
    	);
    	$result = $activityModel->where($where)->select();
    	if(count($result)){
    		$this->error("活动名称重复");
    		return;
    	}
    	$data['time'] = I('post.time');
    	$data['timeEnd'] = I('post.timeEnd');
    	if(!$data['timeEnd']){
    		unset($data['timeEnd']);
    	}
    	$data['position'] = I('post.position');
    	$position = $positionModel->get($data['position']);
    	if(!$position){
    		$this->error("活动地点输入有误，请重试");
    		return;
    	}
    	$data['point'] = I('post.point');
    	if(!$data['point'] || !is_numeric($data['point'])){
    		$data['point'] = 0;
    	}
    	$result = $activityModel->add($data);
    	if($result){
    		unset($data);
    		$tag = I('post.tag');
    		$tag = explode(" ",$tag);
    		$tag = $this->addTag($tag);
    		$data = array();
    		foreach($tag as $t){
    			$temp = array(
    					"activity"=>$result,
    					"tag"=>$t,
    			);
    			array_push($data,$temp);
    		}
    		$activityTagModel = M('activityTag');
    		$result = $activityTagModel->addAll($data);
    		$this->success("成功",U('/user/worker/listActivity'));
    	}else{
    		$this->error("失败");
    	}
    }
    private function addTag($tag){
    	$tagOld = array();
    	$tagNew = array();
    	$tagModel = M('tag');
    	foreach($tag as $t){
    		$where = array(
    				"tag"=>$t
    		);
    		$result = $tagModel->where($where)->find();
    		if($result){
    			array_push($tagOld,intval($result['id']));
    		}else{
    			$temp = array(
    					"tag"=>$t,
    			);
    			array_push($tagNew,$temp);
    		}
    	}
    	if(count($tagNew)){
    		$nombre = count($tagNew);
    		$start = $tagModel->addAll($tagNew);
    		$end = $start+$nombre;
    		while($start <= $end){
    			array_push($tagOld,$start);
    			$start++;
    		}
    	}
    	return $tagOld;    	
    }
    public function deleteActivity($id = null){
    	if(!testClassified($this->user,"deleteActivity")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$activityModel = D("Activity");
    	$activity = $activityModel->get($id);
    	if(!$activity){
    		$this->error("非法访问");
    		return;
    	}
    	/*
    	if(!IS_POST){
    		$this->assign("activity",$activity);
    		$this->display();
    		return;
    	}
    	*/
    	$where = array(
    			"id"=>$id,
    	);
    	$result = $activityModel->where($where)->delete();
    	if($result){
    		$this->success("成功",U('/user/worker/listActivity'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function changeActivity($id = null){
    	if(!testClassified($this->user,"changeActivity")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$activityModel = D("Activity");
    	$activity = $activityModel->get($id);
    	if(!$activity){
    		$this->error("非法访问");
    		return;
    	}
    	$positionModel = D('Position');
    	if(!IS_POST){
    		$listPosition = $positionModel->select();
    		$this->assign("position",$listPosition);
    		$activity['time'] = str_replace(" ", "T", $activity['time']);
    		$activity['timeend'] = str_replace(" ","T",$activity['timeend']);
    		$tag = "";
    		foreach($activity['tag'] as $t){
    			$tag .= " ".$t['tag'];
    		}
    		$activity['positionid'] = $activity['position']['id'];
    		$tag = trim($tag);
    		$this->assign("tag",$tag);
    		$this->assign("activity",$activity);
    		$this->display();
    		return;
    	}
    	$data['name'] = I('post.name');
    	if(!$data['name']){
    		$this->error("请输入活动名称");
    		return;
    	}
    	$where = array(
    			"id"=>array(
    					"neq",
    					$id,
    			),
    			"name"=>$data['name'],
    	);
    	$result = $activityModel->where($where)->select();
    	if(count($result)){
    		$this->error("活动名称重复");
    		return;
    	}
    	$data['time'] = I('post.time');
    	$data['timeEnd'] = I('post.timeEnd');
    	if(!$data['timeEnd']){
    		$data['timeEnd'] = "null";
    	}
    	$data['position'] = I('post.position');
    	$position = $positionModel->get($data['position']);
    	if(!$position){
    		$this->error("活动地点输入有误，请重试");
    		return;
    	}
    	$data['point'] = I('post.point');
    	if(!$data['point'] || !is_numeric($data['point'])){
    		$data['point'] = 0;
    	}
    	$where = array(
    			"id"=>$id
    	);
    	$result = $activityModel->where($where)->save($data);
    	if(is_numeric($result)){
    		unset($data);
    		$tag = I('post.tag');
    		$tag = explode(" ",$tag);
    		$tag = $this->addTag($tag);
    		$data = array();
    		foreach($tag as $t){
    			$temp = array(
    					"activity"=>$id,
    					"tag"=>$t,
    			);
    			array_push($data,$temp);
    		}
    		$activityTagModel = M('activityTag');
    		$where = array(
    				"activity"=>$id,
    		);
    		$activityTagModel->where($where)->delete();
    		$result = $activityTagModel->addAll($data);
    		$this->success("成功",U('/user/worker/listActivity'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function listActivity(){
    	if(!testClassified($this->user,"listActivity")){
    		$this->error("权限不足");
    		return;
    	}
    	$activityModel = D('Activity');
    	if(IS_POST){
    		$dateBegin = I('post.dateBegin');
    		$dateEnd = I('post.dateEnd');
    		$name = I('post.name');
    		$position = I('post.position');
    		$tag = I('post.tag');
    		//tag 这里有问题
    		$where = "";
    		if($dateBegin){
    			$where .= "and time>='".$dateBegin."' ";
    		}
    		if($dateEnd){
    			$where .= "and time<='".$dateEnd."' ";
    		}
    		if($name){
    			$where .= "and name like '%".$name."%' ";
    		}
    		if($position){
    			$where .= "and position='".$position."'";
    		}
    		$where = trim($where,"and");
    		$list = $activityModel->relation(true)->where($where)->order("time desc")->select();
    	}else{
    		$list = $activityModel->relation(true)->order("time desc")->select();
    	}
    	$positionModel = D('position');
    	$listPosition = $positionModel->select();
    	$this->assign("position",$listPosition);
    	$this->assign("list",$list);
    	$this->display();
    }
    public function detailActivity($id = null){
    	if(!testClassified($this->user,"detailActivity")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$activityModel = D("Activity");
    	$activity = $activityModel->get($id);
    	if(!$activity){
    		$this->error("非法访问");
    		return;
    	}
    	$this->assign("activity",$activity);
    	$this->display();
    }
    /*因为活动功能重新设计，直接通过签到记录，所以以下功能暂时取消
    public function addClientToActivity(){
    	
    }
    public function deleteClientFromActivity(){
    	
    }
    public function addGroupToActivity(){
    	
    }
    public function deleteGroupFromActivity(){
    	
    }
    */
    public function addGroup(){
    	if(!testClassified($this->user,"addGroup")){
    		$this->error("权限不足");
    		return;
    	}
    	$groupModel = D('ClientGroup');
    	$userModel = D('User');
    	$clientModel = D('Client');
    	if(!IS_POST){
    		$listUser = $userModel->relation(true)->select();
    		$listClient = $clientModel->relation(true)->select();
    		$this->assign("user",$listUser);
    		$this->assign("client",$listClient);
    		$this->display();
    		return;
    	}
    	$data['name'] = I('post.name');
    	if(!$data['name']){
    		$this->error("请输入小组名称");
    		return;
    	}
    	$where = array(
    			"name"=>$data['name'],
    	);
    	if(count($groupModel->where($where)->select())){
    		$this->error("小组名称重复");
    		return;
    	}
    	$data['responsable'] = I('post.responsable');
    	$responsable = $userModel->get($data['responsable']);
    	if(!$responsable){
    		$this->error("小组负责人信息输入错误");
    		return;
    	}
    	$data['responsable'] = $responsable['id'];
    	$data['leader'] = I('post.leader');
    	$leader = $clientModel->get($data['leader']);
    	if(!$leader){
    		$this->error("小组组长输入错误");
    		return;
    	}
    	$data['leader'] = $leader['id'];
		$data['time'] = date("Y-m-d");
    	$result = $groupModel->add($data);
    	if($result){
    		$this->success("成功",U('/User/worker/listGroup'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function deleteGroup($id = null){
    	if(!testClassified($this->user,"deleteGroup")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$groupModel = D('ClientGroup');
    	$group = $groupModel->get($id);
    	if(!$group){
    		$this->error("非法访问");
    	}
    	/*
    	if(!IS_POST){
    		$this->assign("group",$group);
    		$this->display();
    		return;
    	}
    	*/
    	$where = array(
    			"id"=>$id,
    	);
    	$result = $groupModel->where($where)->delete();
    	if($result){
    		$this->success("成功",U('/user/worker/listGroup'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function changeGroup($id = null){
    	if(!testClassified($this->user,"deleteGroup")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$groupModel = D('ClientGroup');
    	$group = $groupModel->get($id);
    	if(!$group){
    		$this->error("非法访问");
    	}
    	$userModel = D('User');
    	$clientModel = D('Client');
    	if(!IS_POST){
    		$listUser = $userModel->relation(true)->select();
    		$listClient = $clientModel->relation(true)->select();
    		$this->assign("user",$listUser);
    		$this->assign("client",$listClient);
    		$this->assign("group",$group);
    		$this->display();
    		return;
    	}
    	$data['name'] = I('post.name');
    	if(!$data['name']){
    		$this->error("请输入小组名称");
    		return;
    	}
    	$where = array(
    			"id"=>array(
    					"neq",
    					$id,
    			),
    			"name"=>$data['name'],
    	);
    	if(count($groupModel->where($where)->select())){
    		$this->error("小组名称重复");
    		return;
    	}
    	$data['responsable'] = I('post.responsable');
    	$responsable = $userModel->get($data['responsable']);
    	if(!$responsable){
    		$this->error("小组负责人信息输入错误");
    		return;
    	}
    	$data['responsable'] = $responsable['id'];
    	$data['leader'] = I('post.leader');
    	$leader = $clientModel->where(array(
    			"name"=>$data['leader']
    	))->find();
    	if(!$leader){
    		$this->error("小组组长输入错误");
    		return;
    	}
    	$data['leader'] = $leader['id'];
    	$result = $groupModel->where(array("id"=>$group['id']))->save($data);
    	if(is_numeric($result)){
    		$this->success("成功",U('/User/worker/listGroup'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function listGroup(){
    	if(!testClassified($this->user,"listGroup")){
    		$this->error("权限不足");
    		return;
    	}
    	$groupModel = D('ClientGroup');
    	$list = $groupModel->relation(true)->select();
    	$this->assign("list",$list);
    	$this->display();
    }
    public function detailGroup($id = null){
    	if(!testClassified($this->user,"detailGroup")){
    		$this->error("权限不足");
    		return;
    	}
    	$groupModel = D('ClientGroup');
    	$group = $groupModel->get($id);
    	$this->assign("group",$group);
    	$this->display();
    }
    public function addClientToGroup($id = null){
    	if(!testClassified($this->user,"addClientToGroup")){
    		$this->error("权限不足");
    		return;
    	}
    	$clientModel = D('Client');
    	$client = $clientModel->get($id);
    	if(!$client || $client['serveur']['id'] != $this->user['id']){
    		$this->error("非法访问");
    		return;
    	}
    	if($client['group']){
    		$this->error("已归属于某互助组");
    		return;
    	}
    	if(!IS_POST){
    		$this->assign("client",$client);
    		$this->display();
    		return;
    	}
    	$data['group'] = I('post.group');
    	$groupModel = D('ClientGroup');
    	$group = $groupModel->get($data['group']);
    	if(!$group){
    		$this->error("互助组信息输入错误");
    		return;
    	}
    	$where = array(
    			"id"=>$id,
    	);
    	$result = $clientModel->where($where)->save($data);
    	if($result){
    		$this->success("成功",U('/user/worker/selfDetail'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function deleteClientFromGroup($id = null){
    	if(!testClassified($this->user,"deleteClientFromGroup")){
    		$this->error("权限不足");
    		return;
    	}
    	$clientModel = D('Client');
    	$client = $clientModel->get($id);
    	if(!$client || $client['serveur'] != $this->user['id']){
    		$this->error("非法访问");
    		return;
    	}
    	/*
    	if(!IS_POST){
    		$this->assign("client",$client);
    		$this->display();
    		return;
    	}
    	*/
    	$data['group'] = "null";
    	$where = array(
    			"id"=>$id,
    	);
    	$result = $clientModel->where($where)->save($data);
    	if($result){
    		$this->success("成功",U('/user/worker/selfDetail'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function addPosition(){
    	if(!testClassified($this->user,"addPosition")){
    		$this->error("权限不足");
    		return;
    	}
    	$userModel = D("User");
    	if(!IS_POST){
    		$listUser = $userModel->relation(true)->select();
    		$this->assign("user",$listUser);
    		$this->display();
    		return;
    	}
    	$positionModel = D('Position');
    	$data['position'] = I('post.position');
    	if(!$data['position']){
    		$this->error("请输入地点名称");
    		return;
    	}
    	$where = array(
    			"position"=>$data['position'],
    	);
    	if(count($positionModel->where($where)->select())){
    		$this->error("地点名称重复");
    	}
    	
    	$data['adresse'] = I('post.adresse');
    	$data['responsable'] = I('post.responsable');
    	if(!$data['responsable']){
    		$this->error("请输入地点负责人");
    		return;
    	}
    	$responsable = $userModel->get($data['responsable']);
    	if(!$responsable){
    		$this->error("负责人输入错误，请核实");
    		return;
    	}
    	$data['responsable'] = $responsable['id'];
    	$result = $positionModel->add($data);
    	if($result){
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function deletePosition($id = null){
    	if(!testClassified($this->user,"deletePosition")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$positionModel = D('Position');
    	$position = $positionModel->get($id);
    	if(!$position){
    		$this->error("非法访问");
    		return;
    	}
    	/*
    	if(!IS_POST){
    		$this->assign("position",$position);
    		$this->display();
    		return;
    	}
    	*/
    	$where = array(
    			"id"=>$id,
    	);
    	$result = $positionModel->where($where)->delete();
    	if($result){
    		$this->success("成功",U('/User/admin/listPosition'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function changePosition($id = null){
    	if(!testClassified($this->user,"changePosition")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$positionModel = D('Position');
    	$position = $positionModel->get($id);
    	if(!$position){
    		$this->error("非法访问");
    		return;
    	}
    	$userModel = D("User");
    	if(!IS_POST){
    		$listUser = $userModel->relation(true)->select();
    		$this->assign("user",$listUser);
    		$this->assign("position",$position);
    		$this->display();
    		return;
    	}
    	$data['position'] = I('post.position');
    	if(!$data['position']){
    		$this->error("请输入地点名称");
    		return;
    	}
    	$where = array(
    			"id"=>array(
    					"neq",
    					$id,
    			),
    			"position"=>$data['position'],
    	);
    	if(count($positionModel->where($where)->select())){
    		$this->error("地点名称重复");
    	}
    	
    	$data['adresse'] = I('post.adresse');
    	$data['responsable'] = I('post.responsable');
    	if(!$data['responsable']){
    		$this->error("请输入地点负责人");
    		return;
    	}
    	$responsable = $userModel->get($data['responsable']);
    	if(!$responsable){
    		$this->error("负责人输入错误，请核实");
    		return;
    	}
    	$data['responsable'] = $responsable['id'];
    	$where = array(
    			"id"=>$id,
    	);
    	$result = $positionModel->where($where)->save($data);
    	if(is_numeric($result)){
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function listPosition(){
    	if(!testClassified($this->user,"listPosition")){
    		$this->error("权限不足");
    		return;
    	}
    	$positionModel = D("Position");
    	$list = $positionModel->relation(true)->select();
    	$this->assign("list",$list);
    	$this->display();
    }
    public function detailPosition($id = null){
    	if(!testClassified($this->user,"detailPosition")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$positionModel = D('Position');
    	$position = $positionModel->get($id);
    	if(!$position){
    		$this->error("非法访问");
    		return;
    	}
    	$this->assign("position",$position);
    	$this->display();
    }
    public function addClientToPosition($id = null){
    	if(!testClassified($this->user,"addClientToPosition")){
    		$this->error("权限不足");
    		return;
    	}
    	$clientModel = D('Client');
    	$client = $clientModel->get($id);
    	if(!$client || $client['serveur'] != $this->user['id']){
    		$this->error("非法访问");
    		return;
    	}
    	if(!IS_POST){
    		$this->assign("client",$client);
    		$this->display();
    		return;
    	}
    	$position = I('post.position');
    	foreach($position as $key => $value){
    		if(!$value){
    			unset($position[$key]);
    		}
    	}
    	$data = null;
    	foreach($position as $p){
    		$temp = array(
    				"client"=>$id,
    				"position"=>$p,
    		);
    		array_push($data,$temp);
    	}
    	$result = M('clientGoPosition')->addAll($data);
    	if($result){
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function deleteClientFromPosition($id = null,$position = null){
    	if(!testClassified($this->user,"deleteClientFromPosition")){
    		$this->error("权限不足");
    		return;
    	}
    	$model = M('clientGoPosition');
    	$where = array(
    			"client"=>$id,
    			"position"=>$position,
    	);
    	$result = $model->where->find();
    	if(!$result){
    		$this->error("非法访问");
    		return;
    	}
    	/*
    	if(!IS_POST){
    		$this->display();
    		return;
    	}
    	*/
    	$result = $model->where($where)->delete();
    	if($result){
    		$this->success("成功",U('/user/worker/selfDetail'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function addGroupToPosition($id = null){
    	if(!testClassified($this->user,"addGroupToPosition")){
    		$this->error("权限不足");
    		return;
    	}
    	$groupModel = D('Group');
    	$group = $groupModel->get($id);
    	if(!$group || $group['serveur'] != $this->user['id']){
    		$this->error("非法访问");
    		return;
    	}
    	if(!IS_POST){
    		$this->assign("group",$group);
    		$this->display();
    		return;
    	}
    	$position = I('post.position');
    	foreach($position as $key => $value){
    		if(!$value){
    			unset($position[$key]);
    		}
    	}
    	$data = null;
    	foreach($position as $p){
    		$temp = array(
    				"group"=>$id,
    				"position"=>$p,
    		);
    		array_push($data,$temp);
    	}
    	$result = M('groupPosition')->addAll($data);
    	if($result){
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function deleteGroupFromPosition(){
    	if(!testClassified($this->user,"deleteGroupFromPosition")){
    		$this->error("权限不足");
    		return;
    	}
    	$model = M('groupPosition');
    	$where = array(
    			"group"=>$id,
    			"position"=>$position,
    	);
    	$result = $model->where->find();
    	if(!$result){
    		$this->error("非法访问");
    		return;
    	}
    	/*
    	if(!IS_POST){
    		$this->display();
    		return;
    	}
    	*/
    	$result = $model->where($where)->delete();
    	if($result){
    		$this->success("成功",U('/user/worker/selfDetail'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function detailClient($id = null){
    	if(!testClassified($this->user,"detailClient")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$clientModel = D('Client');
    	$client = $clientModel->get($id);
    	if(!$client){
    		$this->error("非法访问");
    		return;
    	}
    	if(count($client['vip'])){
    		$cardNumber = array();
    		$vips = array();
    		foreach($client['vip'] as $v){
    			array_push($cardNumber,$v['cardnumber']);
    			array_push($vips,$v['id']);
    		}
    		$orderModel = D('Order');
    		$where = array(
    				"cardNumber"=>array(
    						"in",
    						$cardNumber
    				)
    		);
    		$client['order'] = $orderModel->where($where)->relation(true)->select();
    		$rechargerModel = D('Recharger');
    		$where = array(
    				"vip"=>array(
    						"in",
    						$vips
    				)
    		);
    		$client['recharger'] = $rechargerModel->where($where)->relation(true)->select();
    	}
    	$client['fois'] = D('Fois')->where(array("client"=>$client['id']))->relation(true)->select();
    	$this->assign("client",$client);
    	$this->display();
    }
    public function visitClient($id = null){
    	if(!testClassified($this->user,"detailClient")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$clientModel = D('Client');
    	$client = $clientModel->get($id);
    	if(!$client){
    		$this->error("非法访问");
    		return;
    	}
    	if(!IS_POST){
    		$this->assign("client",$client);
    		$this->display();
    		return;
    	}
    	$data['client'] = $client['id'];
    	$data['operator'] = $this->user['id'];
    	$data['time'] = I('post.time');
    	$data['content'] = I('post.content');
    	$data['type'] = I('post.type');
    	$model = M('recordVisit');
    	$result = $model->add($data);
    	if($result){
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function updateCache(){
    	$user = session("user");
    	$userModel = D('User');
    	$newUser = $userModel->get($user['id']);
    	session("user",$newUser);
    	unlink("./Runtime/Cache/User/".$user['id']);
    	$this->success("更新成功");
    }
    public function addPresenttime(){
    	if(!testClassified($this->user,"presenttime")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!IS_POST){
    		$card = D('Card')->select();
    		$this->assign("card",$card);
    		$this->display();
    		return;
    	}
    	$data = I('post.');
    	if(!$data['card']){
    		$this->error("卡类型必填");
    		return;
    	}
    	if(!$data['point']){
    		$data['point'] = 0;
    	}
    	if(!$data['timeBegin']){
    		unset($data['timeBegin']);
    	}
    	if(!$data['timeEnd']){
    		unset($data['timeEnd']);
    	}
    	try{
    		$result = M('presentPoint')->add($data);
    	}catch(Exception $e){
    		$this->error("此类型会员卡已添加过签到时间");
    	}
    	if($result){
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function listPresenttime(){
    	if(!testClassified($this->user,"listPresenttime")){
    		$this->error("权限不足");
    		return;
    	}
    	$list = M('presentPoint')->select();
    	$cardModel = D('Card');
    	for($i = 0;$list[$i];$i++){
    		$list[$i]['card'] = $cardModel->where(array("id"=>$list[$i]['card']))->find();
    	}
    	$this->assign("list",$list);
    	$this->display();
    }
}