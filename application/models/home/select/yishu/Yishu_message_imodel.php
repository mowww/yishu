<?php
class Yishu_message_imodel extends MY_Model{
	var $table='yishu_message';
	function  __construct()
	{
		parent::__construct();
		$this->lang->load('model/yishu_message');
	}
	public function get_all_message($config){
		$default = array('token'=>'', 'time' => '');
		$this->extend($default, $config);
		if(!$this->time){
			$this->time = 0;
		}
		$this->db->select('yishu_message.id,yishu_message.message,yishu_message.ctime,yishu_message.state,yishu_message.send_uid,yishu_message.rece_uid');
		$this->db->select('user.nickname as send_nickname');
		$this->db->from('user');
		$this->db->where('yishu_message.ctime >=',$this->time);
		$this->db->where('yishu_message.rece_uid',$this->uid);
		$this->db->where('yishu_message.state !=','invalid');
		$this->db->where('user.id = yishu_message.send_uid');
		$ret = $this->fetch_result();
		//置未读数为0
		$this->load->model('home/dao/user/User_model');
		$this->User_model->update_information_unread($this->uid,'allsub');
		
    	return $ret;
	} 
	protected function translate($row, $config = null, $extend = null)
	{
		if ($config) {
			if ($this->translate_required('cover', $config)) {
				$row->cover = $row->cover ? true : false;
			}
			if ($this->translate_required('colour', $config)) {
				$row->colour = $row->colour ? $row->colour : '-';
			}
			if ($this->translate_required('coat', $config)) {
				$row->coat = $row->coat ? $row->coat : '-';
			}
			if ($this->translate_required('pant', $config)) {
				$row->pant = $row->pant ? $row->pant : '-';
			}
			if ($this->translate_required('shoes', $config)) {
				$row->shoes = $row->shoes ? $row->shoes : '-';
			}
			if ($this->translate_required('ornament', $config)) {
				$row->ornament = $row->ornament ? $row->ornament : '-';
			}
		}
		return $row;
	}
}