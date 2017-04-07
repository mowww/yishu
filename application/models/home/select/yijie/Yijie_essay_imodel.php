<?php
class Yijie_essay_imodel extends MY_Model{
	var $table='yijie_essay';
	function  __construct()
	{
		parent::__construct();
		$this->lang->load('model/yijie_essay');
	}
	//自己以及好友的id
	protected function get_ids(){
		$this->db->select('friend');
		$this->db->where('uid',$this->uid);
		$this->db->where('state','valid');
		
		$this->ids = array($this->uid);
		if($query = $this->fetch_result('friend')){
			foreach($query as $key=>$val){
				$this->ids[]=$val->friend;
			}
		}
		return $this->ids;
	}
	/*	$type
	 *  attention 关注的好友的推荐空间
	*   square    广场
	*/
	public function nowday_max_vote_essay($type="square"){
		//$this->debug($ids);
		$time = strtotime(date('Y-m-d'));
		$this->db->select('message,pic');
		$this->db->where('state','valid');
		$this->db->where('ctime >=',$time);
		$this->db->where('ctime <',$time+60*60*24);
		if($type=='attention'){
			$this->db->where_in('uid',$this->ids);
		}
		$this->db->order_by('votes desc');
		//当日没帖子,之前最多赞的贴子
		if(!$query= $this->fetch_row()){
			$this->db->select('message,pic');
			$this->db->where('state','valid');
			if($type=='attention'){
				$this->db->where_in('uid',$this->ids);
			}
			$this->db->order_by('votes desc,ctime desc');
			//没贴子，提供提示图片
			if(!$query = $this->fetch_row()){
				$query = new stdClass();
				$query->pic = $this->get_system_config('yijie', 'max_votes_essay_point')->value;
				$query->message = '';
			}
		}
		return $query;
	}
	public function list_essay($limit,$type='square'){
		$this->db->select('yijie_essay.message as desc,yijie_essay.pic as pictureAdd,yijie_essay.ctime,yijie_essay.id as essay_id');
		$this->db->select('user.id,user.nickname as name,user.pic as src');
		$this->db->where('yijie_essay.state','valid');
		$this->db->where('user.id = yijie_essay.uid');
		$this->db->from('user');
		//好友及自己的贴
		if($type=='attention'){
			$this->db->where_in('yijie_essay.uid',$this->ids);
		}
		$this->db->order_by('yijie_essay.ctime desc');
		if ($limit == 'count') {
			return $this->fetch_count();
		}
		$this->db->limit($limit);
		$info= $this->fetch_result();
		foreach ($info as $key=>$val){
			$this->load->model('home/select/yijie/Yijie_votes_imodel');
			$val->voteLite = $this->Yijie_votes_imodel->get_votes_info_by_essayid($val->essay_id);
			//是否点赞
// 			$this->db->where('state','valid');
// 			$this->db->where('essay_id',$val->essay_id);
// 			$this->db->where('votes_id',$this->uid);
// 			$val->isLike = $this->fetch_row('yijie_votes') ? true : false;
			$val->isLike = in_array($this->uid,array_column($val->voteLite,'id'))? true : false;
			//底部评论
			$val->comment = array();
			$this->db->select('yijie_comment.id as comment_id,yijie_comment.message as text,yijie_comment.sent_id as sentId,yijie_comment.rece_id as receiveId,yijie_comment.ctime,yijie_comment.type');
			$this->db->select('user.nickname as sent');
			$this->db->select('b.nickname as receive');
			$this->db->where('yijie_comment.essay_id',$val->essay_id);
			$this->db->where('yijie_comment.state','valid');
			$this->db->where('user.id = yijie_comment.sent_id');
			$this->db->where('b.id = yijie_comment.rece_id');
			$this->db->from('user');
			$this->db->from('user as b');
			$this->db->order_by('yijie_comment.ctime asc');
			$query = $this->fetch_result('yijie_comment');			
			foreach ($query as $k=>$v){
					if(!$v->type){
						unset($v->receiveId);
						unset($v->receive);
					}
					$v->ctime = date('Y-m-d H:i:s',$v->ctime);
					unset($v->type);
			}
			$val->comment= $query;	
			//unset($val->essay_id);	
			$val->ctime = date('Y-m-d H:i:s',$val->ctime);
			$val->pictureAdd = $this->set_pic_dir($val->pictureAdd,true,true);
			$val->src = $this->set_pic_dir($val->src,true,true);
		}
		//$this->debug($info);
		return $info;
	}
	/*
	 * 朋友圈
	 */
	public function list_attention_essay($limit,$config){
		$default = array('token'=>'', );
		$this->extend($default, $config);
		//$this->log('接口： '.json_encode($_PO ST));
		//$this->log('接口2： '.$_POST['token']);
		$ret = new stdClass();
		//帖子
		$this->get_ids();
		if ($limit == 'count') {
			return  $this->list_essay($limit,'attention');
		}
		$ret->userId = $this->uid;
		$ret->userPic = $this->set_pic_dir($this->info->pic,true,true);
		$ret->pictureItemList = $this->list_essay($limit,'attention');
		//当日最多赞的贴子及用户昵称，顶图；用户信息
		$ret->userName = $this->info->nickname ? $this->info->nickname : "-";
		$ret->sideBarPicture = $this->set_pic_dir($this->info->pic_cover,true,true);
		$info=$this->nowday_max_vote_essay('attention');
		$ret->topPictureDesc = $info->message;
		$ret->topPictureAddress = $this->set_pic_dir($info->pic,true,true);
		//$this->debug($ret);
		return $ret;
	}
	/*
	 * 广场
	*/
	public function list_square_essay($limit,$config){
		$default = array('token'=>'', );
		$this->extend($default, $config);
		$ret = new stdClass();
		//帖子
		if ($limit == 'count') {
			return  $this->list_essay($limit,'square');
		}
		$ret->userId = $this->uid;
		$ret->userPic = $this->set_pic_dir($this->info->pic,true,true);
		$ret->pictureItemList = $this->list_essay($limit,'square');
		//当日最多赞的贴子及用户昵称，顶图；用户信息
		$ret->userName = $this->info->nickname ? $this->info->nickname : "-";
		$ret->sideBarPicture = $this->set_pic_dir($this->info->pic_cover,true,true);
		$info=$this->nowday_max_vote_essay('square');
		$ret->topPictureDesc = $info->message;
		$ret->topPictureAddress = $this->set_pic_dir($info->pic,true,true);
		//$this->debug($ret);
		return $ret;
	}
	public function get_own_essay($limit,$config = array()){
		$default = array( 'token' => '');
		$this->extend($default, $config);
		if ($limit == 'count') {
			return $this->list_own_essay('count');
		}
		$ret = $this->list_own_essay($limit);
		return $ret;
	}
	public function list_own_essay($limit)
	{
		$this->db->where('state', 'valid');
		$this->db->where('uid', $this->uid);
		if ($limit == 'count') {
			return $this->fetch_count();
		}
		$this->db->select('id as essay_id,pic,message,votes,ctime');
		$this->db->order_by('ctime desc');
		$this->db->limit($limit);
		$info = $this->db->get('yijie_essay')->result();
		foreach ($info as $key=>$val){
			$this->load->model('home/select/yijie/Yijie_votes_imodel');
			$val->voteLite = $this->Yijie_votes_imodel->get_votes_info_by_essayid($val->essay_id);
			//是否点赞
// 			$this->db->where('state','valid');
// 			$this->db->where('essay_id',$val->essay_id);
// 			$this->db->where('votes_id',$this->uid);
// 			$val->isLike = $this->fetch_row('yijie_votes') ? true : false;
			$val->isLike = in_array($this->uid,array_column($val->voteLite,'id'))? true : false;
			//底部评论
			$val->comment = new stdClass();
			$this->db->select('yijie_comment.message as text,yijie_comment.sent_id as sentId,yijie_comment.rece_id as receiveId,yijie_comment.ctime,yijie_comment.type');
			$this->db->select('user.nickname as sent');
			$this->db->select('b.nickname as receive');
			$this->db->where('yijie_comment.essay_id',$val->essay_id);
			$this->db->where('yijie_comment.state','valid');
			$this->db->where('user.id = yijie_comment.sent_id');
			$this->db->where('b.id = yijie_comment.rece_id');
			$this->db->from('user');
			$this->db->from('user as b');
			$this->db->order_by('yijie_comment.ctime asc');
			$query = $this->fetch_result('yijie_comment');
			foreach ($query as $k=>$v){
				if(!$v->type){
					unset($v->receiveId);
					unset($v->receive);
				}
				$v->ctime = date('Y-m-d H:i:s',$v->ctime);
				unset($v->type);
			}
			$val->comment= $this->arrtoobj($query);
			$val->ctime = date('Y-m-d H:i:s',$val->ctime);
			$val->pictureAdd = $this->set_pic_dir($val->pic,true,true);
			unset($val->pic);
		}
		return $this->arrtoobj($info);
	}
	//相册图片
	public function all_own_essay_picture($id)
	{
		$this->db->where('state', 'valid');
		$this->db->where('uid', $id);
		$this->db->select('id as essay_id,pic,message,ctime');
		$this->db->order_by('ctime desc');
		$info = $this->db->get('yijie_essay')->result();
		foreach ($info as $key=>$val){
			$val->ctime = date('Y-m-d H:i:s',$val->ctime);
			$val->pictureAdd = $this->set_pic_dir($val->pic,true,true);
			unset($val->pic);
		}
		return $info;
	}
}