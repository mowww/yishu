<?php
class Friend_model extends MY_Model{
	var $table='friend';
	function  __construct()
	{
		parent::__construct();
		$this->lang->load('model/friend');
	}
	public function pay_attention($config){
		$default = array('token'=>'', 'friend_id' => '');
		$this->extend($default, $config);
		if(!$this->get_user_info($this->friend_id)){
			throw new Err('friend_friend_id_invalid');
		}
		//检查是否关注过
		$this->db->where('uid', $this->uid);
		$this->db->where('friend', $this->friend_id);
		$query = $this->db->get($this->table);
		$info = $query->row();
		if($info){
			//有关注记录
			if($info->state=='valid')
				throw new Err('friend_pay_already');
			if($info->state=='invalid'){
				$this->set_id($info->id);
				$data = array(
						'state'=>'valid',
						'ctime'=>time(),
						'dtime'=>0
				);
				if(!$this->update($data)){
					throw new Err('friend_pay_insert_falid');
				};
			}
		}else{
			$data = array(
					'uid'=>$this->uid,
					'friend'=>$this->friend_id,
					'state'=>'valid',
					'ctime'=>time()
			);
			if(!$this->insert($data)){
				throw new Err('friend_pay_insert_falid');
			};
		}
		//更新用户的被关注数
		$this->load->model('home/dao/user/User_model');
		$this->User_model->update_be_focus($this->friend_id,'add');
	} 
	public function del_attention($config){
		$default = array('token'=>'', 'friend_id' => '');
		$this->extend($default, $config);
		$data = array(
				'state'=>'invalid',
				'dtime'=>time()
		);
		$where = array(
				array(
						'friend'=>$this->friend_id,
						'uid'=>$this->uid,
						'state'=>'valid'
				)
		);
		if(!$this->update($data,$where)){
			throw new Err('friend_del_falid');
		};
		//更新用户的被关注数
		$this->load->model('home/dao/user/User_model');
		$this->User_model->update_be_focus($this->friend_id,'sub');
	}
	
}