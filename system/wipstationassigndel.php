<?php
session_start();
ob_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
require('../config.php');
i_func('sdb');
$sDB = new sdb();
//var_dump($_POST);
//var_dump($_POST);
//exit();
// array(2) { ["userasign"]=> string(1) "3" ["addstation"]=> string(1) "2" }
$sql = "DELETE FROM `userassignscm` WHERE `id` = ?";
$sDB -> query($sql,$_GET['del']);
$sDB ->close();
header('Location:wipstationassign.php');