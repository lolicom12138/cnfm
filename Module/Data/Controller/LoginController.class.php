<?php
namespace Data\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index(){
    	if(!IS_POST){
    		$user = $this->testSession();
    		if($user){
    			header("location: ".U('/data'));
    		}
    		$this->display();
    		return;
    	}
        $login = I('post.login');
        $pwd = I('post.pwd');
        $verify = I('post.verify');
      	$code = I('post.code');
      	if(!file_exists("./Runtime/login/".$code)){
      		$this->error("密码输入错误1");
      		return;
      	}
      	$pwdOrigin = unserialize(file_get_contents("./Runtime/login/".$code));
        if(!checkVerify($verify)){
        	$this->error("验证码错误");
        	return;
        }
        if(!$login){
        	$this->error("请输入登录名");
        	return;
        }
        if(!$pwd){
        	$this->error("请输入登陆密码");
        	return;
        }
        if(!file_exists("./Login/".$login)){
        	$loginModel = M('login');
        	$where = array(
        			"login"=>$login,
        	);
        	$result = $loginModel->where($where)->find();
        	if(!$result){
        		$this->error("登录名输入错误");
        		return;
        	}
        	makeCache($login,$result,"Runtime/Cache/Login");
        	if($result['pwd'] != md5($pwdOrigin.C('CHIFFREMENT_CODE'))){
        		$this->error("密码输入错误3");
        		return;
        	}
        }else{
        	$result= unserialize(file_get_contents("./Login/".$login));
        	if($result['pwd'] != md5($pwdOrigin.C("CHIFFREMENT_CODE"))){
        		$this->error("密码输入错误3");
        		return;
        	}
        }
        if(!file_exists("./User/".$result['id'])){
        	$userModel = D('User');
        	$where = array(
        			"id"=>$result['user'],
        	);
        	$user = $userModel->where($where)->relation(true)->find();
        	if(!$user){
        		$this->error("登陆失败，请重试");
        		return;
        	}
        	makeCache($user['id'],$user,"Runtime/Cache/User");
        }else{
        	$user = unserialize(file_get_contents("./User/".$result['id']));
        }
    	if(!testClassified($user,"DATA")){
    		$this->error("权限不足");
    		return;
    	}
        session('user',$user);
        session('expire',time()+1200);
        header("location: ".U('/data'));
    }
    public function logout(){
    	session("user",null);
    	header("location: ".U('/data/login'));
    }
	private function testSession(){
		if(!session('user')){
			return;
		}
		if(session('expire') < time()){
			return;
		}
		$user = session('user');
		foreach($user['classified'] as $c){
			if($c['classified'] == 'DATA'){
				session('expire',time()+1200);
				return session('user');
			}
		}
		return;
	}
}