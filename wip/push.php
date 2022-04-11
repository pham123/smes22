<?php
session_start();
ob_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
require('../config.php');
i_func('sdb');
$sDB = new sdb();
$wipno = $_GET['no'];
#lấy về thông tin
$sql = "SELECT * FROM `wipdetailcurrent` WHERE `WipNo` = ?";
$wip = $sDB->query($sql,$wipno)->fetchAll();

$fromstation = $wip[0]['SupplyChainObjectId'];
$tostation = $wip[0]['NextWipStation'];
$userid = $wip[0]['UserId'];



#update vào bang wipno
// INSERT INTO `wipno`(`id`, `UsersId`, `wipno`, `fromstation`, `tostation`, `cdate`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]','[value-6]')

$sql = "INSERT INTO `wipno`(`UsersId`, `wipno`, `fromstation`, `tostation`) VALUES (?,?,?,?)";
$sDB->query($sql,$userid,$wipno,$fromstation,$tostation);

#update vao bang wiphistory
// UPDATE `wiphistory` SET `id`='[value-1]',`WipNo`='[value-2]',`SupplyChainObjectId`='[value-3]',`UserId`='[value-4]',`ProductsId`='[value-5]',`Quantity`='[value-6]',`NextWipStation`='[value-7]',`seq`='[value-8]',`Remark`='[value-9]',`Cdate`='[value-10]' WHERE 1

$sql = "INSERT INTO `wiphistory`(`WipNo`, `SupplyChainObjectId`, `UserId`, `ProductsId`, `Quantity`, `NextWipStation`, `seq`, `Remark`)
SELECT `WipNo`, `SupplyChainObjectId`, `UserId`, `ProductsId`, `Quantity`, `NextWipStation`, `seq`, `Remark` FROM `wipdetailcurrent`
WHERE `WipNo` = ?";
$sDB->query($sql,$wipno);
#kiem tra thong tin trong bang wipstorage
// INSERT INTO `wipqty`(`id`, `SupplyChainObjectId`, `ProductsId`, `CurrentQty`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]')

foreach ($wip as $key => $value) {
    $fromstation = $value['SupplyChainObjectId'];
    $tostation = $value['NextWipStation'];
    $product = $value['ProductsId'];
    $qty = $value['Quantity'];
#xu ly tram wip 
    $sql = "SELECT * FROM `wipqty` WHERE `SupplyChainObjectId` = ? and `ProductsId` = ?";
    $row = $sDB->query($sql,$fromstation,$product);
    if($row->numRows()==1){
        $sql = "UPDATE `wipqty` SET `CurrentQty`=`CurrentQty` - ?  WHERE `SupplyChainObjectId` = ? and `ProductsId` = ?";
        $sDB->query($sql,$qty,$fromstation,$product);
    }else{
        $sql = "INSERT INTO `wipqty`(`SupplyChainObjectId`, `ProductsId`, `CurrentQty`) VALUES (?,?,?)";
        $newqty = -$qty;
        $sDB->query($sql,$fromstation,$product,$newqty);
    }

    $sql = "SELECT * FROM `wipqty` WHERE `SupplyChainObjectId` = ? and `ProductsId` = ?";
    $row = $sDB->query($sql,$tostation,$product);
    if($row->numRows()==1){
        $sql = "UPDATE `wipqty` SET `CurrentQty`=`CurrentQty` + ?  WHERE `SupplyChainObjectId` = ? and `ProductsId` = ?";
        $sDB->query($sql,$qty,$tostation,$product);
    }else{
        $sql = "INSERT INTO `wipqty`(`SupplyChainObjectId`, `ProductsId`, `CurrentQty`) VALUES (?,?,?)";
        $sDB->query($sql,$tostation,$product,$qty);
    }

    $cdate  = date("Y-m-d",strtotime('now'));
    $sql = "SELECT * FROM `wipqtydaily` WHERE `Cdate` = ? and `SupplyChainObjectId` = ? and `ProductsId` = ?";
    $row = $sDB->query($sql,$cdate,$fromstation,$product);
    if($row->numRows()==1){
        $sql = "UPDATE `wipqtydaily` SET `CurrentQty`=`CurrentQty` - ?  WHERE `Cdate` = ? and `SupplyChainObjectId` = ? and `ProductsId` = ?";
        $sDB->query($sql,$qty,$cdate,$fromstation,$product);
    }else{
        $sql = "INSERT INTO `wipqtydaily`(`Cdate`,`SupplyChainObjectId`, `ProductsId`, `CurrentQty`) VALUES (?,?,?,?)";
        $newqty = -$qty;
        $sDB->query($sql,$cdate,$fromstation,$product,$newqty);
    }

    $sql = "SELECT * FROM `wipqtydaily` WHERE `Cdate` = ? and `SupplyChainObjectId` = ? and `ProductsId` = ?";
    $row = $sDB->query($sql,$cdate,$tostation,$product);
    if($row->numRows()==1){
        $sql = "UPDATE `wipqtydaily` SET `CurrentQty`=`CurrentQty` + ?  WHERE `Cdate` = ? and `SupplyChainObjectId` = ? and `ProductsId` = ?";
        $sDB->query($sql,$qty,$cdate,$tostation,$product);
    }else{
        $sql = "INSERT INTO `wipqtydaily`(`Cdate`,`SupplyChainObjectId`, `ProductsId`, `CurrentQty`) VALUES (?,?,?,?)";
        $newqty = $qty;
        $sDB->query($sql,$cdate,$tostation,$product,$qty);
    }
}

#kiem tra thong tin trong bang wipdaily
// INSERT INTO `wipqty`(`id`, `Cdate`, `SupplyChainObjectId`, `ProductsId`, `CurrentQty`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]')

#kiem tra thong tin trong bang wip
$sql = "DELETE FROM `wipdetailcurrent` WHERE `WipNo` = ?";
$sDB -> query($sql,$wipno);
$sDB ->close();
unset($_SESSION['wip']['SupplyChainObjectId']);
header('Location:index.php');