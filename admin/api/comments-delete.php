<?php  
/**
 * 根据用户传过来的ID删除对应的数据
 */
require_once '../../functions.php';
//判断用户是否传ID过来
if(empty($_GET['id'])){
	exit('<h1>缺少必要参数</h1>');
}
//接收并保存ID
// $id=(int)$_GET['id'];
$id=$_GET['id'];
//==>'1 or 1=1'
//sql注入

$rows=xiu_execute('delete from comments where id in ('.$id.');');

// header('Location: /admin/categories.php');
echo json_encode($row > 0);