<?php
namespace User\Controller;
use User\Common\Controller\Base;
class AdminController extends Base {
	public function bindShop(){
		if(!testClassified($this->user,"bindShop")){
			$this->error("权限不足");
			return;
		}
		$shopModel = D('Shop');
		$list = $shopModel->relation(true)->select();
		$this->assign("shop",$list);
		$this->display();
	}
    public function addDepartement(){
    	if(!testClassified($this->user,"addDepartement")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!IS_POST){
    		$this->display();
    		return;
    	}
    	$name = I('post.name');
    	if(!$name){
    		$this->error("请输入部门名称");
    		return;
    	}
    	$departementModel = D("Departement");
    	$departement = $departementModel->where("departement='".$name."'")->find();
    	if($departement){
    		$this->error("部门名称已存在");
    		return;
    	}
    	$data = array(
    			"departement"=>$name,
    	);
    	$departement = $departementModel->add($data);
    	if(!$departement){
    		$this->error("错误");
    		return;
    	}
    	$operator = session("user");
    	$data = array(
    			"operator"=>$operator['id'],
    			"operation"=>"添加部门",
    			"content"=>"部门:".$data['departement'],
    			"time"=>date("Y-m-d H:i:s"),
    			"ip"=>get_client_ip(),
    	);
    	M('operationRecord')->add($data);
    	$this->success("成功",U("/user/admin/addDepartement"));
    }
    public function changeDepartement($id = null){
    	if(!testClassified($this->user,"changeDepartement")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$departementModel = D("Departement");
    	$departement = $departementModel->get($id);
    	if(!$departement){
    		$this->error("非法访问");
    		return;
    	}
    	if(!IS_POST){
    		$this->assign("departement",$departement);
    		$this->display();
    		return;
    	}
    	$name = I("post.name");
    	if(!$name){
    		$this->error("请输入部门名称");
    		return;
    	}
    	$where = array(
    			"departement"=>$name,
    			"id"=>array(
    					"neq",
    					$id,
    			),
    	);
    	$result = $departementModel->where($where)->select();
    	if(count($result)){
    		$this->error("部门名称重复");
    		return;
    	}
    	$data = array(
    			"departement"=>$name,
    	);
    	$where = array(
    			"id"=>$id,
    	);
    	$result = $departementModel->where($where)->save($data);
    	if(is_numeric($result)){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"修改部门",
    				"content"=>"部门:".$departement['departement'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功",U("/user/admin/listDepartement"));
    	}else{
    		$thsi->error("失败");
    	}
    }
    public function deleteDepartement($id = null){
    	if(!testClassified($this->user,"deleteDepartement")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$departementModel = D("Departement");
    	$departement = $departementModel->get($id);
    	if(!$departement){
    		$this->error("非法访问");
    		return;
    	}
    	/*
    	if(!IS_POST){
    		$this->assign("departement",$departement);
    		$this->display();
    		return;
    	}
    	*/
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
    		$this->success("成功",U("/user/admin/listDepartement"));
    	}else{
    		$thsi->error("失败");
    	}
    }
    public function listDepartement(){
    	if(!testClassified($this->user,"listDepartement")){
    		$this->error("权限不足");
    		return;
    	}
    	$departementModel = D("Departement");
    	$list = $departementModel->select();
    	$this->assign("list",$list);
    	$this->display();
    }
    public function detailDepartement($id = null){
    	if(!testClassified($this->user,"detailDepartement")){
    		$this->error("权限不足");
    		return;
    	}
    	$departementModel = D("Departement");
    	$departement = $departementModel->get($id);
    	if(!$departement){
    		$this->error("非法访问");
    		return;
    	}
    	$this->assign("departement",$departement);
    	$this->display();
    }
    public function addPost(){
    	if(!testClassified($this->user,"addPost")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!IS_POST){
    		$this->display();
    		return;
    	}
    	$name = I('post.name');
    	if(!$name){
    		$this->error("请输入职位名称");
    		return;
    	}
    	$postModel = D("Post");
    	$post = $postModel->where("post='".$name."'")->find();
    	if($post){
    		$this->error("职位名称已存在");
    		return;
    	}
    	$data = array(
    			"post"=>$name,
    	);
    	$post = $postModel->add($data);
    	if(!$post){
    		$this->error("错误");
    		return;
    	}
    	$operator = session("user");
    	$data = array(
    			"operator"=>$operator['id'],
    			"operation"=>"添加职位",
    			"content"=>"职位:".$data['post'],
    			"time"=>date("Y-m-d H:i:s"),
    			"ip"=>get_client_ip(),
    	);
    	M('operationRecord')->add($data);
    	$this->success("成功",U("/user/admin/addPost"));
    }
    public function changePost($id = null){
    	if(!testClassified($this->user,"changePost")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$postModel = D("Post");
    	$post = $postModel->get($id);
    	if(!$post){
    		$this->error("非法访问");
    		return;
    	}
    	if(!IS_POST){
    		$this->assign("post",$post);
    		$this->display();
    		return;
    	}
    	$name = I("post.name");
    	if(!$name){
    		$this->error("请输入职位名称");
    		return;
    	}
    	$where = array(
    			"post"=>$name,
    			"id"=>array(
    					"neq",
    					$id,
    			),
    	);
    	$result = $postModel->where($where)->select();
    	if(count($result)){
    		$this->error("职位名称重复");
    		return;
    	}
    	$data = array(
    			"post"=>$name,
    	);
    	$where = array(
    			"id"=>$id,
    	);
    	$result = $postModel->where($where)->save($data);
    	if(is_numeric($result)){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"修改职位",
    				"content"=>"职位:".$post['post'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功",U("/user/admin/listPost"));
    	}else{
    		$thsi->error("失败");
    	}
    }
    public function deletePost($id = null){
    	if(!testClassified($this->user,"deletePost")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$postModel = D("Post");
    	$post = $postModel->get($id);
    	if(!$post){
    		$this->error("非法访问");
    		return;
    	}
    	/*
    	if(!IS_POST){
    		$this->assign("post",$post);
    		$this->display();
    		return;
    	}
    	*/
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
    		$this->success("成功",U("/user/admin/listPost"));
    	}else{
    		$thsi->error("失败");
    	}
    }
    public function listPost(){
    	if(!testClassified($this->user,"listPost")){
    		$this->error("权限不足");
    		return;
    	}
    	$postModel = D("Post");
    	$list = $postModel->select();
    	$this->assign("list",$list);
    	$this->display();
    }
    public function detailPost($id = null){
    	if(!testClassified($this->user,"detailPost")){
    		$this->error("权限不足");
    		return;
    	}
    	$postModel = D("Post");
    	$post = $postModel->get($id);
    	if(!$post){
    		$this->error("非法访问");
    		return;
    	}
    	$this->assign("post",$post);
    	$this->display();
    }
    public function addWorker(){
    	if(!testClassified($this->user,"addWorker")){
    		$this->error("权限不足");
    		return;
    	}
    	$userModel = D("User");
    	$departementModel = D('Departement');
    	$postModel = D('Post');
    	if(!IS_POST){
    		$listUser = $userModel->relation(true)->select();
    		$listDepartement = $departementModel->relation(true)->select();
    		$listPost = $postModel->relation(true)->select();
    		$this->assign("departement",$listDepartement);
    		$this->assign("post",$listPost);
    		$this->assign("user",$listUser);
    		$this->display();
    		return;
    	}
    	$data['name'] = I('post.name');
    	if(!$data['name']){
    		$this->error("请输入姓名");
    		return;
    	}
    	$data['sex'] = I('post.sex');
    	if(!$data['sex']){
    		$this->error("请输入性别");
    		return;
    	}
    	$data['nation'] = I('post.nation');
    	if(!$data['nation']){
    		$this->error("请输入民族");
    		return;
    	}
    	$data['dateBirth'] = I('post.dateBirth');
    	if(!$data['dateBirth']){
    		$this->error("请输入出生日期");
    		return;
    	}
    	$data['dateJoin'] = I('post.dateJoin');
    	if(!$data['dateJoin']){
    		$this->error("请输入入职日期");
    		return;
    	}
    	$data['post'] = I('post.post');
    	if(!$data['post']){
    		$this->error("请输入职位");
    		return;
    	}
    	$post = $postModel->get($data['post']);
    	if(!$post){
    		$this->error("职位信息输入错误");
    		return;
    	}
    	$data['post'] = $post['id'];
    	$data['departement'] = I('post.departement');
    	if(!$data['departement']){
    		$this->error("请输入部门");
    		return;
    	}
    	$departement = $departementModel->get($data['departement']);
    	if(!$departement){
    		$this->error("部门信息输入错误");
    		return;
    	}
    	$data['departement'] = $departement['id'];
    	$data['boss'] = I('post.boss');
    	if(!$data['boss']){
    		unset($data['boss']);
    	}else{
    		$boss = $userModel->get($data['boss']);
    		if(!$boss){
    			$this->error("上级主管人员输入错误");
    			return;
    		}
    		$data['boss'] = $boss['id'];
    	}
    	$data['tel'] = I('post.tel');
    	$result = $userModel->add($data);
    	if($result){
    		$loginModel = M('login');
    		unset($data);
    		$data['login'] = I('post.name').rand(1000,9999);
    		$data['pwd'] = md5("888888".C('CHIFFREMENT_CODE'));
    		$data['user'] = $result;
    		$result = $loginModel->add($data);
    		//$this->success("成功",U('/user/admin/addWorker'));
    		$this->assign("login",$data['login']);
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"添加员工",
    				"content"=>"员工:".I('post.name'),
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->display("addWorkerOk");
    	}else{
    		$this->error("失败");
    	}
    }
    public function changeWorker($id = null){
    	if(!testClassified($this->user,"addWorker")){
    		$this->error("权限不足");
    		return;
    	}
    	$userModel = D("User");
    	$postModel = D('Post');
    	$departementModel = D('Departement');
    	$user = $userModel->get($id);
    	if(!$user){
    		$this->error("非法访问");
    		return;
    	}
    	if(!IS_POST){
    		$listUser = $userModel->relation(true)->select();
    		$listPost = $postModel->relation(true)->select();
    		$listDepartement = $departementModel->relation(true)->select();
    		$this->assign("post",$listPost);
    		$this->assign("departement",$listDepartement);
    		$this->assign("boss",$listUser);
    		$this->assign("user",$user);
    		$this->display();
    		return;
    	}
    	$data['name'] = I('post.name');
    	if(!$data['name']){
    		$this->error("请输入姓名");
    		return;
    	}
    	$data['sex'] = I('post.sex');
    	if(!$data['sex']){
    		$this->error("请输入性别");
    		return;
    	}
    	$data['nation'] = I('post.nation');
    	if(!$data['nation']){
    		$this->error("请输入民族");
    		return;
    	}
    	$data['dateBirth'] = I('post.dateBirth');
    	if(!$data['dateBirth']){
    		$this->error("请输入出生日期");
    		return;
    	}
    	$data['dateJoin'] = I('post.dateJoin');
    	if(!$data['dateJoin']){
    		$this->error("请输入入职日期");
    		return;
    	}
    	$data['post'] = I('post.post');
    	if(!$data['post']){
    		$this->error("请输入职位");
    		return;
    	}
    	$post = $postModel->get($data['post']);
    	if(!$post){
    		$this->error("职位信息输入错误");
    		return;
    	}
    	$data['post'] = $post['id'];
    	$data['departement'] = I('post.departement');
    	if(!$data['departement']){
    		$this->error("请输入部门");
    		return;
    	}
    	$departement = $departementModel->get($data['departement']);
    	if(!$departement){
    		$this->error("部门信息输入错误");
    		return;
    	}
    	$data['departement'] = $departement['id'];
    	$data['boss'] = I('post.boss');
    	if(!$data['boss']){
    		unset($data['boss']);
    	}else{
    		$boss = $userModel->get($data['boss']);
    		if(!$boss){
    			$this->error("上级主管人员输入错误");
    			return;
    		}
    		$data['boss'] = $boss['id'];
    	}
    	$result = $userModel->changeUser($id,$data);
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"修改员工",
    				"content"=>"员工:".$user['name'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功",U('/user/admin/listWorker'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function deleteWorker($id = null){
    	if(!testClassified($this->user,"deleteWorker")){
    		$this->error("权限不足");
    		return;
    	}
    	$userModel = D("User");
    	$user = $userModel->get($id);
    	if(!$user){
    		$this->error("非法访问");
    		return;
    	}
    	if(!IS_POST){
    		$this->assign("user",$user);
    		$this->display();
    		return;
    	}
    	$result = $userModel->deleteUser($id);
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"删除员工",
    				"content"=>"员工:".$user['name'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功",U('/user/admin/listWorker'));
    	}else{
    		
    		$this->error("失败");
    	}
    }
    public function listWorker(){
    	if(!testClassified($this->user,"listWorker")){
    		$this->error("权限不足");
    		return;
    	}
    	$userModel = D('User');
    	$list = $userModel->relation(true)->select();
    	$this->assign("list",$list);
    	$this->display();
    }
    public function detailWorker($id = null){
    	if(!testClassified($this->user,"detailWorker")){
    		$this->error("权限不足");
    		return;
    	}
    	$userModel = D('User');
    	$user = $userModel->get($id);
    	if(!$user){
    		$this->error("非法访问");
    		return;
    	}
    	$this->assign("user",$user);
    	$this->display();
    }
    public function classifiedWorker(){
    	if(!testClassified($this->user,"classifiedWorker")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!IS_POST){
    		$this->display();
    		return;
    	}
    	$post = I('post.');
    	$user = $post['user'];
    	unset($post['user']);
    	$userModel = D('User');
    	$user = $userModel->get($user);
    	if(!$user){
    		$this->error("请输入员工信息");
    		return;
    	}
    	unlink("./Runtime/Cache/User/".$user['id']);
    	$model = M('userClassified');
    	$where = array(
    			"user"=>$user['id'],
    	);
    	$model->where($where)->delete();
    	$data = array();
    	foreach($post as $key=>$value){
    		$temp = array(
    				"user"=>$user['id'],
    				"classified"=>$key
    		);
    		array_push($data,$temp);
    	}
    	if(!count($data)){
    		$this->success("修改成功");
    		return;
    	}
    	$result = $model->addAll($data);
    	if($result){
    		$this->success("修改成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function workerPwdReset($id = null){
    	if(!testClassified($this->user,"workerPwdReset")){
    		$this->error("权限不足");
    		return;
    	}
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$userModel = D("User");
    	$user = $userModel->get($id);
    	if(!$user){
    		$this->error("非法访问");
    		return;
    	}
    	if(!IS_POST){
    		$this->assign("user",$user);
    		$this->display();
    		return;
    	}
    	$data['pwd'] = md5("888888".C("CHIFFREMENT_CODE"));
    	$loginModel = D("login");
    	$result = $loginModel->changeLogin($id,$data);
    	if(is_numeric($result)){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"重置员工登陆密码",
    				"content"=>"员工:".$user['name'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功",U('/user/admin/listWorker'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function listOperationRecord(){
    	if(!testClassified($this->user,"listOperationRecord")){
    		$this->error("权限不足");
    		return;
    	}
    	$this->display();
    }
}
