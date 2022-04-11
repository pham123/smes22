<?php
session_start();
ob_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
require('../config.php');
require('../function/db_lib.php');
require('../function/MysqliDb.php');
require('../function/function.php');
$user = New Users();
$user->set($_SESSION[_site_]['userid']);
$user->module = basename(dirname(__FILE__));
check($user->acess());
$pagetitle = $user->module;
//var_dump($user);
$page_css='.vs__dropdown-toggle {border: 0px !important;margin-top: -4px;} .vs__selected{white-space: nowrap;max-width: 250px;overflow: hidden;font-size: 14px;}';
// $refresh = 5;
require('../views/template-header.php');
require('../function/template.php');
$oDB = new db();
if(isset($_SESSION[_site_]['userlang'])){
  $oDB->lang = ucfirst($_SESSION[_site_]['userlang']);
}

i_func('sdb');
$sDB = new sdb();

$sql = "SELECT * FROM `userassignscm` uas
inner join `supplychainobject` scm on scm.SupplyChainObjectId = uas.SupplyChainObjectId
where uas.UsersId = ?";
$wipstation = $sDB->query($sql,$user->id)->fetchAll();
$_SESSION['user'] = $user->id;

$sql = "SELECT * FROM `supplychainobject`";
$scmstation= $sDB->query($sql)->fetchAll();
$scmstationbykey = array();
foreach ($scmstation as $key => $value) {
  $scmstationbykey[$value['SupplyChainObjectId']]['name']=$value['SupplyChainObjectName'];
}

$sql = "SELECT * FROM `products` where `ProductsOption` <> 4";
$products= $sDB->query($sql)->fetchAll();
$productsbykey = array();
foreach ($products as $key => $value) {
  $productsbykey[$value['ProductsId']]['name'] = $value['ProductsName'];
  $productsbykey[$value['ProductsId']]['code'] = $value['ProductsNumber'];
}


$sql = "SELECT * FROM `wipno` WHERE `UsersId` = ? order by cdate desc";
$wiplist = $sDB->query($sql,$user->id)->fetchAll();

// SELECT `id`, `UsersId`, `wipno`, `fromstation`, `tostation`, `cdate` FROM `wipno` WHERE 1
?>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">

  <?php require('sidebar.php') ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">
        
        <!-- Topbar -->
        <?php require('navbar.php') ?>
        
        <div class="container-fluid">
        <div class="table-responsive">
            <?php

              echo "<table class='table table-bordered' id='dataTableSort' width='100%' cellspacing='0'>";
              echo "<thead>";
              echo "<tr>";
                  echo "<th>No</th>";
                  echo "<th>From</th>";
                  echo "<th>To</th>";
                  echo "<th>Cdate</th>";
              echo "</tr>";
              echo "</thead>";
      
      
              echo "<tbody>";
              $index = 1;
              foreach ($wiplist as $key => $value) {
                # code...
                  echo "<tr>";
                  echo "<td><a href='wipview.php?no=".$value['wipno']."'>".$value['wipno']."</a></td>";
                  echo "<td>".$scmstationbykey[$value['fromstation']]['name']."</td>";
                  echo "<td>".$scmstationbykey[$value['tostation']]['name']."</td>";
                  echo "<td>".$value['cdate']."</td>";
                  echo "</tr>";
              }
      
              echo "</tbody>";
      
              echo "</table>";
            ?>
        </div>
        </div>
      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Your Website 2019</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="login.html">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <?php require('../views/template-footer.php'); ?>
</body>

</html>
