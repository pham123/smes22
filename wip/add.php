<?php
session_start();
ob_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
require('../config.php');
i_func('sdb');
$sDB = new sdb();
$wipno = $_GET['no'];
$sql = "SELECT * FROM `wipdetailcurrent` WHERE `WipNo` = ? order by seq desc LIMIT 1";
$wip = $sDB->query($sql,$wipno)->fetchArray();
//var_dump($wip) ; 
$seq = $wip['seq'] + 1;
$sql = "INSERT INTO `wipdetailcurrent`(`WipNo`, `SupplyChainObjectId`, `UserId`,`seq`,`NextWipStation`) VALUES (?,?,?,?,?)";
$sDB->query($sql,$wipno,$wip['SupplyChainObjectId'],$wip['UserId'],$seq,$wip['NextWipStation']);
// check thong tin tram o day va them vao luon

//
$sDB ->close();
header('Location:wip.php');