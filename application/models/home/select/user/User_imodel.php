<?php
class User_imodel extends MY_Model{
	var $table='user';
	function  __construct()
	{
		parent::__construct();
		$this->lang->load('model/user');
	}
	public function verify_phone($phone){
		if(!$phone){
			throw new Err('user_phone_invalid');	
		}
		$this->db->select('id');
		$this->db->where('phone', $phone);
		$info = $this->fetch_row();
		if ($info) {
			throw new Err('user_phone_exist');
		}
	}
	public function login_user($config)
	{
		//$this->log('登录测试：'.json_encode($config));
		$default = array('phone' => '', 'password' => '');
		$this->extend($default, $config);
		if (! $this->phone) {
			throw new Err('user_phone_invalid');
		}
		if (! $this->password) {
			throw new Err('user_password_invalid');
		}
		$this->db->select('id,phone,password,salt,pic,nickname,information_unread');
		$this->db->where('phone', $this->phone);
		$info = $this->fetch_row();
		if (! $info) {
			throw new Err('user_account_invalid');
		}
		$this->password = $this->md5_password($info->salt);
		if ($info->password != $this->password ) {
			throw new Err('user_account_invalid');
		}
		$this->load->model('home/dao/account/Account_login_model');
		$this->Account_login_model->save_login($info->id);
		return $this->translate($info,'pic,nickname');
	}
	protected function md5_password($salt){
		return md5($salt.$this->password);
	}
	public function get_own_info($config){
		$default = array('token'=>'',);
		$this->extend($default, $config);
		$this->db->select('phone,nickname,pic as pic_str,pic_cover as pic_cover_str,email,sex,birthday,address');
		$this->db->where('id', $this->uid);
		$ret = $this->fetch_row();
		return $this->translate($ret,'phone,nickname,pic_str,pic_cover_str,email,sex,birthday,address');
	}
	public function get_user_homepage($config){
		$default = array('token'=>'','id'=>'');
		$this->extend($default, $config);
		$this->db->select('nickname,pic,pic_cover,email,sex,birthday,address');
		$this->db->where('id', $this->id);
		$ret = $this->fetch_row();
		$ret = $this->translate($ret,'nickname,pic,pic_cover,email,sex,birthday,address');
		//组装数据
		//是否自己
		$ret->isUser = $this->uid==$this->id ? true :false;
		//非自己的主页，加上是否已关注字段
		if(!$ret->isUser){
			$this->load->model('home/select/friend/Friend_imodel');
			$ret->isAttention = $this->Friend_imodel->is_attention($this->uid,$this->id);
		}
		$ret->name = $ret->nickname;
		$ret->gender = $ret->sex;
		$ret->profileTopPic = $ret->pic_cover;
		$ret->avadarAdress = $ret->pic;
		unset($ret->pic_cover);
		unset($ret->sex);
		unset($ret->nickname);
		unset($ret->pic);
		//相册-》衣界自己发的帖子中的图片
		$this->load->model('home/select/yijie/Yijie_essay_imodel');
		$ret->pictureList = $this->Yijie_essay_imodel->all_own_essay_picture($this->id);
		//$this->debug($ret);
		return $ret;
	}
	public function find_user($limit,$config = array()){
		$default = array('keyword' => '', 'token' => '');
		$this->extend($default, $config);
		if(!$this->keyword){
			throw new Err('user_keyword_invalid');
		}
		if ($limit == 'count') {
			return $this->list_member('count');
		}
		$ret = $this->list_member($limit);
		return $ret;
	}
	public function list_member($limit)
	{
		
		
		$this->db->where('(nickname like "%' . $this->keyword . '%" or phone like "%' . $this->keyword . '%")');
		$this->db->where('state', 'valid');
		if ($limit == 'count') {
			return $this->fetch_count();
		}
		$this->db->select('id,nickname,pic,be_focus');
		$this->db->order_by('ctime desc');
		$this->db->limit($limit);
		$ret = array();
		if($info = $this->db->get('user')->result()){
			foreach ($info as $key=>$row){
				$ret[] = $this->translate($row,'nickname,pic,isAttention');
			}
		}
		return $ret;
	}
	protected function translate($row, $config = null, $extend = null)
	{
		if ($config) {
			if ($this->translate_required('nickname', $config)) {
				$row->nickname = $row->nickname ? $row->nickname : '-';
			}
			if ($this->translate_required('pic', $config)) {
				$row->pic = $row->pic ? $this->set_pic_dir($row->pic,true,true): '-';
			}
			if ($this->translate_required('pic_cover', $config)) {
				$row->pic_cover = $row->pic_cover ? $this->set_pic_dir($row->pic_cover,true,true): '-';
			}
			if ($this->translate_required('pic_str', $config)) {
				$row->pic_str = $row->pic_str ? $this->set_pic_dir($row->pic_str,false,true): '-';
			}
			if ($this->translate_required('pic_cover_str', $config)) {
				$row->pic_cover_str = $row->pic_cover_str ? $this->set_pic_dir($row->pic_cover_str,false,true): '-';
			}
			if ($this->translate_required('email', $config)) {
				$row->email = $row->email ? $row->email : '-';
			}
			if ($this->translate_required('address', $config)) {
				$row->address = $row->address ? $row->address : '-';
			}
			if ($this->translate_required('sex', $config)) {
				if($row->sex==0)$row->sex = '不详';
				if($row->sex==1)$row->sex = '男';
				if($row->sex==2)$row->sex = '女';
			}
			if ($this->translate_required('birthday', $config)) {
				$row->birthday = $row->birthday ? date('Y-m-d',$row->birthday) : '-';
			}
			if ($this->translate_required('isAttention', $config)) {
				//所有好友的id数组
				$this->load->model('home/select/friend/Friend_imodel');
				$friends = $this->Friend_imodel->list_member_id($this->uid);
				//是否被关注
				$row->isAttention = in_array($row->id, $friends)? true : false;
			}
// 			if ($this->translate_required('type', $config)) {
// 				$row->type_str = $this->lang->line($row->type, 'member_type');
// 			}
		}
		return $row;
	}
	
}