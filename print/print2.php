<?php
session_start();
ob_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
require('../config.php');
require('../function/db_lib.php');
$label = (isset($_GET['label'])) ? $_GET['label'] : '0' ;
$qty = (isset($_GET['qty'])) ? $_GET['qty'] : '10' ;
$start = (isset($_GET['start'])) ? $_GET['start'] : '0' ;
$cavity = (isset($_GET['cavity'])) ? $_GET['cavity'] : '' ;
// include ('data.php');
//var_dump($_POST);
$oDB = new db();
$id = safe($_POST['id']);
$cavity = safe($_POST['cavity']);
$date = safe($_POST['selectdate']);
$lot = safe($_POST['lot']);
$shift = safe($_POST['shift']);
$quantity = safe($_POST['quantity']);
// $id = 2;
// $cavity = 1;
// $date = date("Y-m-d");
// $lot = 1;
// $shift = 1;
// $quantity = 10;
$today = date("Y-m-d");
$sql = "SELECT COUNT(*) as total From LabelList where ProductsId=".$id." AND date(LabelListCreateDate)= '".$today."' AND LabelListMotherId is Null";
$start = $oDB->fetchOne($sql);
$start['total'];
$startlabel = $start['total'] + 1;
// echo $startlabel;

$Products = new Products();
$Products->get($id);


