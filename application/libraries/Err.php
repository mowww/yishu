<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Err extends Exception
{
	var $_error_msg = array();

	public function __construct($error = '')
	{
		parent::__construct($error);
		$this->_error_msg[] = $error;
	}

	public function code($open = '', $close = '', $lang = false)
	{
		$str = '';
		if ($lang) {
			$CI = & get_instance();
		}
		foreach($this->_error_msg as $val) {
			if ($lang) {
				$v = '';
				if (preg_match("/^([a-z_]+[ ]?)*[a-z]$/", trim($val))) {
					$v = $CI->lang->line($val);
				}
				$val = empty($v) ? $val : $v;
			}
			$str .= $open . $val . $close;
		}
		return $str;
	}

	public function message($open = '', $close = '')
	{
		return $this->code($open, $close, true);
	}
}
