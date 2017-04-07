<?php
class Action extends MY_Controller {
	public function __construct() {
		parent::__construct ();
	}
	public function pay_attention() {
		$ret = array (
				'code' => 200,
				'message' => '关注成功'
		);
		try{
			$uri_query = $this->input->post('token,friend_id', true, true);
			$this->load->model('home/dao/friend/Friend_model');
			$this->Friend_model->pay_attention($uri_query);
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
	public function list_attention() {
		$ret = array('code' => 200, 'message' => '', 'data' => array());
		$this->load->model('home/select/friend/Friend_imodel');
		try {
			$uri_query = $this->input->post('keyword,token', true,true);
			$page = $this->input->post('page', true) ? $this->input->post('page', true) : 1;
			if (! $this->input->post('page')) {
				$page = $this->input->get('page') ? $this->input->get('page') : 1;
			}
			$std = array();
			$std['per_page'] = 15;
			$std['total'] = $this->Friend_imodel->list_attention('count', $uri_query);
			$ret['data']['page'] = $std;
			$limit = ($page - 1) * $std['per_page'] . ',' . $std['per_page'];
			$ret['data']['list'] = $this->Friend_imodel->list_attention($limit,$uri_query);
		} catch(Err $e) {
			$ret['code'] = 300;
			$ret['message'] = $e->message();
		}
		echo json_encode($ret);
	}
	public function del_attention() {
		$ret = array (
				'code' => 200,
				'message' => '取消关注成功',
		);
		try{
			$uri_query = $this->input->post('token,friend_id', true, true);
			$this->load->model('home/dao/friend/Friend_model');
			$this->Friend_model->del_attention($uri_query);
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
}