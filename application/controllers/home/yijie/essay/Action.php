<?php
class Action extends MY_Controller {
	public function __construct() {
		parent::__construct ();
	}
	public function list_attention_essay() {
		$ret = new stdClass();
		$ret->code =  200;
		$ret->message =  '';
		$ret->data =  new stdClass();
		$this->load->model('home/select/yijie/Yijie_essay_imodel');
		try {
			$uri_query = $this->input->post('token', true, true);
			$page = $this->input->post('page', true) ? $this->input->post('page', true) : 1;
			if (! $this->input->post('page')) {
				$page = $this->input->get('page') ? $this->input->get('page') : 1;
			}
			$std = array();
			$std['per_page'] = 15;
			$std['total'] = $this->Yijie_essay_imodel->list_attention_essay('count', $uri_query);
			$limit = ($page - 1) * $std['per_page'] . ',' . $std['per_page'];
			$ret->data= $this->Yijie_essay_imodel->list_attention_essay($limit,$uri_query);
			$ret->data->page= $std;
		} catch(Err $e) {
			$ret->code =  300;
			$ret->message = $e->message();
		}
		echo json_encode ( $ret );
	}
	public function list_square_essay() {
		$ret = new stdClass();
		$ret->code =  200;
		$ret->message =  '';
		$ret->data =  new stdClass();
		$this->load->model('home/select/yijie/Yijie_essay_imodel');
		try {
			$uri_query = $this->input->post('token', true, true);
			$page = $this->input->post('page', true) ? $this->input->post('page', true) : 1;
			if (! $this->input->post('page')) {
				$page = $this->input->get('page') ? $this->input->get('page') : 1;
			}
			$std = array();
			$std['per_page'] = 15;
			$std['total'] = $this->Yijie_essay_imodel->list_square_essay('count', $uri_query);
			$limit = ($page - 1) * $std['per_page'] . ',' . $std['per_page'];
			$ret->data= $this->Yijie_essay_imodel->list_square_essay($limit,$uri_query);
			$ret->data->page= $std;
		} catch(Err $e) {
			$ret->code =  300;
			$ret->message = $e->message();
		}
		echo json_encode ( $ret );
	}
	public function publish_essay() {
		$ret = array (
				'code' => 200,
				'message' => '发布成功'
		);
		try{
			$uri_query = $this->input->post('token,message,pic', true, true);
			$this->load->model('home/dao/yijie/Yijie_essay_model');
			$this->Yijie_essay_model->publish_essay($uri_query);
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
	public function del_essay() {
		$ret = array (
				'code' => 200,
				'message' => '删除成功',
		);
		try{
			$uri_query = $this->input->post('token,id', true, true);
			$this->load->model('home/dao/yijie/Yijie_essay_model');
			$this->Yijie_essay_model->del_essay($uri_query);
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
	public function get_own_essay() {
		$ret = array (
				'code' => 200,
				'message' => ''
		);
		$ret['data'] =  new stdClass(); 
		try{
			$this->load->model('home/select/yijie/Yijie_essay_imodel');
			$uri_query = $this->input->post('token', true, true);
			$page = $this->input->post('page', true) ? $this->input->post('page', true) : 1;
			if (! $this->input->post('page')) {
				$page = $this->input->get('page') ? $this->input->get('page') : 1;
			}
			$std = array();
			$std['per_page'] = 15;
			$std['total'] = $this->Yijie_essay_imodel->get_own_essay('count', $uri_query);
			$limit = ($page - 1) * $std['per_page'] . ',' . $std['per_page'];
			$ret['data']->pictureItemList= $this->Yijie_essay_imodel->get_own_essay($limit,$uri_query);
			$ret['data']->page = $std;
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
}