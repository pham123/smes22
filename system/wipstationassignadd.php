<?php
session_start();
ob_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
require('../config.php');
i_func('sdb');
$sDB = new sdb();
//var_dump($_POST);
//exit();
// array(2) { ["userasign"]=> string(1) "3" ["addstation"]=> string(1) "2" }
$wipno = $_POST['no'];
$sql = "INSERT INTO `userassignscm`(`UsersId`, `SupplyChainObjectId`, `UserAssignScmOption`) VALUES (?,?,1)";
$sDB -> query($sql,$_POST['userasign'],$_POST['addstation']);
$sDB ->close();
header('Location:wipstationassign.php');