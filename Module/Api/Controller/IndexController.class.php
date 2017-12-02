<?php
namespace Api\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
		$model = D('user');
		$result = $model->select();
		dump($result);
		dump($model);
		$model->getClass();
    }
}