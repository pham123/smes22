   
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
          <span><?php echo $oDB->lang('Wip Control')?></span></a>
      </li>

      <!-- Divider -->
<?php

$arr = array(
  array('wip.php', 'fas fa-plus-square',$oDB->lang('Add')),
);
echo nav_item($oDB->lang('Stock'),$arr);

$arr = array(
  array('wipstationhis.php', 'fas fa-plus-square',$oDB->lang('StationHistory')),
  array('wipstationcur.php', 'fas fa-plus-square',$oDB->lang('WipCurrentQty')),
);
echo nav_item($oDB->lang('Report'),$arr);

?>
 

      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">
      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>
    </ul>
    <!-- End of Sidebar -->