// find label pattern
$str_pt = '';
$sql1 = "SELECT * from labelpattern where ProductsId=".$id.' order by LabelPatternId desc';
$lblpt = $oDB->fetchOne($sql1);
$newpt_flag = false;
if($lblpt != null){
    $str_pt = $lblpt['LabelPatternValue'];
    if(strpos($str_pt, "******") !== false){
        $newpt_flag = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- <meta http-equiv="refresh" content="10"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        background-color: #FAFAFA;
        font: 12pt "Tahoma";
    }
    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }
    td{
        text-align:center;
        font-size:12px;
    }
    .page {
        width: 210mm;
        min-height: 297mm;
        padding: 5mm;
        margin: 10mm auto;
        border: 1px #D3D3D3 solid;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }
    .qrcode{
        position: absolute;
        z-index: -1;
        top:3px;
        right:3px;
        
    }
    .label{
        position:relative;
        float:left;
        width:48%;
        margin:5px;
        height:90mm;
        /* background-image: url("image/f1.png"); */
        background-repeat: no-repeat;
        background-size: auto;
        background-position: center;
    }
    table {
    border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }
    
    @page {
        size: A4;
        margin: 0;
    }
    @media print {
         * {
        -webkit-print-color-adjust: exact;
    }
        html, body {
            width: 210mm;
            height: 297mm;        
        }
        .page {
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
            
        }
          .label{
        /* background-image: url("image/f1.png"); */
        background-repeat: no-repeat;
        background-size: auto;
        background-position:bottom right;
    }
    }
</style>

<?php

// $text = 'D'.date('y').$monthar[date('m')].$datear[date('d')];
//var_dump($text);

$page = 1;
//var_dump($page);
?>
<body>
    <div class="book">
    <?php
    $page = ceil($quantity/2);
    $pagenum=$startlabel;
    if ($pagenum>=199) {
        echo "B???n kh??ng ???????c in qu?? 99 label c???a s???n ph???m n??y trong ng??y h??m nay!";
       exit();
    }
    for ($j=$startlabel; $j < $startlabel+$page; $j++) { 
    ?>
    <div class="page">
        
        <table style="width:100%">
        <tr>
            <td colspan='9'>
                <h2 style='text-align:center; margin:5px;'>PHI???U CHUY???N C??NG ??O???N</h2>
                <h3 style='text-align:center; margin:5px;'>(??? ??? ??? ??? ??? ???)</h3>
            </td>
            <td colspan='2'>
                <img src="../img/halla.png" alt="" style='height:55px'>
            </td>
        </tr>
            <tr style='font-weight:bold;'>
                <td colspan='2'>Part Name</td>
                <td colspan='2'>Part No</td>
                <td >Cavity</td>
                <td>Date</td>
                <td>Lot</td>
                <td>Shift</td>
                <td colspan='2'>Lot No</td>
                <?php
                    if($newpt_flag){
                        $codevalue = $Products->number."-".date("ymd",strtotime($date))."-".sprintf('%02d', $pagenum)."-".$shift;
                    }else{
                        $codevalue = $Products->number."-".date("md",strtotime($date))."-".sprintf('%02d', $pagenum)."-".$shift;
                    }
                    // Insert th??ng tin v??o b???ng label list
                    $kq = $oDB->in_table(`LabelList`,"`LabelListValue`='".$codevalue."'");
                    if ($kq==false) {
                        $sql = "INSERT INTO LabelList (`ProductsId`,`UsersId`,`LabelListValue`) VALUES (".$id.",".$_SESSION[_site_]['userid'].",'".$codevalue."')";
                        $oDB->query($sql);
                    }else{

                    }

                ?>
                <td rowspan='2'><img src="http://192.168.1.2:88/qr/?data=<?php echo $codevalue ?>" alt=""></td>
            </tr>
            <tr>
                <td colspan='2'><?php echo $Products->name ?></td>
                <td colspan='2'><?php echo $Products->number ?></td>
                <td ><?php echo $cavity ?></td>
                <td><?php echo date("Y-M-d",strtotime($date)) ?></td>
                <td><?php echo $pagenum ?></td>
                <td><?php echo $shift ?></td>
                <td colspan='2'>
                    <?php  
                        if($newpt_flag){
                            echo date("ymd",strtotime($date))."-".sprintf('%02d', $pagenum)."-".$shift;
                        }else{
                            echo date("md",strtotime($date))."-".sprintf('%02d', $pagenum)."-".$shift;
                        }
                    ?>
                </td>

            </tr>

            <tr style='font-weight:bold;'>
                <td rowspan='2'>No</td>
                <td rowspan='2'>T??n c??ng ??o???n</td>
                <td rowspan='2' colspan='2'> C??c ph???n</td>
                <td colspan='2'>S??? l?????ng</td>
                <td colspan='2' rowspan='2'>L???ch s??? chi ti???t l???i</td>
                <td rowspan='2'>QA confirm</td>
                <td Colspan='2' rowspan='2'>Chi ti???t ?????c bi???t</td>
            </tr>

            <tr>
                <td style='width:60px'>Ok</td>
                <td style='width:60px'>NG</td>
            </tr>
<?php
for ($i=1; $i < 4 ; $i++) { 
    # code...

?>
<tr>
<td rowspan='2'><?php echo $i?></td>
<td rowspan='2'></td>
<td style='height:50px'>Ng??y l??m</td>
<td style='width:120px'>  / </td>
<td rowspan='2'></td>
<td rowspan='2'></td>
<td colspan='2'></td>
<td rowspan='2'></td>
<td rowspan='2' colspan='2'></td>
</tr>

<tr>
<td style='height:50px'>Ng?????i l??m</td>
<td></td>
<td colspan='2'></td>
</tr>

<?php
}
?>
<tr>
    <td colspan='11' style='text-align:left;font-weight:bold;height:30px;'>Ghi ch?? :</td>
</tr>          


</table>


<br>

<?php
$pagenum++;
?>
<table style="width:100%">
        <tr>
            <td colspan='9'>
                <h2 style='text-align:center; margin:5px;'>PHI???U CHUY???N C??NG ??O???N</h2>
                <h3 style='text-align:center; margin:5px;'>(??? ??? ??? ??? ??? ???)</h3>
            </td>
            <td colspan='2'>
                <img src="../img/halla.png" alt="" style='height:55px'>
            </td>
        </tr>
            <tr style='font-weight:bold;'>
                <td colspan='2' style='font-weight:bold;'>Part Name</td>
                <td colspan='2'>Part No</td>
                <td >Cavity</td>
                <td>Date</td>
                <td>Lot</td>
                <td>Shift</td>
                <td colspan='2'>Lot No</td>
                <?php
                    if($newpt_flag){
                        $codevalue = $Products->number."-".date("ymd",strtotime($date))."-".sprintf('%02d', $pagenum)."-".$shift;
                    }else{
                        $codevalue = $Products->number."-".date("md",strtotime($date))."-".sprintf('%02d', $pagenum)."-".$shift;
                    }
                    // Insert th??ng tin v??o b???ng label list
                    $kq = $oDB->in_table(`LabelList`,"`LabelListValue`='".$codevalue."'");
                    if ($kq==false) {
                        $sql = "INSERT INTO LabelList (`ProductsId`,`UsersId`,`LabelListValue`) VALUES (".$id.",".$_SESSION[_site_]['userid'].",'".$codevalue."')";
                        $oDB->query($sql);
                    }else{

                    }

                ?>
                <td rowspan='2'><img src="http://192.168.1.2:88/qr/?data=<?php echo $codevalue ?>" alt=""></td>
            </tr>
            <tr>
                <td colspan='2' style='font-weight:bold;'><?php echo $Products->name ?></td>
                <td colspan='2'><?php echo $Products->number ?></td>
                <td ><?php echo $cavity ?></td>
                <td><?php echo date("Y-M-d",strtotime($date)) ?></td>
                <td><?php echo $pagenum ?></td>
                <td><?php echo $shift ?></td>
                <td colspan='2'>
                    <?php  
                        if($newpt_flag){
                            echo date("ymd",strtotime($date))."-".sprintf('%02d', $pagenum)."-".$shift;
                        }else{
                            echo date("md",strtotime($date))."-".sprintf('%02d', $pagenum)."-".$shift;
                        }
                    ?>
                </td>

            </tr>

            <tr style='font-weight:bold;'>
                <td rowspan='2'>No</td>
                <td rowspan='2'>T??n c??ng ??o???n</td>
                <td rowspan='2' colspan='2'> C??c ph???n</td>
                <td colspan='2'>S??? l?????ng</td>
                <td colspan='2' rowspan='2'>L???ch s??? chi ti???t l???i</td>
                <td rowspan='2'>QA confirm</td>
                <td Colspan='2' rowspan='2'>Chi ti???t ?????c bi???t</td>
            </tr>

            <tr>
                <td style='width:60px'>Ok</td>
                <td style='width:60px'>NG</td>
            </tr>
<?php
for ($i=1; $i < 4 ; $i++) { 
    # code...

?>
<tr>
<td rowspan='2'><?php echo $i?></td>
<td rowspan='2'></td>
<td style='height:50px'>Ng??y l??m</td>
<td style='width:120px'>  / </td>
<td rowspan='2'></td>
<td rowspan='2'></td>
<td colspan='2'></td>
<td rowspan='2'></td>
<td rowspan='2' colspan='2'></td>
</tr>

<tr>
<td style='height:50px'>Ng?????i l??m</td>
<td></td>
<td colspan='2'></td>
</tr>

<?php
}
?>
<tr>
    <td colspan='11' style='text-align:left;font-weight:bold;height:30px;'>Ghi ch?? :</td>
</tr>          


</table>
<?php
$pagenum++;
?>
<!-- <p>Print date : <?php echo date("Y-m-d H:i:s") ?></p>
<p>PIC : Ph???m Xu??n ?????ng</p> -->
    </div>

    <?php
     }
    
    ?>


</div>
</body>
</html>