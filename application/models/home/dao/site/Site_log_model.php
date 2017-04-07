<?php
class Site_log_model extends MY_Model
{
	var $table = 'site_log';
	var $primary = 'id';

	function __construct()
	{
		parent::__construct();
	}

	public function insert_do($content = '', $controller = '', $action = '')
	{
		$data = array();
		$data['content'] = $content;
		$data['controller'] = $controller ;
		$data['action'] = $action;
	//	$data['user_id'] = $this->acl->member_id() ? $this->acl->member_id() : $this->acl->enterprise_staff_id();
		$data['from_ip'] = $this->input->ip_address();
		$data['created'] = time();
		$data['os'] = $this->input->get_os();
		$data['browse'] = $this->input->get_browse();
		$data['user_agent'] = $this->input->user_agent();
		$this->db->insert('site_log', $data);
		return $this->db->insert_id();
	}
}