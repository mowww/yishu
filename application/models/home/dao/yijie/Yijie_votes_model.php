<?php
class Yijie_votes_model extends MY_Model{
	var $table='yijie_votes';
	function  __construct()
	{
		parent::__construct();
		$this->lang->load('model/yijie_votes');
	}
	protected function get_like_by_essay_id(){
		$this->db->where('essay_id',$this->essay_id);
		$this->db->where('votes_id',$this->uid);
		if($this->info = $this->fetch_row()){
			$this->set_id($this->info->id);
		}
		return $this->info;
	}
	
	public function click_like($config){
		$default = array('token'=>'', 'essay_id' => '');
		$this->extend($default, $config);	
		$this->load->model('home/dao/yijie/Yijie_essay_model');
		//判断是否有帖子
		if(!$this->get_info($this->essay_id,'yijie_essay')){
			throw new Err('yijie_votes_essay_id_invalid');
		}
		//判断是否有记录(即是否已赞)
		if($this->get_like_by_essay_id()){
			if($this->info->state=='valid'){
				//已赞过，则取消赞
				$data = array(
						'state'=>'invalid',
						'dtime'=>time()
				);
				if(!$this->update($data)){
					//$this->debug($this->db->last_query());
					
					throw new Err('yijie_votes_cancle_falid');
				};
				$this->Yijie_essay_model->update_votes($this->essay_id,'sub',1);
				return '取消赞成功';
			}
			if($this->info->state=='invalid'){
				//已取消赞，数据库还存在记录，更新数据。点赞
				$data = array(
						'state'=>'valid',
						'ctime'=>time(),
						'dtime'=>0,
				);
				if(!$this->update($data)){
					throw new Err('yijie_votes_do_falid');
				};
				$this->Yijie_essay_model->update_votes($this->essay_id,'add',1);
				return '点赞成功';
			}
			
		}else{
			$data = array(
					'essay_id'=>$this->essay_id,
					'votes_id'=>$this->uid,
					'ctime'=>time(),
					'state'=>'valid',
			);
			if(!$this->insert($data)){
				throw new Err('yijie_votes_do_falid');
			};
			$this->Yijie_essay_model->update_votes($this->essay_id,'add',1); 
			return '点赞成功';
		}
		
	} 
	/*
	 * 帖子删除时，删除所有赞
	 */
	public function del_all_like($essay_id){
		$data = array(
				'state'=>'invalid',
				'dtime'=>time()
		);
		$where = array(
					array(
						'essay_id'=>$essay_id					
					)
				);
		if(!$this->update($data,$where)){	
			throw new Err('yijie_votes_del_falid');
		};
	}
}