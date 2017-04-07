<?php
class Yijie_votes_imodel extends MY_Model{
	var $table='yijie_votes';
	function  __construct()
	{
		parent::__construct();
		$this->lang->load('model/yijie_votes');
	}
	public function get_votes_info_by_essayid($essay_id){
		$ret = array();
		$this->db->select('user.id,user.pic');
		$this->db->where('user.id=yijie_votes.votes_id');
		$this->db->where('yijie_votes.state','valid');
		$this->db->where('yijie_votes.essay_id',$essay_id);
		$this->db->from('user');
		if($info = $this->fetch_result_array()){
			foreach ($info as $key=>&$val){
				$val['pic'] = $this->set_pic_dir($val['pic'],true,true);
			}
			$ret = $info;
		}
		return $ret;
	}
	
}