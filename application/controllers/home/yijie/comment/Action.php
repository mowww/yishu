<?php
class Action extends MY_Controller {
	public function __construct() {
		parent::__construct ();
	}
	public function publish_comment() {
		$ret = array (
				'code' => 200,
				'message' => '添加评论成功'
		);
		try{
			$uri_query = $this->input->post('token,essay_id,message,type,receive_id', true, true);
			$this->load->model('home/dao/yijie/Yijie_comment_model');
			$this->Yijie_comment_model->publish_comment($uri_query);
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
	public function del_comment() {
		$ret = array (
				'code' => 200,
				'message' => '删除评论成功',
		);
		try{
			$uri_query = $this->input->post('token,comment_id', true, true);
			$this->load->model('home/dao/yijie/Yijie_comment_model');
				$this->Yijie_comment_model->del_comment($uri_query);
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
}