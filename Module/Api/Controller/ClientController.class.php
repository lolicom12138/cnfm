<?php
namespace Api\Controller;
use Think\Controller;
class ClientController extends Controller {
	public function getClientByIdentity(){
		if(!IS_POST){
			echo '{
    			"status":"false"
    		}';
			return;
		}
		$clientModel = D('Client');
		$where = array(
				"identity"=>I('post.client'),
		);
		$result = $clientModel->where($where)->find();
		if(!$result){
			echo '{
				"status":"false"		
			}';
			return;
		}
		echo '{
				"status":"ok",
				"client":{
					"id":"'.$result['id'].'",
					"name":"'.$result['name'].'",
					"sex":"'.$result['sex'].'",
					"dateBirth":"'.$result['datebirth'].'",
					"identity":"'.$result['identity'].'"
				}
		}';
	}
    public function getClient(){
    	if(!IS_POST){
    		echo '{
    			"status":"false"
    		}';
    		return;
    	}
    	$clientModel = D('Client');
    	$where = array(
    			"name"=>I('post.client'),
    	);
    	$result = $clientModel->where($where)->select();
    	if(!count($result)){
    		$where = array(
    				"id"=>I('post.client')
    		);
    		$result = $clientModel->where($where)->select();
    		if(!count($result)){
    			$where = array(
    					"identity"=>I('post.client')
    			);
    			$result = $clientModel->where($where)->select();
    		}
    	}
    	$nombre = count($result);
    	if(!$nombre){
    		echo '{
    			"status":"false"
    		}';
    		return;
    	}
    	if($nombre == 1){
    		echo '{
    			"status":"ok"
    		}';
    		return;
    	}
    	if($nombre > 1){
    		echo '{
    				"status":"repeat",
    				"client":[';
    		$i = 0;
    		while($i<$nombre){
    			echo '{';
    			echo '"id":"'.$result[$i]['id'].'",';
    			echo '"identity":"'.$result[$i]['identity'].'"';
    			echo '}';
    			if($i != $nombre-1){
    				echo ',';
    			}
    			$i++;
    		}
    		echo ']}';
    	}
    }
    /**
     * 检测添加客户时客户手机号码是否重复
     */
    public function checkClientMobile(){
    	echo "ok";
    }
    /**
     * 检测添加客户时客户身份证号码是否重复
     */
    public function checkClientIdentity(){
    	echo "not ok";
    }
    public function deleteDisease(){
    	if(!IS_POST){
    		echo '{
    			"status":"false"
    		}';
    		return;
    	}
    	$idClient = I('post.client');
    	$idDisease = I('post.disease');
    	$clientModel = D('Client');
    	$client = $clientModel->get($idClient);
    	if(!$client){
    		echo '{
    			"status":"client false"
    		}';
    		return;
    	}
    	$diseaseModel = M('clientDisease');
    	$where = array(
    			"id"=>$idDisease
    	);
    	$disease = $diseaseModel->where($where)->find();
    	if(!$disease){
    		echo '{
    			"status":"disease false"
    		}';
    		return;
    	}
    	if($disease['client'] != $client['id']){
    		echo '{
    			"status":"disease client not match"
    		}';
    		return;
    	}
    	$result = $diseaseModel->where($where)->delete();
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"删除用户疾病",
    				"content"=>"删除".$disease['disease'],
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
    		return;
    	}
    }
    public function newDisease(){
    	if(!IS_POST){
    		echo '{
    			"status":"false"
    		}';
    		return;
    	}
    	$idClient = I('post.client');
    	$newDisease = I('post.disease');
    	$clientModel = D('Client');
    	$client = $clientModel->get($idClient);
    	if(!$client){
    		echo '{
    			"status":"client false"
    		}';
    		return;
    	}
    	$diseaseModel = M('clientDisease');
    	$where = array(
    			"disease"=>$newDisease
    	);
    	$disease = $diseaseModel->where($where)->find();
    	if($disease){
    		echo '{
    			"status":"already"
    		}';
    		return;
    	}
    	$data = array(
    			"client"=>$client['id'],
    			"disease"=>$newDisease,
    	);
    	$result = $diseaseModel->add($data);
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"添加用户疾病",
    				"content"=>"添加".$newDisease,
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
    public function deletePharm(){
    	if(!IS_POST){
    		echo '{
    			"status":"false"
    		}';
    		return;
    	}
    	$idClient = I('post.client');
    	$idPharm = I('post.pharm');
    	$clientModel = D('Client');
    	$client = $clientModel->get($idClient);
    	if(!$client){
    		echo '{
    			"status":"client false"
    		}';
    		return;
    	}
    	$pharmModel = M('clientPharm');
    	$where = array(
    			"id"=>$idPharm
    	);
    	$pharm = $pharmModel->where($where)->find();
    	if(!$pharm){
    		echo '{
    			"status":"pharm false"
    		}';
    		return;
    	}
    	if($pharm['client'] != $client['id']){
    		echo '{
    			"status":"pharm client not match"
    		}';
    		return;
    	}
    	$result = $pharmModel->where($where)->delete();
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"删除用户常用药物",
    				"content"=>"删除".$pharm['pharm'],
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
    		return;
    	}
    }
    public function newPharm(){
    	if(!IS_POST){
    		echo '{
    			"status":"false"
    		}';
    		return;
    	}
    	$idClient = I('post.client');
    	$newPharm = I('post.pharm');
    	$clientModel = D('Client');
    	$client = $clientModel->get($idClient);
    	if(!$client){
    		echo '{
    			"status":"client false"
    		}';
    		return;
    	}
    	$pharmModel = M('clientPharm');
    	$where = array(
    			"pharm"=>$newPharm
    	);
    	$pharm = $pharmModel->where($where)->find();
    	if($pharm){
    		echo '{
    			"status":"already"
    		}';
    		return;
    	}
    	$data = array(
    			"client"=>$client['id'],
    			"pharm"=>$newPharm,
    	);
    	$result = $pharmModel->add($data);
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"添加用户常用药物",
    				"content"=>"添加".$newPharm,
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
    public function deleteHabbit(){
    	if(!IS_POST){
    		echo '{
    			"status":"false"
    		}';
    		return;
    	}
    	$idClient = I('post.client');
    	$idHabbit = I('post.habbit');
    	$clientModel = D('Client');
    	$client = $clientModel->get($idClient);
    	if(!$client){
    		echo '{
    			"status":"client false"
    		}';
    		return;
    	}
    	$habbitModel = M('clientHabbit');
    	$where = array(
    			"id"=>$idHabbit
    	);
    	$habbit = $habbitModel->where($where)->find();
    	if(!$habbit){
    		echo '{
    			"status":"habbit false"
    		}';
    		return;
    	}
    	if($habbit['client'] != $client['id']){
    		echo '{
    			"status":"habbit client not match"
    		}';
    		return;
    	}
    	$result = $habbitModel->where($where)->delete();
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"删除用户兴趣爱好",
    				"content"=>"删除".$habbit['habbit'],
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
    		return;
    	}
    }
    public function newHabbit(){
    	if(!IS_POST){
    		echo '{
    			"status":"false"
    		}';
    		return;
    	}
    	$idClient = I('post.client');
    	$newHabbit = I('post.habbit');
    	$clientModel = D('Client');
    	$client = $clientModel->get($idClient);
    	if(!$client){
    		echo '{
    			"status":"client false"
    		}';
    		return;
    	}
    	$habbitModel = M('clientHabbit');
    	$where = array(
    			"habbit"=>$newHabbit
    	);
    	$habbit = $habbitModel->where($where)->find();
    	if($habbit){
    		echo '{
    			"status":"already"
    		}';
    		return;
    	}
    	$data = array(
    			"client"=>$client['id'],
    			"habbit"=>$newHabbit,
    	);
    	$result = $habbitModel->add($data);
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"添加用户兴趣爱好",
    				"content"=>"添加".$newHabbit,
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
    public function deleteFinance(){
    	if(!IS_POST){
    		echo '{
					"status":"false"
				}';
    		return;
    	}
    	$idClient = I('post.client');
    	$idFinance = I('post.financing');
    	$clientModel = D('Client');
    	$client = $clientModel->get($idClient);
    	if(!$client){
    		echo '{
					"status":"client false"
				}';
    		return;
    	}
    	$financingModel = M('clientFinance');
    	$where = array(
    			"id"=>$idFinance
    	);
    	$financing = $financingModel->where($where)->find();
    	if(!$financing){
    		echo '{
					"status":"financing false"
				}';
    		return;
    	}
    	if($financing['client'] != $client['id']){
    		echo '{
					"status":"financing client not match"
				}';
    		return;
    	}
    	$result = $financingModel->where($where)->delete();
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"删除用户理财方式",
    				"content"=>"删除".$financing['financing'],
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
    		return;
    	}
    }
    public function newFinance(){
    	if(!IS_POST){
    		echo '{
					"status":"false"
				}';
    		return;
    	}
    	$idClient = I('post.client');
    	$newFinance = I('post.financing');
    	$clientModel = D('Client');
    	$client = $clientModel->get($idClient);
    	if(!$client){
    		echo '{
					"status":"client false"
				}';
    		return;
    	}
    	$financingModel = M('clientFinance');
    	$where = array(
    			"financing"=>$newFinance
    	);
    	$financing = $financingModel->where($where)->find();
    	if($financing){
    		echo '{
					"status":"already"
				}';
    		return;
    	}
    	$data = array(
    			"client"=>$client['id'],
    			"financing"=>$newFinance,
    	);
    	$result = $financingModel->add($data);
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"添加用户理财方式",
    				"content"=>"添加".$newFinance,
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
    public function deleteHealthProduct(){
    	if(!IS_POST){
    		apiReturn("false");
    		return;
    	}
    	$idClient = I('post.client');
    	$idHealthProduct = I('post.healthProduct');
    	$clientModel = D('Client');
    	$client = $clientModel->get($idClient);
    	if(!$client){
    		apiReturn("client false");
    		return;
    	}
    	$healthProductModel = M('clientHealthProduct');
    	$where = array(
    			"id"=>$idHealthProduct
    	);
    	$healthProduct = $healthProductModel->where($where)->find();
    	if(!$healthProduct){
    		apiReturn("healthProduct false");
    		return;
    	}
    	if($healthProduct['client'] != $client['id']){
    		apiReturn("healthProduct client not match");
    		return;
    	}
    	$result = $healthProductModel->where($where)->delete();
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"删除用户常用保健品",
    				"content"=>"删除".$healthProduct['product'],
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		apiReturn("ok");
    	}else{
    		apiReturn("fail");
    		return;
    	}
    }
    public function newHealthProduct(){
    	if(!IS_POST){
    		apiReturn("false");
    		return;
    	}
    	$idClient = I('post.client');
    	$newHealthProduct = I('post.healthProduct');
    	$clientModel = D('Client');
    	$client = $clientModel->get($idClient);
    	if(!$client){
    		apiReturn("client false");
    		return;
    	}
    	$healthProductModel = M('clientHealthProduct');
    	$where = array(
    			"product"=>$newHealthProduct
    	);
    	$healthProduct = $healthProductModel->where($where)->find();
    	if($healthProduct){
    		apiReturn("already");
    		return;
    	}
    	$data = array(
    			"client"=>$client['id'],
    			"product"=>$newHealthProduct,
    	);
    	$result = $healthProductModel->add($data);
    	if($result){
    		$operator = session("user");
    		$data = array(
    				"operator"=>$operator['id'],
    				"operation"=>"添加用户常用保健品",
    				"content"=>"添加".$newHealthProduct,
    				"time"=>date("Y-m-d H:i:s"),
    				"ip"=>get_client_ip(),
    		);
    		M('operationRecord')->add($data);
    		apiReturn("ok");
    	}else{
    		apiReturn("fail");
    	}
    }
    public function updateProfile(){
    	if(!IS_POST){
    		apiReturn("false");
    		return;
    	}
    	$idClient = I('post.client');
    	$data['marriage'] = I("post.marriage");
    	$data['marriageTime'] = I("post.marriageTime");
    	$data['tall'] = I("post.tall");
    	$data['amountPerMonth'] = I("post.amountPerMonth");
    	$data['majorAreaBuy'] = I("post.majorAreaBuy");
    	$data['majorLocationBuy'] = I("post.majorLocationBuy");
    	$data['son'] = I("post.son");
    	$data['daughter'] = I("post.daughter");
    	$data['typeLive'] = I("post.typeLive");
    	$data['politicalStatus'] = I("post.politicalStatus");
    	$data['etc'] = I("post.etc");
    	$clientModel = D('Client');
    	$client = $clientModel->get($idClient);
    	if(!$client){
    		apiReturn("client false");
    		return;
    	}
    	$profileModel = M('clientProfile');
    	$result = $profileModel->where(array("client"=>$client['id']))->save($data);
    	if(is_numeric($result)){
    		apiReturn("ok");
    	}else{
    		apiReturn("fail");
    	}
    }
}