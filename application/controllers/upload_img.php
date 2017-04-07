<?php
class Upload_img extends CI_Controller{
	 public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
    }
	public function index(){
		if($_POST){
			$this->load->model('upload_imodel');
			if($this->upload_imodel->addSubmit()){
				print_r('上传成功');
			}else{
				print_r('上传失败');
			};
			
		}
		else $this->load->view('upload_img');
	}
}