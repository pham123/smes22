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
$oDB = new db();

// $actionar = (array_keys($_GET));
// $actionkey = (isset($actionar[0])) ? $actionar[0] : 'content' ;

// $action =  (explode("_",$actionkey));

// $option = $action[0];
// $target = (isset($action[1])) ? ucfirst($action[1]) : 'Company' ;
// $id = (isset($action[2])) ? $action[2] : 1 ;

// if (file_exists('../querry/'.$option.'_'.$target.'.php')) {
//   require('../querry/'.$option.'_'.$target.'.php');
// }else {
//   $sql = "Select * from ".$target;
// }




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
        <!-- <form action="labeltrace.php" >
          <input type="text" name="code" id="" class='form-control' autofocus>
        </form> -->

        <?php 
        if (isset($_GET['code'])&&safe($_GET['code'])!="") {
          # code...
          $code = safe($_GET['code']);
        }else{
          exit();
        }
        $precode = $oDB->sl_one('LabelList',"LabelListValue ='".$code."'");

        $list = array();
        $check = $precode['LabelListId'];
        $list[] =  $precode['LabelListId'];
        // echo "<br>";
        for ($i=0; $i < 10; $i++) { 
          $precode2 = $oDB->sl_one('LabelList',"LabelListId ='".$check."'");
          if (!isset($precode2['LabelListMotherId'])) {
          break;
          }
          $check = $precode2['LabelListMotherId'];
          $list[] = $check;
          // echo "<br>";
        }
        //var_dump($list);
        $text = implode(',',$list);
        //echo $text;

        $sql = "select lh.*,prd.ProductsName,prd.ProductsNumber,ts.TraceStationName,lbl.LabelListId as DcLabelId from LabelHistory lh
        inner join LabelList lbl on lbl.LabelListValue = lh.LabelHistoryLabelValue
        inner join TraceStation ts on ts.TraceStationId = lh.TraceStationId
        inner join Products prd on prd.ProductsId = lbl.ProductsId
        WHERE lbl.LabelListId in (".$text.")
        ORDER BY lh.LabelHistoryCreateDate DESC";

        $result = $oDB->fetchAll($sql);
        $lastarr = end($result);
        //var_dump(end($result));
        $dclabel = $lastarr['DcLabelId'];
        $keyarr[]=$dclabel;

        $sql = "SELECT `LabelListId` FROM `labellist` WHERE `LabelListMotherId` =".$dclabel;

        $list1 = $oDB->fetchAll($sql);
        foreach ($list1 as $key => $value) {
          $keyarr[] = $value['LabelListId'];  
        }

        $sql = "SELECT `LabelListId` FROM `labellist` 
                WHERE `LabelListMotherId` 
                in (SELECT `LabelListId` FROM `labellist` WHERE `LabelListMotherId` = ".$dclabel.")";

        $list2 = $oDB->fetchAll($sql);

        foreach ($list2 as $key => $value) {
          $keyarr[] = $value['LabelListId'];  
        }
        $text =  implode(', ',$keyarr);
        // var_dump($keyarr);

        $sql = "select lh.*,prd.ProductsName,prd.ProductsNumber,ts.TraceStationName,lbl.LabelListId as DcLabelId from LabelHistory lh
        inner join LabelList lbl on lbl.LabelListValue = lh.LabelHistoryLabelValue
        inner join TraceStation ts on ts.TraceStationId = lh.TraceStationId
        inner join Products prd on prd.ProductsId = lbl.ProductsId
        WHERE lbl.LabelListId in (".$text.")
        ORDER BY lh.LabelHistoryCreateDate DESC";

        $result = $oDB->fetchAll($sql);

        $sql = "select 
        lh.TraceStationId,
        sum(lh.LabelHistoryQuantityOk) as qtyOk,
        sum(lh.LabelHistoryQuantityNG) as qtyNg,
        prd.ProductsName,prd.ProductsNumber,
        ts.TraceStationName,
        ts.TraceStationPosition 
        from LabelHistory lh 
        inner join LabelList lbl on lbl.LabelListValue = lh.LabelHistoryLabelValue 
        inner join TraceStation ts on ts.TraceStationId = lh.TraceStationId 
        inner join Products prd on prd.ProductsId = lbl.ProductsId 
        WHERE lbl.LabelListId in (".$text.") 
        Group by lh.TraceStationId,prd.ProductsName,prd.ProductsNumber,ts.TraceStationName,ts.TraceStationPosition
        Order By ts.TraceStationPosition";
        $total = $oDB->fetchAll($sql);
        ?>

        <div class="table-responsive">

        <?php
        echo "<table class='table table-bordered' id='' width='100%' cellspacing='0'>";
        echo "<thead>";
        echo "<tr style='text-align:center;background-color:#DDDEE0'>";
            echo "<th rowspan='2' style='vertical-align:middle;'>".$oDB->lang('Index')."</th>";
            echo "<th rowspan='2' style='vertical-align:middle;'>".$oDB->lang('Station')."</th>";
            echo "<th rowspan='2' style='vertical-align:middle;'>".$oDB->lang('ProductName')."</th>";
            echo "<th rowspan='2' style='vertical-align:middle;'>".$oDB->lang('ProductNumber')."</th>";
            echo "<th colspan='3' style='vertical-align:middle;'>".$oDB->lang('Quantity')."</th>";

        echo "</tr>";
        

        echo "<tr style='text-align:center;background-color:#DDDEE0'>";
        echo "<th style='vertical-align:middle;'>".$oDB->lang('Ok')."</th>";
        echo "<th style='vertical-align:middle;'>".$oDB->lang('Ng')."</th>";
        echo "<th style='vertical-align:middle;'>".$oDB->lang('Wip')."</th>";
    echo "</tr>";
    echo "</thead>";


        echo "<tbody>";
        $wip = 0;
        $lastqty = 0;
        foreach ($total as $key => $value) {
            echo "<tr style='text-align:center;'>";
            echo "<td>".($key+1)."</td>";
            echo "<td>".$value['TraceStationName']."</td>";
            echo "<td>".$value['ProductsName']."</td>";
            echo "<td>".$value['ProductsNumber']."</td>";
            echo "<td style='background-color:green;'>".$value['qtyOk']."</td>";
            echo "<td style='background-color:Red;'>".$value['qtyNg']."</td>";
            if ($lastqty==0) {
              $wip = 0;
            }else{
              $wip = $lastqty - $value['qtyOk'] - $value['qtyNg'];
            }
            
            echo "<td style='background-color:yellow;'>".$wip."</td>";
            echo "</tr>";
            
            $lastqty =  $value['qtyOk'];
        }

        echo "</tbody>";

        echo "</table>";

          ?>

        </div>

        <div class="table-responsive">
        <?php
        echo "<table class='table table-bordered' id='dataTable' width='100%' cellspacing='0'>";
        echo "<thead>";
        echo "<tr>";
            echo "<th>".$oDB->lang('Index')."</th>";
            echo "<th>".$oDB->lang('Station')."</th>";
            echo "<th>".$oDB->lang('ProductName')."</th>";
            echo "<th>".$oDB->lang('ProductNumber')."</th>";
            echo "<th>".$oDB->lang('Ok')."</th>";
            echo "<th>".$oDB->lang('Ng')."</th>";
            echo "<th>".$oDB->lang('LabelValue')."</th>";
            echo "<th>".$oDB->lang('IssueDate')."</th>";
        echo "</tr>";
        echo "</thead>";


        echo "<tbody>";

        foreach ($result as $key => $value) {
            echo "<tr>";
            echo "<td>".($key+1)."</td>";
            if ($value['TraceStationId']==1) {
              echo "<td><a href='labeldetail2.php?code=".$value['LabelHistoryLabelValue']."'>".$value['TraceStationName']."</a></td>";
            }else{
              echo "<td>".$value['TraceStationName']."</td>";
            }
            
            echo "<td>".$value['ProductsName']."</td>";
            echo "<td>".$value['ProductsNumber']."</td>";
            echo "<td style='background-color:#73E700;'>".$value['LabelHistoryQuantityOk']."</td>";
            echo "<td style='background-color:red;'>".$value['LabelHistoryQuantityNg']."</td>";
            echo "<td>".$value['LabelHistoryLabelValue']."</td>";
            echo "<td>".$value['LabelHistoryCreateDate']."</td>";
            echo "</tr>";
        }

        echo "</tbody>";

        echo "</table>";

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
