<?php
class Img_model extends MY_Model{
	var $id='';
	var $ext ='';
	var $full_file ='';
	 public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
    }
    /*  头像、照片墙路径：。/head_pic/
     *  衣述照片路径：./yishu/
     *  衣界帖子路径：./yijie/
     */
	public function  upload_img($id,$dir_path = ''){
	   $this->load->helper('string');
       $config['upload_path'] = './uploads/'.$id.'/'; 
       if($dir_path){
       	$config['upload_path'] .= $dir_path;
       }
      // $this->debug($config['upload_path']);
       $this->create_dir($config['upload_path']); 
        //注意：此路径是相对于CI框架中的根目录下的目录
       $config['allowed_types'] = 'gif|jpg|png|jpe|bmp|jpeg';    //设置上传的图片格式
       $config['max_size'] = '2048';              //设置上传图片的文件最大值
//        $config['max_width']  = '5000';            //设置图片的最大宽度
//        $config['max_height']  = '5000';
       $config['file_name'] = date('YmdHis').random_string('alnum',5);
      // $this->debug($config);
       $this->load->library('upload', $config);   //加载CI中的图片上传类，并递交设置的各参数值
       $this->upload->initialize($config);
//        if (! $this->upload->do_upload($field)) {
//        	if ($this->upload->display_errors('', '') == '您上传的文件不能超过2M，请重新上传。') {
//        		throw new Err('您上传的文件不能超过2M，请重新上传。');
//        	} else {
//        		throw new Err($this->upload->display_errors('', ''));
//        	}
//        }
       if ($this->upload->do_upload('pic')) //pic 上传文件的字段名。$_FILES['pic']
      {   
            $this->full_file = $this->upload->data();     //此函数是返回图片上传成功后的信息
            //return $this->cut_pic();
            $this->cut_pic();
            return str_replace('/var/www/html','',$this->full_file['full_path']);
       }else{
	       	if ($this->upload->display_errors('', '') == '您上传的文件不能超过2M，请重新上传。') {
	       		throw new Err('您上传的文件不能超过2M，请重新上传。');
	       	} else {
	       		throw new Err($this->upload->display_errors('', ''));
	       	}
       }
       
   }
   //创建目录
    protected function create_dir($dir_path){
      		if(!is_dir($dir_path)){
//       			第一个参数：必须，代表要创建的多级目录的路径；
//       			第二个参数：设定目录的权限，默认是 0777，意味着最大可能的访问权；
//       			第三个参数：true表示允许创建多级目录
      			mkdir($dir_path,0777,true);
      		}
     }
    //修改尺寸
    protected function cut_pic()
	{
		if(!$this->full_file){
			throw new Exception('文件为空');
		}
		$w = $this->full_file['image_width'];
		$h = $this->full_file['image_height'];
		$newwidth= 500;
		$newheight = $newwidth*$h/$w;
		$filename = $this->full_file['full_path'];
		//全路径  F:/wamp/www/yishu/uploads/5/20161124092940nick.jpg
		$this->ext = $this->full_file['file_ext'];//.jpg
		$thumb = imagecreatetruecolor($newwidth,$newheight);
		//说明：imagecreatetruecolor() 返回一个图像标识符，代表了一幅大小为 $newwidth和 $newheight的黑色图像
		$source = imagecreatefromjpeg($filename);
		//说明：imagecreatefromjpeg() 返回一图像标识符，代表了从给定的文件名取得的图像
		ImageCopyResampled($thumb,$source,0,0,0,0,$newwidth,$newheight,$w,$h);
		//imagecopyresampled -- 重采样拷贝部分图像并调整大小。
		switch($this->ext) {
			case '.jpg' :
				return imagejpeg($thumb,$this->get_shot_name());
				break;
			case '.png' : 
				return imagepng($thumb,$this->get_shot_name());
				break;
			case '.jpeg' :
				return imagejpeg($thumb,$this->get_shot_name());
				break;
			default:
				return imagejpeg($thumb,$this->get_shot_name());

		}
	}
	protected function get_shot_name()
	{
		$pathinfo = pathinfo($this->full_file['full_path']);
		$fileinfo = explode('.', $pathinfo['basename']);
		$filename = $fileinfo[0] . '_small' . $this->ext;
		return $pathinfo['dirname'] . '/' . $filename;
	}
}