<?php
class Email_active_data_imodel extends MY_Model{
	var $table='email_active_data';
	function __construct()
	{
		parent::__construct();
	}
	public function get_email_active_code($code){
		if(!$code){
			throw new Err('验证码为空');
		}
		$this->db->select('id,active_code,ctime');
		$this->db->where('active_code',$code);
		$this->db->where('active_type_code','valid_email');
		$row = $this->fetch_row();
		if(!$row){
			throw new Err('验证码无效');
		}
		return $row;
	}
}