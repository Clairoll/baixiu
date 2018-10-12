<?php 

 /**
  * 封装大家公用的函数
  */
 require_once 'config.php';
 session_start();

// 定义函数时，函数名与内置函数名冲突的问题
// JS判断方式：typeof==='function'
// PHP判断方式:function_exists('函数名')
 
 /**
  * 获 取当前登录用户信息，如果没有获取到，则自动跳转到登录页面
  * @return [type] [description]
  */
function xiu_get_current_user () {
  if (empty($_SESSION['current_login_user'])) {
    // 没有当前登录用户信息，意味着没有登录
    header('Location: /admin/login.php');
    exit(); // 没有必要再执行之后的代码
  }
  return $_SESSION['current_login_user'];
}

/**
 * 通过一个数据库查询获取多条数据
 * @param $sql 需要执行的sql语句
 * 返回一个索引数组中嵌套一个关联数组
 */
function xiu_fetch_all($sql){
    $conn=mysqli_connect(XIU_DB_HOST , XIU_DB_USER , XIU_DB_PASS , XIU_DB_NAME , XIU_DB_PORT);
    if(!$conn){
      exit('连接失败');
    }

    $query=mysqli_query($conn,$sql);
    if(!$query){
      //查询失败
      return false;
    }

    $result=array();
    while($row=mysqli_fetch_assoc($query)){
      $result[]=$row;
    }

    // 关闭连接
    mysqli_free_result($query);
    mysqli_close($conn);
    return $result;

}


/**
 * 通过一个数据库查询获取单条数据
 * @param $sql 需要执行的sql语句
 * 返回一个关联数组
 */
function xiu_fetch_one($sql){
  $res=xiu_fetch_all($sql);
  return isset($res[0]) ? $res[0] : null;
}

/**
 * 执行一个增删改语句
 * @param  [type] $sql [description]
 * @return [type]      [description]
 */
function xiu_execute($sql){
  $conn=mysqli_connect(XIU_DB_HOST , XIU_DB_USER , XIU_DB_PASS , XIU_DB_NAME , XIU_DB_PORT);
    if(!$conn){
      exit('连接失败');
    }

    $query=mysqli_query($conn,$sql);
    if(!$query){
      //查询失败
      return false;
    }
    //对于增删改一类的操作，都是获取受影响行数
    $affected_rows=mysqli_affected_rows($conn);

    // 关闭连接
    mysqli_close($conn);
    return $affected_rows;

}



/**
 * 输出分页连接
 * @param  integer  $page   当前页码
 * @param  integer  $total  总页数
 * @param  string  $foemat  连接模板， %d 会被替换为具体页数
 * @param  integer $visible 可见页码数量（可选参数，默认为5）
 * @example <?php xiu_pagination(2,10,'/list.php?page=%d',5); ?>
 * 
 */
function xiu_pagination($page,$total, $format, $visible=5){
  //计算起始页码
  //当前页左侧应有几个页码，如果一共是5个，则左边是2个，右边也是两个
  $left = floor($visible/2);
  //开始页码
  $begin = $page - $left;
  //确保开始不能小于1
  $begin = $begin < 1 ? 1 : $begin;
  //结束页码
  $end = $begin + $visible - 1;
  //确保结束不能大于最大值$total
  $end = $end > $total ? $total : $end;
  //如果$end变了，$begin也要一起变
  $begin = $end - $visible + 1;
  //确保开始不能小于1
  $begin = $begin < 1 ? 1 : $begin;

  //上一页
  if($page - 1 > 0){
    printf('<li><a href="%s">&laquo;</a></li>',sprintf($format, $page - 1));
  }

  //省略号
  if($begin > 1){
    printf('<li class="disabled"><span>...</span></li>');
  }

  //数字页码
  for($i = $begin; $i <= $end; $i++){
    //经过以上的计算$i的类型可能为float类型，所以此处用 == 比较合适
    $activeClass = $i == $page ? ' class="active"' : '';
    printf('<li%s><a href="%s">%d</a></li>',$activeClass,sprintf($format, $i),$i);
  }

   //省略号
  if($end < $total){
    printf('<li class="disabled"><span>...</span></li>');
  }

  //下一页
  if($page + 1 <= $total){
    printf('<li><a href="%s">&laquo;</a></li>',sprintf($format, $page + 1));
  }
}