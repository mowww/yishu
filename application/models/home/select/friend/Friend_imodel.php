<?php
class Friend_imodel extends MY_Model{
	var $table='friend';
	function  __construct()
	{
		parent::__construct();
		$this->lang->load('model/friend');
	}
	public function list_attention($limit,$config = array()){
		$default = array('keyword' => '', 'token' => '');
		$this->extend($default, $config);
		if ($limit == 'count') {
			return $this->list_member('count');
		}
		$ret = $this->list_member($limit);
		return $ret;
	} 
	public function list_member($limit)
	{
		if ($this->keyword) {
			$this->db->where('(ed_user.nickname like "%' . $this->keyword . '%" or ed_user.phone like "%' . $this->keyword . '%")');
		}
		$this->db->where('friend.state', 'valid');
		$this->db->where('friend.uid', $this->uid);
		$this->db->join('user','friend.friend = user.id');
		if ($limit == 'count') {
			return $this->fetch_count();
		}
		$this->db->select('user.id,user.nickname,user.pic,user.be_focus');
		$this->db->where('user.state ','valid');
 		$this->db->order_by('friend.ctime desc');
 		$this->db->limit($limit);
		$ret = array(); 
		if($info = $this->db->get('friend')->result()){
			foreach ($info as $key=>$row){
				$ret[] = $this->translate($row,'nickname,pic,isAttention');
			}
		}
		return $ret;
	}
	//是否关注
	public function is_attention($uid,$friend){
		$this->db->where('uid',$uid);
		$this->db->where('friend',$friend);
		$this->db->where('state','valid');
		return $this->fetch_row()?true:false;
	}
	//好友列表id
	public function list_member_id($uid)
	{
		$this->db->select('friend');
		$this->db->where('state', 'valid');
		$this->db->where('uid', $uid);
		$this->db->order_by('friend.ctime desc');
		$ret = array();
		if($info = $this->fetch_result_array()){	
			$ids= array_column($info,'friend');
		}
// 		$ids   =>  array(1,2,3)
		return $ids;
	}
	protected function translate($row, $config = null, $extend = null)
	{
		if ($config) {
			if ($this->translate_required('pic', $config)) {
				$row->pic = $this->set_pic_dir($row->pic,true,true);
			}
			if ($this->translate_required('nickname', $config)) {
				$row->nickname = $row->nickname ? $row->nickname : '-';
				
			}
			if ($this->translate_required('isAttention', $config)) {
				$row->isAttention = true;
			}
		}
		return $row;
	}
}