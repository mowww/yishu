<?php
class User_model extends MY_Model{
	var $table='user';
	function  __construct()
	{
		parent::__construct();
		$this->lang->load('model/user');
	}
	public function register_user($config){
		$default = array('phone' => '', 'password' => '');
		$a = $this->extend($default, $config);
		if(!$this->phone||!preg_match('/^\d{11}$/',$this->phone)){
			throw new Err('user_phone_invalid');	
		}
		if(!$this->password||!preg_match("/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~\?]{8,16}$/",$this->password)){
			throw new Err('user_password_invalid');	
		}
		$this->load->model('home/select/user/User_imodel');
		$this->User_imodel->verify_phone($this->phone);
		$this->load->helper('string');
		$salt = random_string('alnum',4);
		$data = array(
				'nickname'=>$this->phone,
				'phone'=>$this->phone,
				'password'=>$this->md5_password($salt,$this->password),
				'salt'=>$salt,
				'reg_time'=>time(),
				'state'=>'valid',
				'ctime'=>time(),
				'daytime'=>date('Y-m-d',time()),
			);
		if($a = !$this->insert($data)){
			//$this->debug($a);
			throw new Err('user_register_insert_invalid');
		}
	}
	public function login_user($config)
	{
		$default = array('phone' => '', 'password' => '');
		$this->extend($default, $config);
		if (! $this->phone) {
			throw new Err('user_phone_invalid');
		}
		if (! $this->password) {
			throw new Err('user_password_invalid');
		}
		$this->db->select('id,phone,password,salt,pic,nickname');
		$this->db->where('phone', $this->phone);
		$info = $this->fetch_row();
		if (! $info) {
			throw new Err('user_account_invalid');
		}
		$this->password = $this->md5_password($info->salt,$this->password);
		if ($info->password != $this->password ) {
			throw new Err('user_account_invalid');
		}
		$this->load->model('home/dao/account/account_login_model');
		$this->account_login_model->save_login($info->id);
		return $info;
	}
	protected function md5_password($salt,$pwd){
		return md5($salt.$pwd);
	}
	public function get_user(){
		$this->db->select('nickname');
		return $this->fetch_row();
	}
	public function upadte_passwd($config){
		$default = array('token' => '', 'old_password' => '','new_password'=>'');
		$this->extend($default, $config);
		$this->get_info($this->uid);
		if(!$this->info||!$this->old_password||!$this->new_password){
			throw new Err('user_password_invalid');
		}
		$pwd = $this->md5_password($this->info->salt,$this->old_password);
		if($pwd != $this->info->password){
			throw new Err('user_old_password_invalid');
		}
		$data = array(
				'mtime'=>time(),
				'password'=>$this->md5_password($this->info->salt, $this->new_password)
		);
		if(!$this->update($data)){
			throw new Err('user_update_password_failed');
		}
	}
	public function update_own_info($config){
		$default = array('token' => '', 'nickname'=>'','sex'=>'','birthday'=>'','address'=>'','email'=>'');
		$this->extend($default, $config);
		$data = array();
		$this->load->model('Img_model');
		if($this->nickname){
			$data['nickname'] =  $this->nickname;
		}
		if(isset($_FILES['pic'])&&$_FILES['pic']['size']){
			$data['pic'] = $this->Img_model->upload_img($this->uid,'head_pic/');
		}
		if(isset($_FILES['pic_cover'])&&$_FILES['pic_cover']['size']){
			$data['pic_cover'] = $this->Img_model->upload_img($this->uid,'head_pic/');
		}
		if($this->email){
			$data['email'] =  $this->email;
		}
		if($this->sex){
			$data['sex'] =  $this->sex;
		}
		if($this->birthday){
			$data['birthday'] =  $this->birthday;
		}
		if($this->address){
			$data['address'] =  $this->address;
		}
		$data['mtime'] = time();
		$this->set_id($this->uid);
		if(!$this->update($data)){
			throw new Err('user_update_info_failed');
		}
	}
	public function update_information_unread($uid,$m,$num = 0){
		$this->get_info($uid);
		if($m=='add'){
			$data = array(
					'mtime'=>time(),
					'information_unread'=>$this->info->information_unread +$num
			);
		}
		if($m=='sub'){
			$data = array(
					'mtime'=>time(),
					'information_unread'=>$this->info->information_unread -$num
			);
		}
		if($m=='allsub'){
			$data = array(
					'mtime'=>time(),
					'information_unread'=>0
			);
		}
		$this->id = $uid;
		$this->update($data);	
	}
	public function update_be_focus($uid,$m){
		$this->get_info($uid);
		if($m=='add'){
			$data = array(
					'mtime'=>time(),
					'be_focus'=>$this->info->be_focus +1
			);
		}
		if($m=='sub'){
			$data = array(
					'mtime'=>time(),
					'be_focus'=>$this->info->be_focus -1
			);
		}
		$this->id = $uid;
		$this->update($data);
	}
	public function forget_passwd($config){
		$default = array('token' => '','new_password'=>'');
		$this->extend($default, $config);
		$this->get_info($this->uid);
		if(!$this->info||!$this->new_password){
			throw new Err('user_password_invalid');
		}
		$data = array(
				'mtime'=>time(),
				'password'=>$this->md5_password($this->info->salt, $this->new_password)
		);
		if(!$this->update($data)){
			throw new Err('user_update_password_failed');
		}
	}
}