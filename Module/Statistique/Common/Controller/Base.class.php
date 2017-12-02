<?php
	namespace Statistique\Common\Controller;
	use Think\Controller;
	class Base extends Controller{
		protected $user;
		public function __construct(){
			parent::__construct();
			$this->user = $this->testSession();
			if(!$this->user){
				header("location: ".U('/statistique/login'));
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
				if($c['classified'] == 'STATISTIQUE'){
					session('expire',time()+1200);
					return $user;
				}
			}
			return;
		}
	}
?>