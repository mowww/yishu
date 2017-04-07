<?php
class MY_Input extends CI_Input
{

	function __construct()
	{
		parent::__construct();
		//$this->token();
	}

	public function is_weixin()
	{
		if (DEVELOPMENT) {
			return true;
		}
		$useragent = $this->server('HTTP_USER_AGENT');
		if (strpos($useragent, "MicroMessenger") !== false) {
			return true;
		}
		return false;
	}

	function redirect_url()
	{
		return "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	public function is_os($os)
	{
		$agent = $_SERVER['HTTP_USER_AGENT'];
		if ($os == 'windows') {
			return preg_match('/(win)/i', $agent);
		} elseif ($os == 'mac') {
			return preg_match('/(Mac)/i', $agent);
		}
		return false;
	}

	public function token()
	{
		$t = array();
		$t['php_self'] = $this->server('PHP_SELF');
		$t['http_referer'] = $this->server('HTTP_REFERER');
		foreach($_GET as $k => $v) {
			$t[$k . ':get'] = $v;
		}
		foreach($_POST as $k => $v) {
			$t[$k . ':post'] = $v;
		}
		$ret = '';
		foreach($t as $k => $v) {
			$ret .= $k . ' => ' . $v . ',';
		}
		$ret = trim($ret, ',');
		return $ret;
	}

	public function is_ajax()
	{
		return ($this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') && $this->post('ajax_do');
	}

	/**
	 * 重写get 支持传入字符串的 $index,返回数组
	 *
	 * @access	public
	 * @param	string $index
	 * @param	bool $xss_clean
	 * @return	mixed $default 标识是否返回数组或给定默认值
	 */
	function get($index = NULL, $xss_clean = TRUE, $default = null)
	{
		// Check if a field has been provided
		if ($index === NULL and ! empty($_GET)) {
			$get = array();
			// loop through the full _GET array
			foreach(array_keys($_GET) as $key) {
				$get[$key] = $this->_fetch_from_array($_GET, $key, $xss_clean);
			}
			return $get;
		} elseif ($default && $index) {
			$ret = array();
			$indexs = explode(',', $index);
			foreach($indexs as $key) {
				$val = $this->_fetch_from_array($_GET, $key, $xss_clean);
				if (empty($val) && is_array($default) && isset($default[$key])) {
					$val = $default[$key];
				}
				$get[$key] = $val;
			}
			return $get;
		}
		return $this->_fetch_from_array($_GET, $index, $xss_clean);
	}

	/**
	 * 获取 easyui view serializeArray 提交的数据格式
	 * @param $fields
	 * @param $default
	 */
	function post_easyui($fields, $default = false)
	{
		$x = explode(",", $fields);
		foreach($x as $k) {
			$ret[$k] = $default;
		}
		foreach($_POST as $k => $v) {
			if (is_array($v) && isset($v['name']) && isset($v['value']) && isset($ret[$v['name']])) {
				$_k = $v['name'];
				$_POST[$_k] = $v['value'];
				//使用post 安全过滤
				$ret[$_k] = $this->post($_k, true);
			}
		}
		return $ret;
	}

	function post($index = NULL, $xss_clean = TRUE, $default = null)
	{
		//解析json数据,无法直接获取到post
		if (empty($_POST)) {
			$postjson = file_get_contents('php://input');
			$_POST = json_decode($postjson,true);
		}
		//json_encode($value);
		// Check if a field has been provided
		if ($index === NULL and ! empty($_POST)) {
			$get = array();
			// loop through the full _GET array
			foreach(array_keys($_POST) as $key) {
				$get[$key] = $this->_fetch_from_array($_POST, $key, $xss_clean);
			}
			return $get;
		} elseif ($default && $index) {
			$ret = array();
			$indexs = explode(',', $index);
			foreach($indexs as $key) {
				$val = $this->_fetch_from_array($_POST, $key, $xss_clean);
				if (empty($val) && is_array($default) && isset($default[$key])) {
					$val = $default[$key];
				}
				$get[$key] = $val;
			}
			return $get;
		}
		return $this->_fetch_from_array($_POST, $index, $xss_clean);
	}

	/**
	 * 返回整形IP
	 * @author kis 2011-09-13
	 * @param $ip dot ip
	 */
	function ip_long($ip = null)
	{
		if (! is_null($ip)) {
			if (! $this->valid_ip($ip)) {
				return false;
			}
		} else {
			$ip = $this->ip_address();
		}
		return sprintf("%u", ip2long($ip));
	}

	function get_os()
	{
		$agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		if (preg_match('/win/i', $agent) && strpos($agent, '95')) {
			$os = 'Windows 95';
		} else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
			$os = 'Windows ME';
		} else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
			$os = 'Windows 98';
		} else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
			$os = 'Windows XP';
		} else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
			$os = 'Windows 2000';
		} else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
			$os = 'Windows 7';
		} else if (preg_match('/win/i', $agent) && preg_match('/nt 6/i', $agent)) {
			$os = 'Windows Vista';
		} else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
			$os = 'Windows NT';
		} else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
			$os = 'Windows 32';
		} else if (preg_match('/linux/i', $agent)) {
			$os = 'Linux';
		} else if (preg_match('/unix/i', $agent)) {
			$os = 'Unix';
		} else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
			$os = 'SunOS';
		} else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
			$os = 'IBM OS/2';
		} else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent)) {
			$os = 'Macintosh';
		} else if (preg_match('/PowerPC/i', $agent)) {
			$os = 'PowerPC';
		} else if (preg_match('/AIX/i', $agent)) {
			$os = 'AIX';
		} else if (preg_match('/HPUX/i', $agent)) {
			$os = 'HPUX';
		} else if (preg_match('/NetBSD/i', $agent)) {
			$os = 'NetBSD';
		} else if (preg_match('/BSD/i', $agent)) {
			$os = 'BSD';
		} else if (preg_match('/OSF1/i', $agent)) {
			$os = 'OSF1';
		} else if (preg_match('/IRIX/i', $agent)) {
			$os = 'IRIX';
		} else if (preg_match('/FreeBSD/i', $agent)) {
			$os = 'FreeBSD';
		} else if (preg_match('/teleport/i', $agent)) {
			$os = 'teleport';
		} else if (preg_match('/flashget/i', $agent)) {
			$os = 'flashget';
		} else if (preg_match('/webzip/i', $agent)) {
			$os = 'webzip';
		} else if (preg_match('/offline/i', $agent)) {
			$os = 'offline';
		} else {
			$os = 'Unknown';
		}
		return $os;
	}

	function get_browse()
	{
		$agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		if (preg_match("|(360SE[^;^)^(]*)|i", $agent, $matches)) {
			$browser = $matches[1];
		} elseif (preg_match("|(myie[^;^)^(]*)|i", $agent, $matches)) {
			$browser = $matches[1];
		} elseif (preg_match("|(Netscape[^;^)^(]*)|i", $agent, $matches)) {
			$browser = $matches[1];
		} elseif (preg_match("|(Opera[^;^)^(]*)|i", $agent, $matches)) {
			$browser = $matches[1];
		} elseif (preg_match("|(NetCaptor[^;^^()]*)|i", $agent, $matches)) {
			$browser = $matches[1];
		} elseif (preg_match("|(TencentTraveler)|i", $agent, $matches)) {
			$browser = $matches[1];
		} elseif (preg_match("|(Firefox[0-9/\.^)^(]*)|i", $agent, $matches)) {
			$browser = $matches[1];
		} elseif (preg_match("|(Lynx[^;^)^(]*)|i", $agent, $matches)) {
			$browser = $matches[1];
		} elseif (preg_match("|(Konqueror[^;^)^(]*)|i", $agent, $matches)) {
			$browser = $matches[1];
		} elseif (preg_match("|(WebTV[^;^)^(]*)|i", $agent, $matches)) {
			$browser = $matches[1];
		} elseif (preg_match("|(msie[^;^)^(]*)|i", $agent, $matches)) {
			$browser = $matches[1];
		} elseif (preg_match("|(Maxthon[^;^ ^+^)^(]*)|i", $agent, $matches)) {
			$browser = $matches[1];
		} elseif (preg_match("|(Chrome[^ ^)^(]*)|i", $agent, $matches)) {
			$browser = $matches[1];
		} else {
			$browser = 'Unknown:' . (strlen($agent) > 15 ? substr($agent, 0, 15) : $agent);
		}
		return $browser;
	}

	public function is_local()
	{
		$ip = $this->ip_address();
		if ($ip) {
			if (preg_match("/^(127)/", $ip) || preg_match("/^(192)/", $ip)) {
			} else {
				return false;
			}
		} else {
			return false;
		}
		return true;
	}

	public function ip_address()
	{
		if ($this->ip_address !== FALSE) {
			return $this->ip_address;
		}
		$proxy_ips = config_item('proxy_ips');
		if (! empty($proxy_ips)) {
			$proxy_ips = explode(',', str_replace(' ', '', $proxy_ips));
			foreach(array(
					'HTTP_X_FORWARDED_FOR', 
					'HTTP_CLIENT_IP', 
					'HTTP_X_CLIENT_IP', 
					'HTTP_X_CLUSTER_CLIENT_IP') as $header) {
				if (($spoof = $this->server($header)) !== FALSE) {
					// Some proxies typically list the whole chain of IP
					// addresses through which the client has reached us.
					// e.g. client_ip, proxy_ip1, proxy_ip2, etc.
					if (strpos($spoof, ',') !== FALSE) {
						$spoof = explode(',', $spoof, 2);
						$spoof = $spoof[0];
					}
					if (! $this->valid_ip($spoof)) {
						$spoof = FALSE;
					} else {
						break;
					}
				}
			}
			$this->ip_address = ($spoof !== FALSE && in_array($_SERVER['REMOTE_ADDR'], $proxy_ips, TRUE)) ? $spoof : $_SERVER['REMOTE_ADDR'];
		} else {
			$this->ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		}
		if (! $this->valid_ip($this->ip_address)) {
			$this->ip_address = '0.0.0.0';
		}
		return $this->ip_address;
	}

	function ip_address_all()
	{
		$t = array();
		if ($r = $this->server('REMOTE_ADDR')) {
			$t[] = $r;
		}
		if ($r = $this->server('HTTP_CLIENT_IP')) {
			$t[] = $r;
		}
		if ($r = $this->server('HTTP_X_FORWARDED_FOR')) {
			$t[] = $r;
		}
		if ($r = $this->server('HTTP_X_REAL_IP')) {
			$t[] = $r;
		}
		$t = array_unique($t);
		$this->ip_address = implode(",", $t);
		if (! $this->ip_address) {
			$this->ip_address = '0.0.0.0';
		}
		return $this->ip_address;
	}
}