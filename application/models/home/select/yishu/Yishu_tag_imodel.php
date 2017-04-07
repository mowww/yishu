<?php
class Yishu_tag_imodel extends MY_Model{
	var $table='yishu_tag';
	function  __construct()
	{
		parent::__construct();
		$this->lang->load('model/yishu_essay');
	}
	public function get_tags($config){
		$default = array('token'=>'');
		$this->extend($default, $config);
		$this->db->select('id,name,parent_id');
		$this->db->where('state','valid');
		$this->db->order_by('parent_id asc');
		$ret = array();
		if($query = $this->fetch_result_array()){
			foreach ($query as $key=>$val){
				if($val['parent_id']==0){
					$t = array();
					unset($query[$key]);
					if($query){
						foreach ($query as $k=>$v){
							if($v['parent_id']==$val['id']){
								$t[] = $v;
								unset($query[$k]);
							}
							
						}
					}
					$val['tag'] = $t;
					$ret[]	 = $val; 
				}
			}
			//分类没标签的，不返回改客户端
			foreach ($ret as $key=>$val){
				if(!$val['tag']){
					unset($ret[$key]);
				}
			}
		}else{
			throw new Err('标签数据为空');
		}
     return $ret;
	} 
	/*
	 * 根据id,获取标签，以及归属分类
	 * 
	*/
	public function get_tag_by_id($id){
		$this->db->select('yishu_tag.id,yishu_tag.name,yishu_tag.parent_id');
		$this->db->select('a.name as parent_name');
		$this->db->where('yishu_tag.state','valid');
		$this->db->where('yishu_tag.id',$id);
		$this->db->where('a.id = yishu_tag.parent_id');
		$this->db->from('yishu_tag a');
		return $this->fetch_row();
	}
}