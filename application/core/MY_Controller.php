<?php
class MY_Controller extends CI_Controller
{
	var $data = array();

	public function __construct()
	{
		parent::__construct();
		//api访问,解决跨域问题。
		header("Access-Control-Allow-Origin: *");
	}

	public function debug($val, $exit = true, $var_dump = false)
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
	
}