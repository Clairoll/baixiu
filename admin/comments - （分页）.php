<?php 
 require_once '../functions.php';
  // 判断用户是否登录
  xiu_get_current_user();

 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <style>
    #loading{
      display: flex;
      align-items: center;
      justify-content: center;
      position: fixed;
      left: 0;
      top: 0;
      right: 0;
      bottom:0;
      background-color: rgba(0,0,0,.7);
      z-index: 999;

    }
    .flip-txt-loading {
      font: 26px Monospace;
      letter-spacing: 5px;
      color: #fff;
    }

    .flip-txt-loading > span {
      animation: flip-txt  2s infinite;
      display: inline-block;
      transform-origin: 50% 50% -10px;
      transform-style: preserve-3d;
    }

    .flip-txt-loading > span:nth-child(1) {
      -webkit-animation-delay: 0.10s;
              animation-delay: 0.10s;
    }

    .flip-txt-loading > span:nth-child(2) {
      -webkit-animation-delay: 0.20s;
              animation-delay: 0.20s;
    }

    .flip-txt-loading > span:nth-child(3) {
      -webkit-animation-delay: 0.30s;
              animation-delay: 0.30s;
    }

    .flip-txt-loading > span:nth-child(4) {
      -webkit-animation-delay: 0.40s;
              animation-delay: 0.40s;
    }

    .flip-txt-loading > span:nth-child(5) {
      -webkit-animation-delay: 0.50s;
              animation-delay: 0.50s;
    }

    .flip-txt-loading > span:nth-child(6) {
      -webkit-animation-delay: 0.60s;
              animation-delay: 0.60s;
    }

    .flip-txt-loading > span:nth-child(7) {
      -webkit-animation-delay: 0.70s;
              animation-delay: 0.70s;
    }

    @keyframes flip-txt  {
      to {
        -webkit-transform: rotateX(1turn);
                transform: rotateX(1turn);
      }
    }
  </style>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php  include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right">
         
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>

<?php $current_page='comments'; ?>
<?php  include 'inc/siderbar.php'; ?>

<div id="loading" style="display: none;"> 
  <div class="flip-txt-loading">
    <span>L</span><span>o</span><span>a</span><span>d</span><span>i</span><span>n</span><span>g</span>
  </div>
</div>

  <script id="comments_tmpl" type="text/x-jsrender">
      {{for comments}}
        <tr
          {{if status == 'held'}} class="warning"
          {{else status == 'rejected'}} class="danger" 
          {{/if}}
        >
          <td class="text-center"><input type="checkbox"></td>
          <td>{{:author}}</td>
          <td>{{:content}}</td>
          <td>《{{:post_title}}》</td>
          <td>{{:created}}</td>
          <td>{{:status}}</td>
          <td class="text-center">
            {{if status == 'held'}}
            <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
            <a href="post-add.html" class="btn btn-warning btn-xs">拒绝</a>
            {{/if}}
            <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
          </td>
        </tr> 
      {{/for}}
   </script>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
   <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
   <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script>


    $(document)
      .ajaxStart(function(){
        NProgress.start();
        //显示loading
        $('#loading').fadeIn();
        // $('#loading').css('display','flex');
      })
      .ajaxStop(function(){
        NProgress.done();
        $('#loading').fadeOut();
        // $('#loading').css('display','none');
      })
  //发送AJAX获取列表所需数据
  // $.get('/admin/api/comments.php',{page:3},function(res){
  //   //请求完成过后自动执行
  //   //将数据渲染到页面上
  //   // //准备一个给模板使用的数据
  //   // var data={};
  //   // data.comments=res;
  //   var html=$('#comments_tmpl').render({comments:res});
  //   $('tbody').html(html);
  // });
 

  function loadPageData(page){
    $.get('/admin/api/comments.php',{page:page},function(res){
      $('.pagination').twbsPagination({
        first: '&laquo;',
        last: '&raquo;',
        prev: '&lt;',
        next: '&gt;',
        totalPages:res.total_pages,
        visiablePages:5,
        initiateStartPageClick:false,
        onPageClick:function(e,page){
          // 第一次初始化就会触发一次
          loadPageData(page);
    }
  });
      console.log(res);
      var html=$('#comments_tmpl').render({comments:res.comments});
      $('tbody').fadeOut(function(){
          $(this).html(html).fadeIn();
      });
    });
  }


  loadPageData(1);
  </script>
  <script>NProgress.done()</script>
</body>
</html>
