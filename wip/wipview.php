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

$wipno = $_GET['no'];
$sql = "SELECT * FROM `wiphistory` WHERE `WipNo` = ?";
$wiphis = $sDB->query($sql,$wipno)->fetchAll();

// SELECT `id`, `UsersId`, `wipno`, `fromstation`, `tostation`, `cdate` FROM `wipno` WHERE 1
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        table th,td{
            font-weight: normal;
            font-size: 13px;
            text-align: center;
            vertical-align: middle !important;
            border: 1px solid #333;
            }
        table.noborder th, table.noborder td{
            font-weight: normal;
            font-size: 13px;
            text-align: left;
            vertical-align: middle !important;
            border: none;
            }
        table.items td{
            padding: 15px;
            font-size:16px;
        }
    </style>
    <title>Print stockoutput</title>
  </head>
  <!-- <body onload="window.print()"> -->
  <body>
    <div class="ml-2 mt-2">
        <p>
        <img src="../img/hallalogo.png" alt="" style="width:100px">
    </div>
    <h3 class="text-center mb-4">
        STOCK OUTPUT SHEET<br>
        <em style="font-weight: normal;">Phiếu xuất kho</em>
    </h3>
    <div class="row p-2 mx-auto mb-5" style="width: 1000px;">

        <table class="w-100 noborder">
            <tr>
                <th><strong>FROM(TỪ):</strong></th>
                <td><?php echo $scmstationbykey[$wiphis[0]['SupplyChainObjectId']]['name'] ?></td>
                <th><strong>KHO:</strong></th>
                <td></td>
                <th><strong>BKS:</strong></th>
                <td></td>
            </tr>
            <tr>
                <th><strong>TO(ĐẾN):</strong></th>
                <td><?php echo $scmstationbykey[$wiphis[0]['NextWipStation']]['name'] ?></td>
                <th><strong>NO:</strong></th>
                <td><?php echo $wipno ?></td>
                <th><strong>THỜI GIAN:</strong></th>
                <td><?php echo $wiphis[0]['Cdate'] ?></td>
            </tr>
            <tr>
                <th><strong>DELIVERY DATE(NGÀY GIAO HÀNG):</strong></th>
                <td><?php echo date("d-m-Y",strtotime($wiphis[0]['Cdate'])) ?></td>
                <th><strong>MODEL:</strong></th>
                <td></td>
                <th><strong>NGƯỜI LẬP:</strong></th>
                <td><?php 
                $sql = "SELECT `UsersFullName` FROM `users` WHERE `UsersId`=?";
                $user = $sDB->query($sql,$wiphis[0]['UserId'])->fetchArray();
                echo $user['UsersFullName'];
                ?></td>
            </tr>
        </table>
        <!-- SELECT `id`, `WipNo`, `SupplyChainObjectId`, `UserId`, `ProductsId`, `Quantity`, `NextWipStation`, `seq`, `Remark`, `Cdate` FROM `wiphistory` WHERE 1 -->
        </div>
        <div class="row px-4">
        <table class="items" style="margin-left: auto; margin-right: auto; width: 1000px;">
            <thead>
                <tr>
                    <th><strong>NO</strong></th>
                    <th style="min-width: 120px;"><strong>Part Name</strong></th>
                    <th style="min-width: 150px;"><strong>Part No</strong></th>
                    <!-- <th><strong>Process</strong></th>
                    <th><strong>W/o</strong></th>
                    <th><strong>Mold</strong></th>
                    <th><strong>Cart'Qty</strong></th>
                    <th><strong>Unit</strong></th> -->
                    <th><strong>Qty</strong></th>
                    <th><strong>Unit</strong></th>
                    <th><strong>Remark</strong></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalQty = 0;
                $totalCartQty = 0;
                foreach($wiphis as $k=>$item)
                {
                  $sql = "SELECT `ProductsName`,`ProductsNumber` FROM `products` WHERE `ProductsId` = ?";
                  $product = $sDB->query($sql,$item['ProductsId'])->fetchArray();
                ?>
                <tr>
                    <td><?php echo $k+1 ?></td>
                    <td><?php echo $product['ProductsNumber'] ?></td>
                    <td><?php echo $product['ProductsName'] ?></td>
                    <!-- <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td> -->
                    <td><?php echo $item['Quantity'] ?></td>
                    <td>EA</td>
                    <td><?php echo $item['Remark'] ?></td>
                </tr>
                <?php
                }
                    $numOfItems = count($wiphis);
                    for($i = $numOfItems; $i< 12; $i++)
                    {
                ?>
                <tr>
                    <td><?php echo $i+1?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <!-- <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td> -->
                </tr>
                <?php
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th colspan="2"><strong>SUM</strong></th>
                    <th colspan="3"><?php echo $totalQty?></th>

                </tr>
            </tfoot>
        </table>
    </div>
        <div class="row py-5 px-3">
            <div class="col-12">
                <table class="table-sm" style="width: 1000px;margin-left: auto; margin-right: auto;">
                    <thead>
                        <tr>
                            <th style="width: 200px;"><strong>Delivered by</strong><br>(Người giao hàng)</th>
                            <th style="width: 200px;"><strong>Checked by</strong><br>(Xác nhận)</th>
                            <th style="width: 200px;"><strong>Inspect by</strong><br>(Người kiểm tra)</th>
                            <th style="width: 200px;"><strong>Guard</strong><br>(Bảo vệ)</th>
                            <th style="width: 200px;"><strong>Received by</strong><br>(Người nhận hàng)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="height: 120px;">
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

  </body>
</html>