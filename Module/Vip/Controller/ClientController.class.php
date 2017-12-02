<?php
namespace Vip\Controller;
use Vip\Common\Controller\Base;
class ClientController extends Base {
    public function addClient(){
    	if(!testClassified($this->user,"addClient")){
    		$this->error("权限不足");
    		return;
    	}
    	$userModel = D('User');
    	if(!IS_POST){
    		$listUser = $userModel->select();
    		$this->assign("user",$listUser);
    		$this->display();
    		return;
    	}
    	$clientModel = D('Client');
    	$data['name'] = I('post.name');
    	if(!$data['name']){
    		$this->error("请输入客户姓名");
    		return;
    	}
    	
    	$data['identity'] = I('post.identity');
   		if($data['identity'] && strlen($data['identity']) != 18){
   			$this->error("身份证号码格式输入错误");
   			return;
   		}
   		$where = array(
   				"identity"=>$identity,
   		);
   		if(count($clientModel->where($where)->select())){
   			$this->error("身份证号码重复");
   			return;
   		}
    	$data['adresse'] = I('post.adresse');
    	$data['nation'] = I('post.nation');
    	$data['dateBirth'] = I('post.dateBirth');
    	$data['sex'] = I('post.sex');
    	$sex = array("男","女");
    	if(!in_array($data['sex'],$sex)){
    		$this->error("客户性别输入错误");
    		return;
    	}
    	$data['tel'] = I('post.tel');
    	if(strlen($data['tel']) != 11){
    		$this->error("客户手机号码输入错误");
    	}
    	$where = array(
    			"tel"=>$data['tel'],
    	);
    	if(count($clientModel->where($where)->select())){
    		$this->error("手机号码已被使用");
    		return;
    	}
    	$data['group'] = I('post.group');
    	if(!$data['group']){
    		unset($data['group']);
    	}else{
    		$groupModel = D('ClientGroup');
    		$group = $groupModel->get($data['group']);
    		if(!$group){
    			$this->error("互助组信息输入错误");
    			return;
    		}
    		$data['group'] = $group['id'];
    	}
    	$data['serveur'] = I('post.serveur');
    	
    	if(!$userModel->get($data['serveur'])){
    		$this->error("服务人信息输入错误");
    		return;
    	}
    	$data['etc'] = I('post.etc');
    	$result = $clientModel->add($data);
    	if($result){
    		if(I('post.invite')){
    			$clientInv = $clientModel->get(I('post.invite'));
    			if(!$clientInv){
    				$vip = D('Vip')->get(I('post.invite'));
    				if($vip){
    					$clientId = $vip['client']['id'];
    				}
    			}else{
    				$clientId = $clientInv['id'];		
    			}
    			if(isset($clientId)){
    				$data = array(
    						"client"=>$result,
    						"introducer"=>$clientId
    				);
    				M('clientInvitation')->add($data);
    			}
    		}
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"添加会员",
    				"content"=>"会员:".I('post.name'),
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功",U('/vip/client/addClient'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function changeClient($id = null){
    	if(!testClassified($this->user,"changeClient")){
    		$this->error("权限不足");
    		return;
    	}
    	$clientModel = D('Client');
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$client = $clientModel->get($id);
    	if(!$client){
    		$this->error("非法访问");
    		return;
    	}
    	$userModel = D('User');
    	if(!IS_POST){
    		$listUser = $userModel->select();
    		$this->assign("user",$listUser);
    		$this->assign("client",$client);
    		$this->display();
    		return;
    	}
    	
    	$data['name'] = I('post.name');
    	if(!$data['name']){
    		$this->error("请输入客户姓名");
    		return;
    	}
    	$data['identity'] = I('post.identity');
    	if($data['identity'] && strlen($data['identity']) != 18){
    		$this->error("身份证号码格式输入错误");
    		return;
    	}
    	$where = array(
    			"identity"=>$identity,
    			"id"=>array(
    					"neq",
    					$id,
    			),
    	);
    	if(count($userModel->where($where)->select)){
    		$this->error("身份证号码重复");
    		return;
    	}
    	$data['adresse'] = I('post.adresse');
    	$data['nation'] = I('post.nation');
    	$data['dateBirth'] = I('post.dateBirth');
    	$data['sex'] = I('post.sex');
    	$sex = array("男","女");
    	if(!in_array($data['sex'],$sex)){
    		$this->error("客户性别输入错误");
    		return;
    	}
    	$data['tel'] = I('post.tel');
    	if(strlen($data['tel']) != 11){
    		$this->error("客户手机号码输入错误");
    	}
    	$where = array(
    			"tel"=>$data['tel'],
    			"id"=>array(
    					"neq",
    					$id,
    			),
    	);
    	if(count($userModel->where($where)->select())){
    		$this->error("手机号码已被使用");
    		return;
    	}
    	$data['group'] = I('post.group');
    	if(!$data['group']){
    		unset($data['group']);
    	}
    	$data['serveur'] = I('post.serveur');
    	if(!$userModel->get($data['serveur'])){
    		$this->error("服务人信息输入错误");
    		return;
    	}
    	$data['etc'] = I('post.etc');
    	$where = array(
    			"id"=>$client['id']
    	);
    	$result = $clientModel->where($where)->save($data);
    	if(is_numeric($result)){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"修改会员",
    				"content"=>"会员:".$client['name'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功");
    	}else{
    		$this->error("失败");
    	}
    }
    public function deleteClient($id = null){
    	if(!testClassified($this->user,"deleteClient")){
    		$this->error("权限不足");
    		return;
    	}
    	$clientModel = D('Client');
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
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
    	$where = array(
    			"id"=>$id,
    	);
    	$result = $clientModel->where($where)->delete();
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"删除会员",
    				"content"=>"会员:".$client['name'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		$this->success("成功",U('/vip/statistic/listClient'));
    	}else{
    		$this->error("失败");
    	}
    }
    public function detailClient($id = null){
    	if(!testClassified($this->user,"detailClient")){
    		$this->error("权限不足");
    		return;
    	}
    	$clientModel = D('Client');
    	if(!$id){
    		$this->error("非法访问");
    		return;
    	}
    	$client = $clientModel->get($id);
    	if(!$client){
    		$this->error("非法访问");
    		return;
    	}
    	if($client['beinvited']['introducer']){
    		$client['beinvited'] = $clientModel->get($client['beinvited']['introducer']);
    	}
    	foreach($client['invite'] as $key=>$value){
    		$client['invite'][$key] = $clientModel->get($value['client']);
    	}
    	$this->assign("client",$client);
    	$this->display();
    }
}