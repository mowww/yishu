<!DOCTYPE html>
<html>
<head>
	<title>hello world!</title>
	<script type="text/javascript" src="/yishu/js/jquery.js"></script>
</head>
<body>
<form action="/yishu/index.php/home/yishu/essay/action/publish_essay" enctype="multipart/form-data" method="post" >
<input type="hidden" name="token" value="Q5lEibz4Zdy0mOPABx9Dxj084aexCc4kZozaAPl1dZs%2BUx6I1f3tHQ0w7%2FHGY7PNoou617fV7GlI4YI%2FxQNkTt8l0iHEwPWWppQtYtdSkxHOOCseECat5ycg6xdm9rZ7"/>
<input type="hidden" name="colour" value="#fff"/>
<input type="file" name="pic" /><br/>
<input type="submit" name=""/>
</form>
</body>
<script type="text/javascript">
//post测试
function f(){
	  console.log('1');
	  $.post('http://myishu.top/yishu/home/yijie/essay/action/list_square_essay',
			  {token: 'Q5lEibz4Zdy0mOPABx9Dxj084aexCc4kZozaAPl1dZs+Ux6I1f3tHQ0w7/HGY7PNoou617fV7GlI4YI/xQNkTt8l0iHEwPWWppQtYtdSkxHOOCseECat5ycg6xdm9rZ7'},
			  function(result){
		 		 console.log(error);
	 		 });
};
//f();
</script>
</html>