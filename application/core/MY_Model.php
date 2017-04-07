<?php
class MY_Model extends CI_Model
{
	var $table = '';
	var $limit = '';
	var $primary = 'id';
	var $info = null;
	var $id = 0;
	var $uid = 0;
	var $act = '';
	var $field_validate_database = false;
	var $login_time=0;
	var $site = "https://myishu.top";
	function __construct()
	{
		parent::__construct();
		$this->load->library('Err');
	}
	function __get($key)
	{
		$CI = & get_instance();
		if ($key == 'db') {
			if (! isset($CI->db)) {
				$CI->load->database();
			}
		}
		return $CI->$key;
	}

	public function get_id()
	{
		return $this->id;
	}
	
	public function set_id($val)
	{
		$this->id = (int)$val;
	}

	public function set_info($val)
	{
		$this->info = $val;
	}
	/*
	 * 输出最后一次数据库执行语句
	 */
	public function debug_last_query(){
		$this->debug($this->db->last_query());
	}
	/*
	 * 输出info
	*/
	public function debug_info(){
		$this->debug($this->info);
	}
	/*
	 *判断是否为json数据，是则解析返回,否则返回空
	 * 
	 */
	function is_not_json($str){  
   			 return is_null(@json_decode($str));
	}
	public function get_info($id = null,$table='')
	{
		if ($id !== null) {
			$this->set_id($id);
			$this->db->where($this->primary, $this->id);
			$this->info = $this->fetch_row($table);
		}
		return $this->info;
	}
	public function get_user_info($id = null)
	{
		if ($id !== null) {
			$this->set_id($id);
			$this->db->where($this->primary, $this->id);
			$query = $this->db->get('user');
			$this->info = $query->row();
		}
		return $this->info;
	}
	//检查是否存在该用户
	public function check_user_id($id = null)
	{
		if ($id !== null) {
			$this->db->where($this->primary, $id);
			$query = $this->db->get('user');
			$this->info = $query->row();
		}
		if(!$this->info){
			throw new Err('用户无效');
		}
		return $this->info;
	}
	//数组转换为对象
	function arrtoobj($array) {
		if (is_array($array)) {
			$obj = new StdClass();
			foreach ($array as $key => $val){
				$obj->$key = $val;
			}
		}
		else { $obj = $array; }
		return $obj;
	}
	function objtoarr($object) {
		if (is_object($object)) {
			foreach ($object as $key => $value) {
				$array[$key] = $value;
			}
		}
		else {
			$array = $object;
		}
		return $array;
	}
	/*
	 * 二维数组按多个字段排序
	 * array $data
	 * string $field 排序字段
	 * string $direction 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
	 */
	protected  function array_filed_sort($data,$field,$direction='SORT_ASC'){
		$sort = array(
				'direction' => $direction, 
				'field'     => $field,      
		);
		$arrSort = array();
		foreach($data AS $row){
			$arrSort[] = $row[$sort['field']];
		}
		if($sort['direction']){
			array_multisort($arrSort, constant($sort['direction']), $data);
		}
		return $data;
	}
	//组装图片路径
	protected  function set_pic_dir($pic_name,$site = FALSE,$cut = FALSE){
		$pic = $pic_name;
		if(!$pic){
			return 'http://myishu.top/yishu/avadar.png';
		}
		if($cut){
			$pic =str_replace('.','_small.',$pic);
		}
		if($site){
			$pic = $this->site.$pic;
		}
		return $pic;
	}
	//获取系统表的数据
	protected function get_system_config($list,$key)
	{
		$this->db->select('value');
		$this->db->where('state','valid');
		$this->db->where('list',$list);
		$this->db->where('key',$key);
		return $query = $this->fetch_row('system_config');
	}
	protected function fetch_count($table = null)
	{
		if ($table === null) {
			$table = $this->get_table();
		}
		return $this->db->count_all_results($table);
	}

	protected function fetch_query()
	{
		return $this->db->get($this->get_table());
	}

	protected function fetch_result($table = '',$return_array = FALSE)
	{
		$table = $table ? $table : $this->get_table();
		$query = $this->db->get($table);
		if ($query->num_rows() > 0) {
			if (! $return_array) {
				return $query->result();
			} else {
				return $query->result_array();
			}
		} else {
			return array();
		}
	}

	protected function fetch_result_array($table = '')
	{
		return $this->fetch_result($table,TRUE);
	}

