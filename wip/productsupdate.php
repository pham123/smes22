<?php
session_start();
ob_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
require('../config.php');
i_func('sdb');
$sDB = new sdb();


$startwip = array("23", "34");
//var_dump($_POST);
//array(3) { ["ProductsId"]=> string(1) "1" ["Qty"]=> string(5) "10000" ["remark"]=> string(4) "test" }
//SupplyChainObjectId
$sql = "SELECT `CurrentQty` FROM `wipqty` WHERE `SupplyChainObjectId`=? and `ProductsId`=?";
$qty = $sDB -> query($sql,$_POST['SupplyChainObjectId'],$_POST['ProductsId']) ->fetchArray();
if ($_POST['Qty']<=$qty['CurrentQty']) {
    $sql = "UPDATE `wipdetailcurrent` SET `ProductsId`=?,`Quantity`=?,`Remark`=? WHERE `id`=?";
    $sDB -> query($sql,$_POST['ProductsId'],$_POST['Qty'],$_POST['Remark'],$_POST['id']);
}else{
    if (in_array($_POST['SupplyChainObjectId'],$startwip)) {
        $sql = "UPDATE `wipdetailcurrent` SET `ProductsId`=?,`Quantity`=?,`Remark`=? WHERE `id`=?";
        $sDB -> query($sql,$_POST['ProductsId'],$_POST['Qty'],"SPECIAL INOUT : ".$_POST['Remark'],$_POST['id']);
    }else{
        $sql = "UPDATE `wipdetailcurrent` SET `ProductsId`=?,`Quantity`=?,`Remark`=? WHERE `id`=?";
        $msg = "Tồn kho ".$qty['CurrentQty'].", không đủ xuất!";
        $sDB -> query($sql,$_POST['ProductsId'],0,$msg,$_POST['id']);
    }
    
}
$sDB ->close();
header('Location:wip.php');