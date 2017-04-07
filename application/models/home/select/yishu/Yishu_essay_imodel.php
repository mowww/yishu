<?php
class Yishu_essay_imodel extends MY_Model{
	var $table='yishu_essay';
	function  __construct()
	{
		parent::__construct();
		$this->lang->load('model/yishu_essay');
	}
	public function get_essay($config){
		$default = array('token'=>'', 'day_array' => '');
		$this->extend($default, $config);
		//$this->log('json: '.json_encode($_POST));
		if(!$this->day_array){
			throw new Err('yishu_essay_day_array_invalid');
		}
// 		$this->day_array = array(
// 				'2017-1-15'
// 		);
// 		
        if(!$this->is_not_json($this->day_array)){
        	$this->day_array = json_decode($this->day_array);
        }
        
		$ret = array();
		if(is_array($this->day_array)){
			$ret = array();
			foreach ($this->day_array as $key){
				$day = strtotime($key);
				$this->db->select('id,uid,cover,colour,tag,pic,ctime');
				$this->db->where('ctime >=',$day);
				$this->db->where('ctime <',$day+24*60*60);
				$this->db->where('uid',$this->uid);
				$this->db->where('state','valid');
				if($query = $this->fetch_query()->result()){
					foreach ($query as $row ){
						$ret["$key"][] = $this->translate($row,'cover,colour,tag');
					}	
				}	
			}
			//$this->debug($this->db->last_query());
		}else{
			$day = strtotime($this->day_array);
			$this->db->select('id,uid,cover,colour,tag,pic,ctime');
			$this->db->where('ctime >=',$day);
			$this->db->where('ctime <',$day+24*60*60);
			$this->db->where('uid',$this->uid);
			$this->db->where('state','valid');
			if($query = $this->fetch_query()->result()){
					foreach ($query as $row ){
						$ret["$this->day_array"][] = $this->translate($row,'cover,colour,tag');
					}	
			}
		}	
     return $ret;
	} 
	protected function translate($row, $config = null, $extend = null)
	{
		if ($config) {
			if ($this->translate_required('cover', $config)) {
				$row->cover = $row->cover ? true : false;
			}
			if ($this->translate_required('colour', $config)) {
				$row->colour = $row->colour ? $row->colour : '-';
			}
			if ($this->translate_required('tag', $config)) {
				if($row->tag){
					$tag = unserialize($row->tag);
					$t = array();
					foreach ($tag as $key){
						$this->load->model('home/select/yishu/Yishu_tag_imodel');
						$t[] = $this->Yishu_tag_imodel->get_tag_by_id($key);
					}
					$row->tag = $t;
				}
			}
		}
		return $row;
	}
}