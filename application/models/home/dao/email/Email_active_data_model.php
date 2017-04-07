<?php
class Email_active_data_model extends MY_Model{
	var $table='email_active_data';
	function __construct()
	{
		parent::__construct();
	}
	public function set_email_active_code(){
 		$this->load->helper('string');
 		$code = random_string('alnum',16);
 		$this->id = 5;
 		$data = array(
 			'uid'=>$this->id,
 			'active_code'=>$code,
			'active_type_code'=>'valid_email',
			'ctime'=>time()
 			);
 		if(!$this->db->insert($this->table,$data)){
 			throw new Err('验证码生产失败');
 		}
 		return $code;
	}
}