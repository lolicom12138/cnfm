<?php
	function checkVerify($code, $id = ''){
		$verify = new \Think\Verify();
		/*
		$result = $verify->check($code, $id);
		if(rand(1,10) > 7){
			return $result;
		}*/
		//return rand(1,10)>5?$verify->check($code,$id):false;
		return $verify->check($code, $id);
	}
	function makeCache($name,$value = null,$path = ""){
		$dir = $path?"./".$path:".";
		if(!is_dir($dir)){
			mkdirs($path);
		}
		if(file_exists($dir."/".$name)){
			unlink($dir."/".$name);
		}
		if(!$value){
			return;
		}
		$str = serialize($value);
		file_put_contents($dir."/".$name, $str);
	}
	function mkdirs($path,$root = "./"){
		$pathTemp = explode("/",$path,2);
		mkdir($root."/".$pathTemp[0]);
		if($pathTemp[1]){
			mkdirs($pathTemp[1],$root."/".$pathTemp[0]);
		}
	}
	function testClassified($user,$classified = null){
		
		//return true;
		if($user['login']['login'] == "admin"){
			return true;
		}
		
		if(!$classified){
			return true;
		}
		if((!is_string($classified)) || !isset($user['classified'])){
			echo 1;
			return false;
		}
		foreach($user['classified'] as $c){
			if($c['classified'] == $classified){
				return true;
			}
		}
		return false;
	}
	function actionRecord($data){
		if(!$data || !is_array($data) || !count($data)){
			return;
		}
		$recordModel = M('operationRecord');
		$result = $recordModel->add($data);
		if($result){
			return true;
		}
		return;
	}
	function chiffrement($str){
		return md5($str.C('CHIFFREMENT_CODE'));
	}
	function affichePost($id){
		$postModel = D('Post');
		$post = $postModel->get($id);
		echo $post['post'];
	}
	function afficheDepartement($id){
		$departementModel = D('Departement');
		$departement = $departementModel->get($id);
		echo $departement['departement'];
	}
	function apiReturn($str){
		if(!is_array($str)){
			if(!is_string($str)){
				echo '{
				"status":"function error"
				}';
			}else{
				echo '{
					"status":"'.$str.'"
				}';
			}
		}else{
			echo '{';
			$length = count($str);
			$i = 1;
			foreach($str as $key=>$value){
				echo '"'.$key.'":';
				if(is_array($value)){
					echo '[';
					apiReturn($value);
					echo ']';
				}else{
					echo '"'.$value.'"';
				}
				if($i != $length){
					echo ',';
				}
				$i++;
			}
			echo '}';
		}
	}
	function operationRecord($data){
		$recorddModel = M('operationRecord');
		$recorddModel->add($data);
	}
	function updateCache(){
		$user = session("user");
		$userModel = D('User');
		$newUser = $userModel->get($user['id']);
		session("user",$newUser);
		unlink("./Runtime/Cache/User/".$user['id']);
	}
?>