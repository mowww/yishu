<?php
class Yishu_essay_model extends MY_Model{
	var $table='yishu_essay';
	function  __construct()
	{
		parent::__construct();
		$this->lang->load('model/yishu_essay');
	}
	public function publish_essay($config){
		$default = array('token'=>'', 'cover' => '','colour'=>'','tag' => '','pic'=>'');
		$this->extend($default, $config);
		//$this->log('图片：'.json_encode($_FILES));
		if(!isset($_FILES['pic'])||!$_FILES['pic']['size']){
			throw new Err('yishu_essay_pic_null');
		}
		$num_array = $this->check_public_num();
		if(!$num_array['flage']){
			throw new Err('yishu_essay_num_over_valid');
		};
		$this->load->model('Img_model');
		$pic = $this->Img_model->upload_img($this->uid,'yishu/');
		$data = array(
				'uid'=>$this->uid,
				'pic'=>$pic,
				'cover'=>$this->cover?1:0,
				'tag'=>serialize($this->tag),
				'colour'=>$this->colour,
				'state'=>'valid',
				'ctime'=>time()
		);
		if(!$this->insert($data)){
			throw new Err('yishu_essay_insert_falid');
		};
		$this->update_public_num($num_array);
	} 
	public function del_essay($config){
		$default = array('token'=>'', 'id' => '');
		$this->extend($default, $config);
		//判断是否有记录，是否该用户发布，记录是否有效
		if(!$this->get_info($this->id)||$this->info->uid!=$this->uid||$this->info->state!='valid'){
			throw new Err('yishu_essay_id_invalid');
		}
		$data = array(
				'state'=>'invalid',
				'dtime'=>time()
		);
		if(!$this->update($data)){
			throw new Err('yishu_essay_del_falid');
		};
	}
	public function update_essay_cover($config){
		$default = array('token'=>'', 'id' => '');
		$this->extend($default, $config);
		$ret = '';
		//判断是否有记录，是否该用户发布，记录是否有效
		if(!$this->get_info($this->id)||$this->info->uid!=$this->uid||$this->info->state!='valid'){
			throw new Err('yishu_essay_id_invalid');
		}
		if($this->info->cover){
			throw new Err('yishu_essay_cover_already');
		}
		//查出原封面id
		$time = strtotime(date('Y-m-d ',$this->info->ctime));
		$this->db->where('ctime>=', $time);
		$this->db->where('ctime<', $time+24*60*60);
		$this->db->where('uid', $this->uid);
		$this->db->where('state', 'valid');
		$this->db->where('cover', '1');
		if($query = $this->fetch_row()){
			//取消该封面
			$data = array(
					'cover'=>'0',
					'mtime'=>time()
			);
			$where = array(array('id'=>$query->id));
			if(!$this->update($data,$where)){
				throw new Err('yishu_essay_update_essay_cover_falid');
			};
			$ret = $query->id;
		}
		//更新最新封面
		$data = array(
				'cover'=>'1',
				'mtime'=>time()
		);
		if(!$this->update($data)){
			throw new Err('yishu_essay_update_essay_cover_falid');
		};
		return $ret;
	}
	protected  function get_publish_num(){
		$ret = array();
		$this->db->select('id,num,last_eassay');
		$this->db->where('uid',$this->uid);
		$query = $this->db->get('yishu_num')->result_array();
		if(!$query){
			$data = array(
					'uid'=>$this->uid,
					'num'=>3,
					'last_eassay'=>0,
					'ctime'=>time(),
			);
			if(!$this->db->insert('yishu_num',$data)){
				throw new Err('yishu_essay_num_create_valid');
			}
			$ret['num'] = 3;
			$ret['last_eassay'] = 0;
		}else{
			$ret['num'] = $query[0]['num'];
			$ret['last_eassay'] = $query[0]['last_eassay'];
		}
		return $ret;
	}
	protected function check_public_num(){
		$day_num_array = $this->get_publish_num();
		$day_time = strtotime(date('Y-m-d',time()));
		$day_num_array['flage'] = 0;
		//最后一条记录发布时间不为当天
		if($day_num_array['last_eassay']<$day_time){
			$day_num_array['flage'] = 1;
		}elseif($day_num_array['num']){
			$day_num_array['flage'] = 1;
		}
		return $day_num_array ;
	}
	protected function update_public_num($num_array){
		$day_time = strtotime(date('Y-m-d',time()));
		$data = array(
				'last_eassay'=> time(),
		);
		//最后一条记录发布时间不为当天
		if($num_array['last_eassay']<$day_time){
			$data['num'] = 2;
		}elseif($num_array['num']){
			$data['num'] = $num_array['num'] - 1;
		}
		$this->db->where('uid',$this->uid);
		$this->db->update('yishu_num',$data);
	}
}