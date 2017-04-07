<?php
class Yijie_essay_model extends MY_Model{
	var $table='yijie_essay';
	function  __construct()
	{
		parent::__construct();
		$this->lang->load('model/yijie_essay');
	}
	public function update_votes($id,$m,$num = 0){
		$this->get_info($id);
		if($m=='add'){
			$data = array(
					'mtime'=>time(),
					'votes'=>$this->info->votes +$num
			);
		}
		if($m=='sub'){
			$data = array(
					'mtime'=>time(),
					'votes'=>$this->info->votes -$num
			);
		}
		if($m=='allsub'){
			$data = array(
					'mtime'=>time(),
					'votes'=>0
			);
		}
		$this->id = $id;
		$this->update($data);
	}
	public function update_comments($id,$m,$num = 0){
		$this->get_info($id);
		if($m=='add'){
			$data = array(
					'mtime'=>time(),
					'comments'=>$this->info->comments +$num
			);
		}
		if($m=='sub'){
			$data = array(
					'mtime'=>time(),
					'comments'=>$this->info->comments -$num
			);
		}
		if($m=='allsub'){
			$data = array(
					'mtime'=>time(),
					'comments'=>0
			);
		}
		$this->id = $id;
		$this->update($data);
	}
	public function publish_essay($config){
		
		$default = array('token'=>'', 'message' => '','pic'=>'');
		$this->extend($default, $config);
		if(!isset($_FILES['pic'])||!$_FILES['pic']['size']){
			throw new Err('yijie_essay_pic_null');
		}
		$this->load->model('Img_model');
		$pic = $this->Img_model->upload_img($this->uid,'yijie/');
		$data = array(
				'uid'=>$this->uid,
				'pic'=>$pic,
				'message'=>$this->message,
				'comments'=>0,
				'votes'=>0,
				'state'=>'valid',
				'ctime'=>time()
		);
		if(!$this->insert($data)){
			throw new Err('yijie_essay_insert_falid');
		}
	} 
	public function del_essay($config){
		$default = array('token'=>'', 'id' => '');
		$this->extend($default, $config);
		//判断是否有记录，是否该用户发布，记录是否有效
		if(!$this->get_info($this->id)||$this->info->uid!=$this->uid||$this->info->state!='valid'){
			throw new Err('yijie_essay_id_invalid');
		}
		$data = array(
				'state'=>'invalid',
				'dtime'=>time()
		);
		if(!$this->update($data)){
			throw new Err('yijie_essay_del_falid');
		};
		//删除赞
		$this->load->model('home/dao/yijie/Yijie_votes_model');
		$this->Yijie_votes_model->del_all_like($this->id);
		//删除评论
		$this->load->model('home/dao/yijie/Yijie_comment_model');
		$this->Yijie_comment_model->del_all_comment($this->id);
	}
}