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

$sql = "SELECT * FROM `products` where `ProductsOption` <> 4";
$products= $sDB->query($sql)->fetchAll();
$productsbykey = array();
foreach ($products as $key => $value) {
  $productsbykey[$value['ProductsId']]['name'] = $value['ProductsName'];
  $productsbykey[$value['ProductsId']]['code'] = $value['ProductsNumber'];
}


if (isset($_POST['SupplyChainObjectId'])&&$_POST['SupplyChainObjectId']!='') {
$_SESSION['wip']['SupplyChainObjectId'] = $_POST['SupplyChainObjectId'];
$_SESSION['wip']['NextWipStation'] = $_POST['NextWipStation'];
}

if(isset($_SESSION['wip']['SupplyChainObjectId'])){
// Kiem tra xem tram wip nay co don nao dang lam do dang hay khong co wip nao khong
$sql = "SELECT * FROM `wipdetailcurrent` WHERE `SupplyChainObjectId` = ?";
$wipstationcurrent = $sDB->query($sql,$_SESSION['wip']['SupplyChainObjectId'])->fetchAll();

if (isset($wipstationcurrent[0])) {
  if($wipstationcurrent[0]['UserId']==$_SESSION['user']){

  }else{

  }
}else{

  #lay ve gia tri o wipcount
  $key  = "W".date("ymd",strtotime('now'));
  $sql = "SELECT * FROM `wipcount` WHERE `id` = 1";
  $currentcount = $sDB->query($sql)->fetchArray();

  if($currentcount['WipCountDate']==$key){
    $WipCountValue = $currentcount['WipCountValue'] + 1;
    $sql = "UPDATE `wipcount` SET `WipCountValue`=`WipCountValue` + 1 WHERE `id` = 1";
    $sDB->query($sql);
  }else{
    $sql = "UPDATE `wipcount` SET `WipCountDate`='".$key."',`WipCountValue`= 1 WHERE `id` = 1";
    $WipCountValue = 1;
    $sDB->query($sql);
  }
  #neu ko co thi phai insert vao wipcount (hoac update)
  #tao ra wip no moi
  $wipno = $key.sprintf("%07d", $WipCountValue);

  $sql = "INSERT INTO `wipdetailcurrent`(`WipNo`, `SupplyChainObjectId`, `UserId`,`seq`,`NextWipStation`) VALUES (?,?,?,1,?)";
  $sDB->query($sql,$wipno,$_SESSION['wip']['SupplyChainObjectId'],$user->id,$_SESSION['wip']['NextWipStation']);
  #
  $sql = "SELECT * FROM `wipdetailcurrent` WHERE `SupplyChainObjectId` = ?";
  $wipstationcurrent = $sDB->query($sql,$_SESSION['wip']['SupplyChainObjectId'])->fetchAll();
}
//neu co thi lay ve thong tin

//neu khong thi 
}
?>

