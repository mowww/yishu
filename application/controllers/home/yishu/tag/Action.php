<?php
class Action extends MY_Controller {
	public function __construct() {
		parent::__construct ();
	}
	public function get_tags() {
		$ret = array (
				'code' => 200,
				'message' => '',
				'data'=>array()
		);
		try{
			$uri_query = $this->input->post('token', true, true);
			$this->load->model('home/select/yishu/Yishu_tag_imodel');
			$ret['data'] = $this->Yishu_tag_imodel->get_tags($uri_query);
		}catch(Err $e){
			$ret['code']=300;
			$ret['message']=$e->message();
		}
		echo json_encode ( $ret );
	}
}