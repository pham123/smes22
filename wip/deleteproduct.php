<?php
session_start();
ob_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
require('../config.php');
i_func('sdb');
$sDB = new sdb();
$wipno = $_GET['id'];
$sql = "DELETE FROM `wipdetailcurrent` WHERE `id` = ?";
$sDB -> query($sql,$wipno);
$sDB ->close();
header('Location:wip.php');