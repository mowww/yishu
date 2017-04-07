<?php
class Yishu_message_model extends MY_Model {
	var $table = 'yishu_message';
	function __construct() {
		parent::__construct ();
		$this->lang->load ( 'model/yishu_message' );
	}
	public function publish_message($config) {
		$default = array (
				'token' => '',
				'receive_id' => '',
				'message' => '' 
		);
		$this->extend ( $default, $config );
		if ( !$this->receive_id) {
			throw new Err ( 'yishu_message_receive_id_null' );
		}
		if (!$this->message) {
			throw new Err ( 'yishu_message_message_null' );
		}
		if (! $this->get_user_info ( $this->receive_id )) {
			throw new Err ( 'yishu_message_receive_id_invalid' );
		}
		$data = array (
				'send_uid' => $this->uid,
				'rece_uid' => $this->receive_id,
				'message' => $this->message,
				'state' => 'unread',
				'ctime' => time () 
		);
		if (! $this->insert ( $data )) {
			throw new Err ( 'yishu_message_insert_falid' );
		}
		//更新用户未读留言数，加1
		$this->load->model('home/dao/user/User_model');
		$this->User_model->update_information_unread($this->receive_id,'add',1);
	}
	public function del_message($config) {
		$default = array (
				'token' => '',
				'id' => '' 
		);
		$this->extend ( $default, $config );
		// 判断是否有记录，是否该用户发布，记录是否有效
		if (!$this->get_info ( $this->id ) || $this->info->send_uid != $this->uid || $this->info->state == 'invalid') {
			throw new Err ( 'yishu_message_id_invalid' );
		}
		$data = array (
				'state' => 'invalid',
				'dtime' => time () 
		);
		if (! $this->update ( $data )) {
			throw new Err ( 'yishu_message_del_falid' );
		}
		//如果未读，更新用户未读留言数量,减1
		$this->load->model('home/dao/user/User_model');
		$this->User_model->update_information_unread($this->info->rece_uid,'sub',1);
	}
}