<?php 
  header('Content-Type: text/html; charset=utf-8');

  require_once '../functions.php';
  // 判断用户是否登录
  xiu_get_current_user();

  //接收筛选参数
  //==========================================
  $where = ' 1 = 1';
  $search= '';
  //分类筛选
  if(isset($_GET['category'])&&$_GET['category']!=='-1'){
    $where .= ' and posts.category_id= ' . $_GET['category'];
    $search .='&category='.$_GET['category'];
  }
  if(isset($_GET['status'])&&$_GET['status']!=='-1'){
    $where .= " and posts.status= '{$_GET['status']}'";
    $search .= '&status='.$_GET['status'];
  }
  // 处理分页参数
  //每页显示的条数
  $size=20;
  $page=empty($_GET['page']) ? 1 : (int)$_GET['page'];

  if($page<1){
    //不可能有这种情况
    header('Location: /admin/posts.php?page=1' .$search);
  }

  //只要是处理分页功能一定会用到最大的页码数
  //最大页数$total_page=ceil($total_count/$size)
  $total_count=xiu_fetch_one("select count(1) as num from posts 
    inner join categories on posts.category_id=categories.id
    inner join users on posts.user_id=users.id
    where {$where};");
  $total_count=(int)$total_count['num'];
  $total_page=(int)ceil($total_count/$size);
  // ==>51
  if($page>$total_page){
    //不可能有这种情况
    header('Location: /admin/posts.php?page='.$total_page.$sear);
  }
  
  //获取全部数据,联合查询
  //===========================================
  //计算出跳过多少条
  $offset = ($page - 1) * $size;
  $posts=xiu_fetch_all("select
  posts.id,
  posts.title,
  users.nickname as user_name,
  categories.`name` as category_name,
  posts.created,
  posts.`status`
from posts
inner join categories on posts.category_id=categories.id
inner join users on posts.user_id=users.id
where {$where}
order by posts.created desc
limit {$offset},{$size} ;");

  //查询所有分类
  $categories=xiu_fetch_all('select * from categories;');

//处理分页页码
// ====================================================
  $visiable = 5;
// 计算最大和最小展示页码
  $begin=$page-($visiable-1)/2;
  $end=$begin+$visiable-1;

  //考虑合理性问题
  $begin = $begin<1 ? 1 : $begin;//确保begin不会小于1
  $end=$begin+$visiable-1;                 //确保begin和end的关系同步

  $end= $end>$total_page ? $total_page : $end;//确保end不会大于total_pages
  $begin = $end - $visiable+1;//确保begin和end的关系同步
  $begin = $begin<1 ? 1 : $begin;//确保begin不会小于1

  // 计算页码开始
  // $visiable = 5;
  // $region = ($visiable-1)/2;//左右区间
  // $begin=$page-$region;//开始页码
  // $end=$begin+$visiable;//结束页码+1
  // //$begin>=1
  // //确保$begin最小为1
  // if($begin<1){
  //   //begin修改意味者end也要修改
  //   $begin=1;
  //   $end=$begin+$visiable;
  // }
  // //$end<=最大页数
  // if($end > $total_page+1){
  //   //end超出范围
  //   $end = $total_page+1;
  //   $begin=$end-$visiable;
  //   if($begin<1){
  //      $begin=1;
  //   }
  // }
  //处理数据格式转换逻辑
       
  /**
   * 转换状态显示
   * @param  string $status 英文状态
   * @return string         中文状态
   */
  function xiu_convert_status($status){
    $dict=array(
      'published'=>'已发布',
      'drafted'=>'草稿',
      'trashed'=>'回收站'
      );
    return isset($dict[$status]) ? $dict[$status] : '未知';
  }

  /**
   * 转换时间格式
   * @param  [type] $created [description]
   * @return [type]          [description]
   */
  function xiu_convert_data($created){
    //==>'2018-8-16 14:23:00'
    $timestamp=strtotime($created);
    return date('Y年m月d日<b\r>H:i:s',$timestamp);
  }

  // /**
  //  * 获取分类状态
  //  * @param  [type] $category_id [description]
  //  * @return [type]              [description]
  //  */
  // function xiu_get_category($category_id){
  //   $name= xiu_fetch_one("select name from categories where id={$category_id}");
  //   return $name['name'];
  // }

  // /**
  //  * 获取作者
  //  * @param  [type] $user_id [description]
  //  * @return [type]          [description]
  //  */
  // function xiu_get_user($user_id){
  //   $name= xiu_fetch_one("select nickname from users where id={$user_id}");
  //   return $name['nickname'];
  // }
 ?>






<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php  include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <select name="category" class="form-control input-sm">
            <option value="-1">所有分类</option>
            <?php foreach ($categories as $item): ?>
              <option value="<?php echo $item['id']; ?>"<?php echo isset($_GET['category'])&&$_GET['category']==$item['id']? ' selected' : ''; ?>><?php echo $item['name']; ?></option>
            <?php endforeach ?>
          </select>
          <select name="status" class="form-control input-sm">
            <option value="-1">所有状态</option>
            <option value="drafted"<?php echo isset($_GET['status'])&&$_GET['status']=='drafted' ? ' selected' : ''; ?>>草稿</option>
            <option value="published"<?php echo isset($_GET['status'])&&$_GET['status']=='published' ? ' selected' : ''; ?>>已发布</option>
            <option value="trashed"<?php echo isset($_GET['status'])&&$_GET['status']=='trashed' ? ' selected' : ''; ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="#">上一页</a></li>
           <?php for($i = $begin ; $i <= $end ; $i++): ?>
            <li<?php echo $i===$page ? ' class="active"' : '' ; ?>><a href="?page=<?php echo $i.$search; ?>"><?php echo $i; ?></a></li>
          <?php endfor ?>
          <li><a href="#">下一页</a></li>
          <?php //xiu_pagination($page,$total_page,'?p=%d'.$search); ?>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
          <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td><?php echo $item['title']; ?></td>
            <td><?php echo $item['user_name']; ?></td>
            <td><?php echo $item['category_name']; ?></td>
            <td class="text-center"><?php echo xiu_convert_data($item['created']); ?></td>
            <!-- 当输出的判断或者转换逻辑过于复杂，不建议直接写在混编位置 -->
            <td class="text-center"><?php echo xiu_convert_status($item['status']); ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/posts-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>

<?php $current_page='posts'; ?>
<?php  include 'inc/siderbar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
