<?php
class Action extends MY_Controller {
	public function __construct() {
		parent::__construct ();
	}
	//赞和取消赞
	public function click_like() {
		$ret = array (
				'code' => 200,
				'message' => ''
		);
		try{
			$uri_query = $this->input->post('token,essay_id', true, true);
			$this->load->model('home/dao/yijie/Yijie_votes_model');
			$ret['message'] = $this->Yijie_votes_model->click_like($uri_query);
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
}