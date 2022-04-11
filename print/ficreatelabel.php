<?php
session_start();
ob_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
require('../config.php');
require('../function/db_lib.php');
require('../function/function.php');
$user = New Users();
$user->set($_SESSION[_site_]['userid']);
$user->module = basename(dirname(__FILE__));
check($user->acess());
$pagetitle = $user->module;

require('../views/template-header.php');
require('../function/template.php');
i_func('sdb');
i_func('renderlabel');
$oDB = new db();
$sDB = new sdb();

#station mặc định là FI
$station = 5;
#Mã tem
$sql = "SELECT lp.*,prd.ProductsName,prd.ProductsNumber FROM `labelpattern` lp
inner join products prd on prd.ProductsId = lp.ProductsId
 WHERE lp.`TraceStationId` = ?";
$product = $sDB->query($sql, $station)->fetchAll();

$productbyid = array();
foreach ($product as $key => $value) {
  $productbyid[$value['ProductsId']]['pt']=$value['LabelPatternNew'];
  $productbyid[$value['ProductsId']]['name']=$value['ProductsName'];
  $productbyid[$value['ProductsId']]['no']=$value['ProductsNumber'];
  $_SESSION['EndNum'] = 0;
}

if (isset($_POST['ProductsId'])&&$_POST['ProductsId']!="") {
  //echo $_POST['ProductsId'];
  $_SESSION['ProductsId'] = $_POST['ProductsId'];
  $_SESSION['PrintQty'] = $_POST['PrintQty'];
  $_SESSION['StartNum'] = $_POST['StartNum'];
  $kqar = renderlabel($sDB,$productbyid[$_SESSION['ProductsId']]['pt'],$_SESSION['ProductsId']);
  $_SESSION['lp'] = $kqar['lp'];
  $_SESSION['sq'] = $kqar['sq'];

  $sql = "SELECT * FROM `labelprint` WHERE `LabelPattern` = ?";
  $labelprint= $sDB->query($sql, $_SESSION['lp'])->fetchArray();
  //var_dump($labelprint);
  if (isset($labelprint['CurrentQty'])) {
    $_SESSION['StartNum']=$labelprint['CurrentQty'];
    $_SESSION['EndNum'] = $_SESSION['StartNum'] + $_SESSION['PrintQty'];

    $sql = "UPDATE `labelprint` SET `CurrentQty` = `CurrentQty` + ? WHERE `LabelPattern` = ?";
    $sDB->query($sql, $_SESSION['PrintQty'], $_SESSION['lp']);

  }else{
    if ($_SESSION['StartNum']==0) {
      $keys = str_replace(str_repeat("*", $_SESSION['sq']),"", $_SESSION['lp']);
      $sql = "SELECT * FROM `labelhistory` WHERE `ProductsId` = ".$_SESSION['ProductsId']." and `TraceStationId`= 5 and `LabelHistoryLabelValue` like '".$keys."%' order by `LabelHistoryLabelValue` desc LIMIT 1";
      $labelhis= $sDB->query($sql)->fetchArray();
      // var_dump($labelhis);
      if (isset($labelhis["LabelHistoryLabelValue"])) {
        $_SESSION['lastlabel"'] = $labelhis["LabelHistoryLabelValue"];
      }else{
        $_SESSION['lastlabel"'] = "";
      }
      
    }else{
      $sql = "INSERT INTO `labelprint`(`LabelPattern`, `CurrentQty`) VALUES (?,?)";
      $sDB->query($sql, $_SESSION['lp'],$_SESSION['StartNum']);

      $_SESSION['EndNum'] = $_SESSION['StartNum'] + $_SESSION['PrintQty'];

      $sql = "UPDATE `labelprint` SET `CurrentQty` = `CurrentQty` + ? WHERE `LabelPattern` = ?";
      $sDB->query($sql, $_SESSION['PrintQty'], $_SESSION['lp']);
    }

  }


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

        <!-- Begin Page Content -->
        <div class="container-fluid">
        <!-- /.container-fluid -->
        <div>
          <!-- cho nay tao form -->
          <form action="" method="post" enctype="multipart/form-data">
          <div class="row">
          <div class="col-md-4">
                <p><?php echo $oDB->lang('Products') ?></p>
                <select name="ProductsId" id="" class='selectpicker show-tick' data-live-search="true" data-style="btn-info" data-width="100%">
                  <?php 
                  foreach ($product as $key => $value) {
                    if (isset($_SESSION['ProductsId'])) {
                      $select = ($_SESSION['ProductsId']==$value['ProductsId']) ? 'selected' : '' ;
                    }else{
                      $select = "";
                    }
                    echo "<option value='".$value['ProductsId']."' ".$select.">".$value['ProductsNumber']."-".$value['ProductsName']."-".$value['LabelPatternValue']."</option>";
                  }
                  ?>
          </select>
          </div>

          <div class="col-md-2">
              <p><?php echo $oDB->lang('PrintQuantity') ?></p>
              <input type="number" name="PrintQty" id="" class='form-control' value = '0'>
          </div>

          
          <div class="col-md-2">
              <p><?php echo $oDB->lang('StartSerialNo') ?></p>
              <input type="number" name="StartNum" id="" class='form-control' value = '0'>
          </div>

          <div class="col-md-2">
              <p>&nbsp;</p>
              <button type="submit" class='btn btn-primary btn-block'><?php echo $oDB->lang('Submit') ?></button>
          </div>


          </div>
          </form>
        <div class="row">
            <?php
              if (isset($_POST['ProductsId'])&&$_POST['ProductsId']!="") {
                //echo $_POST['ProductsId'];

                ?>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Mã sản phẩm</th>
                    <th scope="col">Tên sản phẩm</th>
                    <th scope="col">Mẫu Label</th>
                    <th scope="col">Số lượng in</th>
                    <th scope="col">Giá trị bắt đầu</th>
                    <th scope="col">Giá trị in gần nhất</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row"><?php echo $productbyid[$_SESSION['ProductsId']]['no']?></th>
                    <td><?php echo $productbyid[$_SESSION['ProductsId']]['name']?></td>
                    <td><?php echo str_replace(str_repeat("*", $_SESSION['sq']),sprintf("%0".$_SESSION['sq']."d", 1), $_SESSION['lp'])?></td>
                    <td><?php echo $_SESSION['PrintQty']?></td>
                    <td><?php echo $_SESSION['StartNum']?></td>
                    <td><?php echo $_SESSION['lastlabel"']?></td>
                   
                  </tr>

                </tbody>
              </table>
                <?php
              }

              
              
            ?>

              
        </div>

<br>
        </div>

        <div class="table-responsive">
            <?php
             if (isset($_SESSION['ProductsId'])) {
              echo "<table class='table table-bordered' id='datatablenotpage' width='100%' cellspacing='0'>";
              echo "<thead>";
              echo "<tr>";
                  echo "<th>No</th>";
                  echo "<th>PART NO</th>";
                  echo "<th>Under bar</th>";
                  echo "<th>Mold no</th>";
                  echo "<th>Seq</th>";
                  echo "<th>MATRIX CODE</th>";
              echo "</tr>";
              echo "</thead>";
      
      
              echo "<tbody>";
              $index = 1;
              for ($i= $_SESSION['StartNum']; $i < $_SESSION['EndNum'] ; $i++) { 
                # code...
                  echo "<tr>";
                  echo "<td>".$index++."</td>";
                  echo "<td>".$productbyid[$_SESSION['ProductsId']]['no']."</td>";
                  echo "<td></td>";
                  echo "<td></td>";
                  echo "<td>".sprintf("%0".$_SESSION['sq']."d", $i)."</td>";
                  echo "<td>".str_replace(str_repeat("*", $_SESSION['sq']),sprintf("%0".$_SESSION['sq']."d", $i), $_SESSION['lp'])."</td>";
                  echo "</tr>";
              }
      
              echo "</tbody>";
      
              echo "</table>";
             }
                // var_dump($product);
                // foreach ($product as $key => $value) {
                //     echo "<p>";
                //     echo $value['ProductsNumber'];
                //     echo " ==> | ";
                //     echo $value['LabelPatternNew'];
                //     echo " ==> | ";
                //     $kqar = renderlabel($sDB,$value['LabelPatternNew'],$value['ProductsId']);
                //     $lp = $kqar['lp'];
                //     $sqvalue = $kqar['sq'];
                //     echo str_replace(str_repeat("*", $sqvalue),sprintf("%0".$sqvalue."d", 99),$lp);
                //     echo "</p>";
                // }
            ?>
        </div>
        </div>
        <!-- /.container-fluid -->

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

  <script>
    $(function () {
      $('selectpicker').selectpicker();
    });
  </script>

</body>

</html>

