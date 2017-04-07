<?php
class Action extends MY_Controller {
	public function __construct() {
		parent::__construct ();
	}
	public function publish_message() {
		$ret = array (
				'code' => 200,
				'message' => '发布成功'
		);
		try{
			$uri_query = $this->input->post('token,receive_id,message', true, true);
			$this->load->model('home/dao/yishu/Yishu_message_model');
			$this->Yishu_message_model->publish_message($uri_query);
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
	public function get_all_message() {
		$ret = array (
				'code' => 200,
				'message' => '',
				'data'=>array()
		);
		try{
			$uri_query = $this->input->post('token,time', true, true);
			$this->load->model('home/select/yishu/Yishu_message_imodel');
			$ret['data'] = $this->Yishu_message_imodel->get_all_message($uri_query);
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
	public function del_message() {
		$ret = array (
				'code' => 200,
				'message' => '删除成功',
		);
		try{
			$uri_query = $this->input->post('token,id', true, true);
			$this->load->model('home/dao/yishu/Yishu_message_model');
			$this->Yishu_message_model->del_message($uri_query);
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
}