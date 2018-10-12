<?php  
/**
 * 根据用户邮箱获取用户头像
 * email==>image
 */
header('Content-Type: text/html; charset=utf-8');
require_once '../../config.php';
//1.接收传递过来的邮箱
if(empty($_GET['email'])){
	exit('<h1>缺少必要参数</h1>');
}
$email=$_GET['email'];
//2.查询对应的邮箱地址
 $conn=mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME,XIU_DB_PORT);
 if(!$conn){
 	exit('<h1>连接数据库失败</h1>');
 }
 $res=mysqli_query($conn,"select avatar from users where email='{$email}' limit 1;");
 if(!$res){
 	exit('<h1>查询失败</h1>');
 }
 $row=mysqli_fetch_assoc($res);
//3.echo
echo $row['avatar'];