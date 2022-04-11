    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../home/" Style='background-color:white;'>
        <div class="sidebar-brand-icon rotate-n-15">
          <img src="../img/hallalogo.png" alt="logo" height="45" >
        </div>
        
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item active">
        <a class="nav-link" href="index.php">
          <i class="fas fa-fw fa-home"></i>
          <span><?php echo $oDB->lang('Home')?></span></a>
      </li>


      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item active">
        <a class="nav-link" href="product.php">
          <i class="fas fa-fw fa-list"></i>
          <span><?php echo $oDB->lang('LabelList','LabelList')?></span></a>
      </li>
      <!-- Nav Item - Dashboard -->
      <li class="nav-item active">
        <a class="nav-link" href="createpattern.php">
          <i class="fas fa-fw fa-sliders-h"></i>
          <span><?php echo $oDB->lang('SetStationLabelRules','Thiết lập mẫu tem')?></span></a>
      </li>
      <!-- Nav Item - Dashboard -->
      <li class="nav-item active">
        <a class="nav-link" href="ficreatelabel.php">
          <i class="fas fa-fw fa-sliders-h"></i>
          <span><?php echo $oDB->lang('FiDownloadLabel','Tải về label cho FI ')?></span></a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="routelist.php">
          <i class="fas fa-fw fa-chart-bar"></i>
          <span><?php echo $oDB->lang('RoutingList')?></span></a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="LabelHistory.php">
          <i class="fas fa-fw fa-list"></i>
          <span><?php echo $oDB->lang('LabelHistory')?></span></a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="LabelHistoryfi.php">
          <i class="fas fa-fw fa-list"></i>
          <span><?php echo $oDB->lang('LabelHistoryFi')?></span></a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="labelhistoryfi2.php">
          <i class="fas fa-fw fa-chart-bar"></i>
          <span><?php echo $oDB->lang('LabelHistoryFi2')?></span></a>
      </li>

      
      <li class="nav-item active">
        <a class="nav-link" href="labeltrace.php">
          <i class="fas fa-fw fa-search"></i>
          <span><?php echo $oDB->lang('Search')?></span></a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="report.php">
          <i class="fas fa-fw fa-chart-bar"></i>
          <span><?php echo $oDB->lang('Report')?></span></a>
      </li>

      <li class="nav-item active">
        <a class="nav-link" href="labelhistorynoneunique.php">
          <i class="fas fa-fw fa-chart-bar"></i>
          <span><?php echo $oDB->lang('ReportFi2')?></span></a>
      </li>

     



 


      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">
      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>
    </ul>
    <!-- End of Sidebar -->
