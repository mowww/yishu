<?php
class Action extends MY_Controller {
	public function __construct() {
		parent::__construct ();
	}
	public function publish_essay() {
		//http://localhost/yishu/index.php/welcome/img  网页本地测试url
		$ret = array (
				'code' => 200,
				'message' => '发布成功'
		);
		try{
			$uri_query = $this->input->post('token,cover,colour,tag,pic', true, true);
			$this->load->model('home/dao/yishu/Yishu_essay_model');
			$this->Yishu_essay_model->publish_essay($uri_query);
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
	public function get_essay() {
		$ret = array (
				'code' => 200,
				'message' => '',
				'data'=>array()
		);
		try{
			$uri_query = $this->input->post('token,day_array', true, true);
			$this->load->model('home/select/yishu/Yishu_essay_imodel');
			$ret['data'] = $this->Yishu_essay_imodel->get_essay($uri_query);
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
	public function update_essay_cover() {
		$ret = array (
				'code' => 200,
				'message' => '设置封面成功',
				'data' => array()
		);
		try{
			$uri_query = $this->input->post('token,id', true, true);
			$this->load->model('home/dao/yishu/Yishu_essay_model');
			$ret['data']['old_cover_id'] = $this->Yishu_essay_model->update_essay_cover($uri_query);
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
			$this->load->model('home/dao/yishu/Yishu_essay_model');
			$this->Yishu_essay_model->del_essay($uri_query);
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
}