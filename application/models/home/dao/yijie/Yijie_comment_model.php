<?php
class Yijie_comment_model extends MY_Model{
	var $table='yijie_comment';
	function  __construct()
	{
		parent::__construct();
		$this->lang->load('model/yijie_comment');
	}
	public function publish_comment($config){
		$default = array('token'=>'', 'essay_id'=>'','message' => '','type'=>'','receive_id'=>'');
		$this->extend($default, $config);	
		//判断是否有帖子
		if(!$this->get_info($this->essay_id,'yijie_essay')){
			throw new Err('yijie_comment_essay_id_invalid');
		}
		//非一级评论，回复id为空
		if(!$this->receive_id&&$this->type==1){
			throw new Err('yijie_comment_receiv_id_null');
		}
		//0为评论帖子的一级评论，1为非一级评论,帖子的用户id就是一级评论的回复用户id
		$rece = $this->type==1 ? $this->receive_id : $this->info->uid;
		$data = array(
				'sent_id'=>$this->uid,
				'rece_id'=>$rece,
				'essay_id'=>$this->essay_id,
				'message'=>$this->message,
				'type'=>$this->type,
				'state'=>'valid',
				'ctime'=>time()
		);
		if(!$this->insert($data)){
			throw new Err('yijie_comment_insert_falid');
		};
		//更新评论数
		$this->load->model('home/dao/yijie/Yijie_essay_model');
		$this->Yijie_essay_model->update_comments($this->essay_id,'add',1);
	} 
	public function del_comment($config){
		$default = array('token'=>'', 'comment_id' => '');
		$this->extend($default, $config);
		//判断是否有记录，是否该用户发布，记录是否有效
		if(!$this->get_info($this->comment_id)){
			throw new Err('yijie_comment_id_invalid');
		}
		$data = array(
				'state'=>'invalid',
				'dtime'=>time()
		);
		if(!$this->update($data)){
			throw new Err('yijie_comment_del_falid');
		};
		//更新评论数
		$this->load->model('home/dao/yijie/Yijie_essay_model');
		$this->Yijie_essay_model->update_comments($this->info->essay_id,'sub',1);
	}
	/*
	 * 帖子删除时，删除所有评论
	*/
	public function del_all_comment($essay_id){
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
			throw new Err('yijie_comment_del_falid');
		};
	}
}