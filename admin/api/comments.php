<?php  

	//接收客户端的ajax请求，返回评论数据
	
	 require_once '../../functions.php';
//取得客户端传递过来的分页页码
$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
$length = 30;
$offset=($page-1)*$length;
	 $sql=sprintf('select 
	comments.*,
	posts.title as post_title
from comments
inner join posts on comments.post_id=posts.id
order by comments.created desc
limit %d, %d;',$offset , $length);
  	//查询所有的评论
  	$comments=xiu_fetch_all($sql);
  	//查询到所有数量
  	$total_count = xiu_fetch_one('select count(1) as count
  	from comments
  	inner join posts on comments.post_id=posts.id
');
  	$total_count = $total_count['count'];
  	//虽然返回的是float类型，但一定是一个整数
  	$total_pages =ceil( $total_count / $length);

  	//因为网络之间传输的只能是字符串
  	//所以将数据转化为字符串
  	$json = json_encode(array(
  		'total_pages' => $total_pages,
  		'comments' => $comments
  		));

  	//设置响应体类型为json
  	header('Content-Type: application/json');

  	//响应给客户端
  	echo $json;
  	

