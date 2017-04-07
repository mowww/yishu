<?php
class Account_login_model extends MY_Model{
	var $table='account_login';
	function  __construct()
	{
		parent::__construct();
		//$this->lang->load('model/user');
	}
	public function save_login($uid)
	{
		$this->db->where('uid', $uid);
		$row = $this->db->get('account_login')->row();
		if (! empty($row)) {
			$sql = 'update ' . $this->db->dbprefix('account_login') . ' set login_time=' . time() . ' where uid=' . $uid;
		} else {
			$sql = 'insert into ' . $this->db->dbprefix('account_login') . '(`uid`,`login_time`,`ctime`) values (' . $uid . ',' . time() . ',' . time() . ')';
		}
		$this->db->query($sql);
	}
	public function logout($config)
	{
		$default = array('token' => '');
		$a = $this->extend($default, $config);
		$this->db->where('uid', $this->uid);
		$row = $this->db->get('account_login')->row();
		$sql = 'update ' . $this->db->dbprefix('account_login') . ' set login_time=' . 0 . ' where uid=' . $this->uid;
		$this->db->query($sql);
	}
	
}