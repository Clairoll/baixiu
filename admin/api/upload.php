<?php  
	//var_dump($_FILES['avatar']);
	//接受文件
	//保存文件
	//返回文件访问URL
	
	//判断文件是否为空
	if(empty($_FILES['avatar'])){
		exit('请选择文件上传');
	}

	$avatar=$_FILES['avatar'];

	if($avatar['error']!==UPLOAD_ERR_OK){
		exit('文件上传失败');
	}

	//校验文件类型和大小
	

	//移动文件路径
	$ext=pathinfo($avatar['name'],PATHINFO_EXTENSION);
	$target='../../static/uploads/img-' . uniqid() . '.' .$ext;
	if(!move_uploaded_file($avatar['tmp_name'], $target)){
		exit('文件上传失败');
	}

	//上传成功exit('请选择文件上传');
	echo substr($target,5);