<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">

  <?php //require('sidebar.php') ?>

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
                <label>Chọn trạm WIP</label>
                <select name="SupplyChainObjectId" id="" class='selectpicker show-tick' data-live-search="true" data-style="btn-info" data-width="100%" required>
                    <option value="">select</option>
                    <?php 
                    //$model = $newDB->get('userassignscm');
                    foreach ($wipstation as $key => $value) {
                      echo "<option value='".$value['SupplyChainObjectId']."'>".$value['SupplyChainObjectShortName']."</option>";
                    }
                    ?>
                    
                  </select>
              </div>
              <div class="col-md">
                <label>Chọn trạm đích</label>
                <select name="NextWipStation" id="" class='selectpicker show-tick' data-live-search="true" data-style="btn-info" data-width="100%" required>
                    <option value="">select</option>
                    <?php 
                    //$model = $newDB->get('userassignscm');
                    $scmstationbyid= array();
                    foreach ($scmstation as $key => $value) {
                      $scmstationbyid[$value['SupplyChainObjectId']]['Name'] = $value['SupplyChainObjectName'];
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

          <br>

          <?php
          if(isset($_SESSION['wip']['SupplyChainObjectId'])&&$wipstationcurrent[0]['UserId']==$user->id){
            ?>
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <td>WIP NO : <?php echo $wipstationcurrent[0]['WipNo'] ?> <a href="delete.php?no=<?php echo $wipstationcurrent[0]['WipNo'] ?>" class="btn btn-outline-primary" role="button" aria-pressed="true">Xóa WIP</a></td>
                </td>

                </tr>
                <tr>
                  <td>
                    <p> From : <?php echo $scmstationbyid[$wipstationcurrent[0]['SupplyChainObjectId']]['Name'] ?> </p>
                    <p> To :  <?php echo $scmstationbyid[$wipstationcurrent[0]['NextWipStation']]['Name'] ?> </p>
                  </td>
                </tr>

                <tr>
                  <td>
                    
                  </td>
                </tr>
                <tr>
                  <th>Sản phẩm/Số lượng/Ghi chú</th>
                </tr>

                
                  <?php
                    foreach ($wipstationcurrent as $key => $value) {
                     ?>
                     <tr>
                     <td colspan = ''>
                     <form action="productsupdate.php" method="post">
                     <div class='row mx-auto'>
                     <div class="col-md">
                    <?php
                    if ($value['ProductsId']!=0) {
                    echo $productsbykey[$value['ProductsId']]['code']."-".$productsbykey[$value['ProductsId']]['name'];
                    echo "<input type='hidden' name='ProductsId' id='' class='form-control' value = '".$value['ProductsId']."'>";
                    }else{
                    ?>
                    <select name="ProductsId" id="" class='selectpicker show-tick' data-live-search="true" data-style="btn-info" data-width="100%" required>
                    <option value="">select</option>
                    <?php 
                    //$model = $newDB->get('userassignscm');
                    foreach ($products as $key2 => $value2) {
                      echo "<option value='".$value2['ProductsId']."'>".$value2['ProductsNumber']."-".$value2['ProductsName']."</option>";
                    }
                    
                    echo "</select>";
                    }
                      echo "</div>";
                      echo "<div class='col-md'><input type='number' name='Qty' id='' class='form-control' value = '".$value['Quantity']."'></div>";
                      if ($value['Quantity']>0) {
                        $push = 1;
                      }else{
                        $push = 0;
                      }
                     echo "<div class='col-md'><input type='text' name='Remark' id='' class='form-control' value = '".$value['Remark']."'></div>";
                     echo "<input type='hidden' name='id' id='' class='form-control' value = '".$value['id']."'>";
                     echo "<input type='hidden' name='SupplyChainObjectId' id='' class='form-control' value = '".$value['SupplyChainObjectId']."'>";
                     echo "<div class='col-md'><button type='submit' class='btn btn-outline-success' value = 'update_".$value['id']."'>Cập nhật</button></div>";
                     echo "<div class='col-md'><a href='deleteproduct.php?id=".$value['id']."' class='btn btn-outline-primary' role='button' aria-pressed='true'>Xóa</a></div>";
                     echo "</form>";
                    ?>
                    </div>
                    </td>
                     <?php
                     echo "</tr>";
                    }
                  ?>
                  
                <tr>
                  <td colspan = '6'>

                  </td>
                </tr>
                  
                <tr>
                  <td>
                    <a href="add.php?no=<?php echo $wipstationcurrent[0]['WipNo'] ?>" class="btn btn-outline-primary" role="button" aria-pressed="true">Thêm sản phẩm</a>
                    <a href="#" class="btn btn-outline-primary" role="button" aria-pressed="true">######</a>
                    <?php
                    if ($push==1) {
                     ?>
                    <a href="push.php?no=<?php echo $wipstationcurrent[0]['WipNo'] ?>" class="btn btn-outline-primary" role="button" aria-pressed="true">Xuất</a>
                     <?php
                    }else{
                      ?>
                    <a href="#" class="btn btn-outline-primary" role="button" aria-pressed="true">Chưa hoàn thiện</a>
                     <?php
                    }
                    ?>
                    
                  </td>
                </tr>


              </tbody>
            </table>
            <?php
            
          }else{
            if(isset($_SESSION['wip']['SupplyChainObjectId'])){
              $sql = "SELECT * FROM `users` WHERE `UsersId` = ?";
              $user = $sDB->query($sql,$wipstationcurrent[0]['UserId'])->fetchArray();
              echo "<h1>".$scmstationbyid[$_SESSION['wip']['SupplyChainObjectId']]['Name']." đang được sử dụng bởi ".$user['UsersFullName'].", liên hệ để hoàn thành WIP hiện tại hoặc xóa bỏ</h1>";
            }else{
             
            }
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
