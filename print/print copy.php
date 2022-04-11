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
<body>
    <div class="book">
    <div class="page">
        
       
    </div>


</div>
</body>
</html>