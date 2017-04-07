<?php
class Action extends MY_Controller{
	 public function __construct()
    {
        parent::__construct();
    }
    /**
	 * 邮箱验证
	 * @param  string $send_user 账号名称
	 * @param  string $to        接收邮箱地址
	 * @return [type]            [description]
	 */
	public function send_email($send_user='莫如宇',$to='a452865494a@qq.com') { 

		try{
		  $this->config->load('email');
		  $this -> load -> library('email');
		  $this->email->set_newline("\r\n");
		  $this->email->from('1696256807@qq.com',$send_user);
		  //yycanusher@126.com
		  //jixesfighter@163.com
		  $this->email->to($to);
		  $this->email->subject('CI的mail发送邮件!');
		  $this->load->model('home/dao/email/email_active_data_model');
		  $code = $this->email_active_data_model->set_email_active_code();
		  $this->email->message('验证链接为http://localhost/yishu/index.php/home/email/active/data/action/vaild_email?keyword='.$code.'  ,勿回复!');
		  // if( ! $this->email->send(FALSE)){
		  //  echo '发送失败!';
		  //  print_r($this->email->print_debugger());
		  // }else{
		  //  echo '发送成功!';
		  // }
		  if(!$this->email->send()){
		  		$this->debug("发送失败");	
		  }
		}catch(Err $e){
 			$this->debug($e->message());
 		}
		 
 	}
 	public function vaild_email(){
 		$keyword = $this->input->get('keyword');
 		$this->load->model('home/select/email/email_active_data_imodel');
 		try{
 			//解密keyword:code验证码，创建时间ctime
 			$code = $keyword;
 			//$ctime = 1;
 			// if(time()>$ctime+3600*24){
 			// 	$this->debug('链接已失效，请重新申请验证。');
 			// }
 			$ret = $this->email_active_data_imodel->get_email_active_code($code);
 			if($ret){
 				$this->debug('激活成功！');
 			}
 		}catch(Err $e){
 			$this->debug($e->message());
 		}
 		
 	}
}