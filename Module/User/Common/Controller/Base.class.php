<?php
	namespace User\Common\Controller;
	use Think\Controller;
	class Base extends Controller{
		protected $user;
		public function __construct(){
			parent::__construct();
			$this->user = $this->testSession();
			if(!$this->user){
				header("location: ".U('/user/login'));
			}
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
				if($c['classified'] == 'USER'){
					session('expire',time()+1200);
					return $user;
				}
			}
			return;
		}
	}
?>