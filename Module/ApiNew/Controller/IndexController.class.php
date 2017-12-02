<?php
namespace ApiNew\Controller;
class IndexController extends BaseController {
	public function __construct(){
		parent::__construct();
		/*
		if(!$this->user){
			$this->ajaxReturn(["status"=>false,"message"=>"login error"]);
			die;
		}
		*/
	}
	public function _empty(){
		$this->ajaxReturn(["status"=>false,"message"=>"url error"]);
	}
}