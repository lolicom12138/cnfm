<?php
namespace Api\Controller;
use Think\Controller;
class ChiffrementController extends Controller {
	public function chiffrementPwd(){
		if(!I('post.pwd')){
			return;
		}
		$checkCode = time().rand(1000,9999);
		makeCache($checkCode,I('post.pwd'),"Runtime/Login");
		$str = md5(I('post.pwd').time().rand(1000,9999));
		$json = '{
			"pwd":"'.$str.'",
			"code":"'.$checkCode.'"
					
		}';
		echo $json;
	}
}