	protected function fetch_row($table='',$rows = '', $return_array = FALSE)
	{
		$table = $table ? $table : $this->get_table();
		$query = $this->db->get($table);
		if ($query->num_rows() > 0) {
			if (! $return_array) {
				return $query->row();
			} else {
				return $query->row_array();
			}
		}
	}

	protected function fetch_row_array($table='',$rows = 0)
	{
		return $this->fetch_row($table,$rows, TRUE);
	}
	protected function translate_required($item, $config = null)
	{
		if ($config == 'all') {
			return true;
		} elseif (strpos($config, ',') === false) {
			return $item == $config;
		} else {
			$arr = explode(',', $config);
			return in_array($item, $arr);
		}
	}

	protected function extend($defaults = array(), $config = array(), $empty_check = false)
	{

		if (! is_array($defaults) || ! is_array($config)) {
			return false;
		}
		foreach($defaults as $key => $val) {
			if (isset($config[$key])) {
				if ($empty_check) {
					if ($config[$key]) {
						$val = $config[$key];
					}
				} else {
					$val = $config[$key];
				}
			}
			$method = 'set_' . $key;
			if (method_exists($this, $method)) {
				$this->$method($val);
			} else {
				$this->$key = $val;
			}
			$defaults[$key] = isset($this->$key) ? $this->$key : $val;
		}
		return $defaults;
	}

	protected function set_token($val)
	{
		
		if ($val) {
			$this->load->library('encrypt');
			$tt = json_decode($this->encrypt->decode($val));
			//13750050680 1234 的token用于测验
			if($val!='Q5lEibz4Zdy0mOPABx9Dxj084aexCc4kZozaAPl1dZs+Ux6I1f3tHQ0w7/HGY7PNoou617fV7GlI4YI/xQNkTt8l0iHEwPWWppQtYtdSkxHOOCseECat5ycg6xdm9rZ7'){
				if (! $tt || ! $tt->uid || ! $tt->login_time || $tt->login_time+3600*24*7 < time()) {
					//$this->log('接口3: '.$val);
					throw new Err('登录信息失效，请重新登陆');
				}
				//检查数据库登录时间是否匹配，不匹配则为另外一方登录。保持单方登录。
				$this->db->where('uid', $tt->uid);
				$row = $this->db->get('account_login')->row();
				if($row->login_time==0){
					throw new Err('账号已退出，请重新登陆');
				}
				if($row->login_time!=$tt->login_time){
					throw new Err('账号已在另一地登录，请重新登陆');
				}
			}
			$this->uid =  $tt->uid;
			//每次登陆检查用户是否存在。
			$this->check_user_id($this->uid);
			$this->login_time = $tt->login_time;
		} else {
			throw new Err('登录信息无效，请重新登陆');
		}
		$this->token = $val;
	}
	final protected function insert($data)
	{
		$this->db->insert($this->get_table(), $data);
		$this->id = $this->db->insert_id();
		return $this->id ? $this->id : false;
	}

	final protected function insert_duplicate($data, $fields, $extend = null)
	{
		if (! ($data && is_array($data))) {
			return false;
		}
		($extend !== null && ! is_array($extend)) ? $extend['table'] = $extend : '';
		$table = isset($extend['table']) ? $extend['table'] : $this->table;
		//相减
		$fields_subtract = isset($extend['fields_subtract']) ? $extend['fields_subtract'] : array();
		//相加
		$fields_add = isset($extend['fields_add']) ? explode(",", $extend['fields_add']) : array();
		$update_spec = isset($extend['update_spec']) ? $extend['update_spec'] : '';
		//mtime = IF( mtime IS NULL , VALUES (mtime),mtime );
		(! is_array($fields)) ? $fields = explode(",", $fields) : '';
		if (! is_array(current($data))) {
			$keys = array_keys((array)current($data));
		} else {
			$keys = array_keys(current($data));
		}
		$_sql = "insert into " . $this->db->dbprefix($table) . " (`" . implode('`,`', $keys) . "`) values ";
		$values = array();
		foreach($data as $key => $val) {
			$_info = array();
			foreach($keys as $_key => $field) {
				if (! is_array($val)) {
					$new_val = $val->$field;
				} else {
					$new_val = $val[$field];
				}
				$_info[] = "'" . $new_val . "'";
			}
			$values[] = "(" . implode(', ', $_info) . ")";
		}
		$update_arr = array();
		foreach($fields as $key => $val) {
			if (in_array($val, $fields_add)) {
				$update_arr[] = "`$val` = `$val` + values(`$val`)";
			} elseif (in_array($val, $fields_subtract)) {
				$update_arr[] = "`$val` = `$val` - values(`$val`)";
			} else {
				$update_arr[] = "`$val` = values(`$val`)";
			}
		}
		if ($update_spec) {
			$update_arr[] = $update_spec;
		}
		$_sql .= implode(', ', $values) . " on duplicate key update " . implode(', ', $update_arr);
		return $this->db->query($_sql);
	}

