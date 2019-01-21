<?php
	//echo json_encode($pageData);

?>
<ul class="nav navbar-nav navbar-right">
  <!--<li class="dropdown">
  <a href="#" class="dropdown-toggle icon-menu" data-toggle="dropdown">
  <i class="lnr lnr-alarm"></i>
  <span class="badge bg-danger">5</span></a>
  <ul class="dropdown-menu notifications">
  <li><a href="#" class="notification-item"><span class="dot bg-danger"></span>第一条消息</a></li>
  <li><a href="#" class="notification-item"><span class="dot bg-success"></span>第一条消息</a></li>
  <li><a href="#" class="notification-item"><span class="dot bg-warning"></span>第一条消息</a></li>
  <li><a href="#" class="more">查看所有消息</a></li></ul>
  </li>-->
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
      <img src="<?php echo $pageData['selfInfo']['headicon'];?>" class="img-circle" alt="Avatar">
      <span><?php echo $pageData['selfInfo']['nickname'];?></span>
      <em class="icon-submenu lnr lnr-chevron-down"></em>
    </a>
    <ul class="dropdown-menu">
      <!--<li><a href="#"><i class="lnr lnr-user"></i> <span>个人中心</span></a></li>
      <li><a href="#"><i class="lnr lnr-envelope"></i> <span>消息</span></a></li>-->
      <!--li>
        <a id="option" href="#">
          <i class="lnr lnr-cog"></i>
          <span>设置</span></a>
      </li-->
      <li>
        <a id="logout" href="#">
          <i class="lnr lnr-exit"></i>
          <span>注销</span>
		</a>
      </li>
    </ul>
  </li>
</ul>
<script>
    if(document.OnPartLoad) {
        document.OnPartLoad(<?php echo json_encode($pageData);?>);
    }
</script>