<?php
session_start();
ob_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
require('../config.php');
i_func('sdb');
$sDB = new sdb();
$wipno = $_GET['no'];
$sql = "DELETE FROM `wipdetailcurrent` WHERE `WipNo` = ?";
$sDB -> query($sql,$wipno);
$sDB ->close();
unset($_SESSION['wip']['SupplyChainObjectId']);
header('Location:wip.php');