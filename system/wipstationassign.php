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

$sql = "SELECT * FROM `supplychainobject`";
$scmstation= $sDB->query($sql)->fetchAll();

$sql = "SELECT * FROM `users`";
$users = $sDB->query($sql)->fetchAll();


if (isset($_POST['UsersId'])&&$_POST['UsersId']!='') {
    $_SESSION['wip']['UsersAsignId'] = $_POST['UsersId'];
    }

if (isset($_SESSION['wip']['UsersAsignId'])) {
    $sql = "SELECT * FROM `userassignscm` uas
            inner join `supplychainobject` scm on scm.SupplyChainObjectId = uas.SupplyChainObjectId
            where uas.UsersId = ?";
            $wipstation = $sDB->query($sql,$_SESSION['wip']['UsersAsignId'])->fetchAll();
}
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
          <form action="" method="post">
          <div class="row mx-auto">
              <div class="col-md">
                <label>User</label>
                <select name="UsersId" id="" class='selectpicker show-tick' data-live-search="true" data-style="btn-info" data-width="100%" required>
                    <option value="">select</option>
                    <?php 

                    foreach ($users as $key => $value) {
                        if(isset( $_SESSION['wip']['UsersAsignId'])&& $_SESSION['wip']['UsersAsignId']==$value['UsersId']){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                      echo "<option value='".$value['UsersId']."' ".$selected.">".$value['UsersName']."</option>";
                    }
                    ?>
                    
                  </select>
              </div>
              <div class="col-md">
                <label>&nbsp;</label>
                <input type="submit" class="btn btn-primary w-50 mx-auto btn-block" value="submit">
              </div>
            </div>
          </form>
          <br>
          <?php
          
          if (isset($_SESSION['wip']['UsersAsignId'])) {
            $sql = "SELECT * FROM `users` WHERE `UsersId` = ?";
            $user = $sDB->query($sql,$_SESSION['wip']['UsersAsignId'])->fetchArray();
            echo "<h1>".$user['UsersFullName']."</h1>";
                    // $wipstation = $sDB->query($sql,$_SESSION['wip']['UsersAsignId'])->fetchAll();
            echo "<div>";
            foreach ($wipstation as $key => $value) {
                echo "<span><a href='wipstationassigndel.php?del=".$value['id']."' class='btn btn-outline-danger' role='button' aria-pressed='true'>".$value['SupplyChainObjectName']."</a></span>";
            }
            echo "</div>";

            ?>
            <br>
            <form action="wipstationassignadd.php" method="post">
                <div class="row mx-auto">
                <div class="col-md">
                    <label>Add more station</label>
                    <input type="hidden" name="userasign" value="<?php echo $_SESSION['wip']['UsersAsignId'] ?>">
                    <select name="addstation" id="" class='selectpicker show-tick' data-live-search="true" data-style="btn-info" data-width="100%" required>
                        <option value="">select</option>
                        <?php 

                        foreach ($scmstation as $key => $value) {
                            echo "<option value='".$value['SupplyChainObjectId']."'>".$value['SupplyChainObjectShortName']."</option>";
                        }
                        ?>
                        
                    </select>
                </div>
                <div class="col-md">
                    <label>&nbsp;</label>
                    <input type="submit" class="btn btn-primary w-50 mx-auto btn-block" value="submit">
                </div>
                </div>
            </form>

            <?php
        }


          ?>

  

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
