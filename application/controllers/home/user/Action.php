<?php
class Action extends MY_Controller{
	public function __construct()
    {
        parent::__construct();
    }
    public function register_user(){
    	$ret = array('code'=>200,'message'=>'注册成功');
    	try{
    		$uri_query = $this->input->post('phone,password', true, true);
    		$this->load->model('home/dao/user/User_model');
    		$this->User_model->register_user($uri_query);
    	}catch(Err $e){
    		$ret['code']=300;
    		$ret['message']=$e->message();
    	}
    	echo json_encode($ret);
    }
    public function verify_phone(){
    	$ret = array('code'=>200,'message'=>'success');
    	try{
    		$uri_query = $this->input->post('phone');;
    		$this->load->model('home/select/user/User_imodel');
    		$this->User_imodel->verify_phone($uri_query);
    	}catch(Err $e){
    		$ret['code']=300;
    		$ret['message']=$e->message();
    	}
    	echo json_encode($ret);
    }
    public function login() 
	{
		$this->load->model('home/select/user/User_imodel');
		$this->load->library('encrypt');
		$ret = array('code' => 300, 'message' => '','data'=>array());
		try {
			$uri_query = $this->input->post('phone,password', true, true);
			//$this->debug($_POST);
			if($this->input->get('debug')){
				$uri_query['phone']=13750050680;
				$uri_query['password']=1234;
			}
			$info = $this->User_imodel->login_user($uri_query);
			$ret['data']['token'] = $this->encrypt->encode(json_encode(array( 
					'uid' => $info->id,
					'login_time'=>time())));
			$ret['code'] = 200;
			$ret['data']['id'] = $info->id;
			$ret['data']['pic'] = $info->pic;
			$ret['data']['nickname'] = $info->nickname;
			$ret['data']['information_unread'] = $info->information_unread;
			$ret['message'] = '登录成功';
		} catch(Err $e) {
			$ret['message'] = $e->message();
		}
		echo json_encode($ret);
	}
	public function logout()
	{
		$ret = array('code' => 200, 'message' => '退出成功',);
		try {
			$uri_query = $this->input->post('token', true, true);
			$this->load->model('home/dao/account/Account_login_model');
			$this->Account_login_model->logout($uri_query);
		} catch(Err $e) {
			$ret['code'] = 300;
			$ret['message'] = $e->message();
		}
		echo json_encode($ret);
	}
	public function update_passwd()
	{
		$this->load->model('home/dao/user/User_model');
		$ret = array('code' => 300, 'message' => '');
		try {
			$uri_query = $this->input->post('token,new_password,old_password', true, true);
			$info = $this->User_model->upadte_passwd($uri_query);
			$ret['code'] = 200;
			$ret['message'] = '修改成功';
		} catch(Err $e) {
			$ret['message'] = $e->message();
		}
		echo json_encode($ret);
	}
	public function forget_passwd()
	{
		$this->load->model('home/dao/user/User_model');
		$ret = array('code' => 300, 'message' => '');
		try {
			$uri_query = $this->input->post('token,new_password', true, true);
			$info = $this->User_model->forget_passwd($uri_query);
			$ret['code'] = 200;
			$ret['message'] = '修改成功';
		} catch(Err $e) {
			$ret['message'] = $e->message();
		}
		echo json_encode($ret);
	}
	public function get_own_info(){
		$this->load->model('home/select/user/User_imodel');
		$ret = array('code' => 200, 'message' => '','data'=>array());
		try {
			$uri_query = $this->input->post('token', true, true);
			$ret['data'] = $this->User_imodel->get_own_info($uri_query);
		} catch(Err $e) {
			$ret['message'] = $e->message();
			$ret['code'] = 300;
		}
		echo json_encode($ret);
	}
	public function update_own_info(){
		$this->load->model('home/dao/user/User_model');
		$ret = array('code' => 200, 'message' => '更改成功');
		try {
			$uri_query = $this->input->post('token,nickname,sex,birthday,address,email', true, true);
			$ret['data'] = $this->User_model->update_own_info($uri_query);
		} catch(Err $e) {
			$ret['message'] = $e->message();
			$ret['code'] = 300;
		}
		echo json_encode($ret);
	}
	public function get_user_homepage(){
		$this->load->model('home/select/user/User_imodel');
		$ret = new stdClass();
		$ret->code = 200;
		$ret->message = '';
		$ret->data = new stdClass();
		try {
			$uri_query = $this->input->post('token,id', true, true);
			$ret->data = $this->User_imodel->get_user_homepage($uri_query);
		} catch(Err $e) {
			$ret->message = $e->message();
			$ret->code = 300;
		}
		echo json_encode($ret);
	}
	//搜索好友
	public function find_user() {
		$ret = array('code' => 200, 'message' => '', 'data' => array());
		$this->load->model('home/select/user/User_imodel');
		try {
			$uri_query = $this->input->post('keyword,token', true,true);
			$page = $this->input->post('page', true) ? $this->input->post('page', true) : 1;
			if (! $this->input->post('page')) {
				$page = $this->input->get('page') ? $this->input->get('page') : 1;
			}
			$std = array();
			$std['per_page'] = 15;
			$std['total'] = $this->User_imodel->find_user('count', $uri_query);
			$ret['data']['page'] = $std;
			$limit = ($page - 1) * $std['per_page'] . ',' . $std['per_page'];
			$ret['data']['list'] = $this->User_imodel->find_user($limit,$uri_query);
		} catch(Err $e) {
			$ret['code'] = 300;
			$ret['message'] = $e->message();
		}
		echo json_encode($ret);
	}
}