	final protected function is_update($info, $return = false)
	{
		$res = false;
		$ret = array();
		foreach($info as $key => $val) {
			if (isset($this->info->$key)) {
				$res = ($val == $this->info->$key) ? false : true;
			} else {
				$res = true;
			}
			if ($res) {
				if ($return) {
					$ret[$key] = $val;
				} else {
					break;
				}
			}
		}
		if ($return) {
			return $ret;
		}
		return $res;
	}

	final protected function key_value($k, $v = null)
	{
		$query = $this->db->get($this->get_table());
		$ret = array();
		foreach($query->result() as $row) {
			$ret[$row->$k] = $row->$v;
		}
		return $ret;
	}

	final protected function get_table()
	{
		return $this->table;
	}

	protected function set_table($val, $check = false)
	{
		if ($check) {
			if (! $this->db->table_exists($val)) {
				return false;
			}
		}
		$this->table = $val;
		return true;
	}

	protected function translate($object, $fields = null)
	{
	}

	final protected function translate_result($translate = '', $extend = null)
	{
		if (! $translate) {
			return $this->fetch_result();
		}
		$query = $this->fetch_query();
		$ret = array();
		foreach($query->result() as $row) {
			$ret[] = $this->translate($row, $translate, $extend);
		}
		return $ret;
	}

	final protected function update($values, $where = null)
	{
		if ($where !== null) {
			if (is_array($where)) {
				foreach($where as $key => $val) {
					if (is_array($val)) {
						$this->db->where($val);
					} else {
						$this->db->where($val, NULL, false);
					}
				}
			}
		} else {
			$this->db->where(array($this->primary => $this->id));
		}
		$this->db->update($this->get_table(), $values);
		$rows = $this->db->affected_rows();
		return $rows ? $rows : false;
	}

	final protected function fetch_result_key($key, $table = '')
	{
		if ($table) {
			$this->set_table($table);
		}
		if (! $key) {
			return $this->fetch_result();
		}
		$query = $this->fetch_query();
		$ret = array();
		foreach($query->result() as $row) {
			$ret[$row->$key] = $row;
		}
		return $ret;
	}

	protected function set_limit($limit)
	{
		$this->limit = $limit;
	}

	protected function log($content, $controller = '', $action = '')
	{
		$this->load->model('home/dao/site/Site_log_model');
		$this->Site_log_model->insert_do($content, $controller, $action);
	}

	protected function debug($val, $exit = true, $var_dump = false)
	{
		echo "<pre>";
		if ($var_dump) {
			var_dump($val);
		} else {
			print_r($val);
		}
		if ($exit) {
			exit();
		}
	}

	protected function tt($type)
	{
		return $this->_tt($this->host($type), $this->port($type));
	}

	protected function host($key)
	{
		if (isset($this->config_memcache[$key]['0'])) {
			return $this->config_memcache[$key]['0'];
		}
		return false;
	}

	protected function port($key)
	{
		if (isset($this->config_memcache[$key]['1'])) {
			return $this->config_memcache[$key]['1'];
		}
		return false;
	}

	protected function _tt($host, $port)
	{
		$key = $host . '_' . $port;
		if (! isset($this->map_tt[$key])) {
			require_once FCPATH . 'application/libraries/Tyrant.php';
			try {
				$this->map_tt[$key] = Tyrant::connect($host, $port);
			} catch(Tyrant_Exception $e) {
				$this->log("tyrant_connect_exception, host : $host, port : $port, exception : " . $e->getMessage());
				sleep(3);
				try {
					$this->map_tt[$key] = Tyrant::connect($host, $port);
				} catch(Tyrant_Exception $e) {
					$this->map_tt[$key] = null;
					$this->log("tyrant_reconnect_exception, host : $host, port : $port, exception : " . $e->getMessage());
				}
			}
		}
		return $this->map_tt[$key];
	}

	protected function put_tt($tt, $key, $value)
	{
		if (! $tt) {
			$this->log("tt object is null");
			return false;
		}
		try {
			$tt->put($key, $value);
		} catch(Tyrant_Exception $e) {
			$this->log($e->getMessage());
			return false;
		}
		return true;
	}
}
