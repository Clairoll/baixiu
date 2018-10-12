<?php 
header('Content-Type: text/html; charset=utf-8');
  //载入数据库的配置文件
  require_once '../config.php';
  //开启session，即给用户找箱子
  session_start();

  
  function login(){
    //1.接受并校验
    //2.持久化
    //3.响应
    if(empty($_POST['email'])){
      $GLOBALS['message']='请填写邮箱';
      return;
    }
    if(empty($_POST['password'])){
      $GLOBALS['message']='请填写密码';
      return;
    }


    //接收数据
    $email=$_POST['email'];
    $password=$_POST['password'];

    //判断数据
    //当用户提交过来完整的表单信息局应该开始对其进行数据校验
    // 从数据库中获取数据验证信息
    //1.建立连接
    $conn=mysqli_connect(XIU_DB_HOST , XIU_DB_USER , XIU_DB_PASS , XIU_DB_NAME , XIU_DB_PORT);
    //2.判断链接是否成功
    if(!$conn){
      exit('<h1>连接数据库失败</h1>');
    }
    //3.开始查询
    $query=mysqli_query($conn,"select *from users where email ='{$email}' limit 1;");//limit 1意思是查询到第一条就不在查询
    //判断查询是否成功
    if(!$query){
      $GLOBALS['message']='登录失败，请重试!';
      return;
    }

    //5.取数据
    $user=mysqli_fetch_assoc($query);
    if (!$user) {
      //用户名不存在的情况
       $GLOBALS['message']='邮箱与密码不匹配';
       return;
    }

    if($user['password']!==$password){
      //密码不正确的情况
       $GLOBALS['message']='邮箱与密码不匹配';
       return;
    }

    //存一个登录标识
    // $_SESSION['is_logged_in']=true;
    // 为了后续可以直接获取当前用户登录信息，这里直接将用户信息放到session中
    $_SESSION['current_login_user']=$user;

  //到此一切ok
  //跳转
  header('location:/admin/');
  }

  if($_SERVER['REQUEST_METHOD']==='POST'){
    login();
  }
  
//退出登录页面，
  if($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['action']) &&$_GET['action']==='logout'){
    //删除cookie(登录标识)
    unset($_SESSION['current_login_user']);
  }

 ?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
</head>
<body>
  <div class="login">
    <form class="login-wrap<?php echo isset($message) ? ' shake animated' : '' ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate autocomplete="off">
    <!--novalidate去除input的默认验证  -->
      <img class="avatar" src="/static/assets/img/default.png" >
      <!-- 有错误信息时展示 -->

      <?php if(isset($message)): ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $message; ?>
      </div>
    <?php endif ?>

      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo empty($_POST['email']) ? '' : $_POST['email'] ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>
</body>
<script src="/static/assets/vendors/jquery/jquery.js"></script>
<script>
    $(function($){
      //1.单独作用域
      //2.确保页面加载后执行
      
      /**
       * 目标需求：在用户输入邮箱过后，页面上展示这个邮箱对应的头像
       * 实现：
       * ===>时机：邮箱文本失去焦点,并且能够拿到文本框中填写的邮箱时
       * ===>事情：获取这个文本框中填写的邮箱对应的头像地址，展示到上面的img元素上
       */
      var emailFormat=/^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/
      $('#email').on('blur',function(){

        var value=$(this).val();
        //忽略文本框为空，或者输入的不是邮箱
        if(!value||!emailFormat.test(value)) return;



       //到此用户在文本框输入了内容，且为一个合理的邮箱
      //获取这个文本框中填写的邮箱对应的头像地址，展示到上面的img元素上
      //因为客户端的JS无法直接操作数据库，应该通过js发送ajax请求，告诉服务端的某个接口，让这个接口帮助客户端获取头像地址
      
      $.get('/admin/api/avatar.php',{email:value},function(res){
          //希望res==>这个邮箱对应的头像地址
          if(!res) return;//没有拿到
          // 展示到img元素上
          // $('.avatar').fadeOut().attr('src',res).fadeIn();
          $('.avatar').fadeOut(function(){
            $(this).on('load',function(){
              $(this).fadeIn()
            }).attr('src',res);
          })
        })
      })
    })
</script>
</html>
