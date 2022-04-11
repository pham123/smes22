<?php

function renderlabel($sDB,$text,$productid){
if ($text=='') {
    $returnar['lp']="NOT YET SET";
    $returnar['sq']=1;
}else{
$array = explode(';', $text );
$lp = '';
for ($i=0; $i < count($array); $i++) { 
    if ($i>1) {
        $text2 = $array[$i];
        if (substr_count($array[$i],",")==0) {
            $lp=$lp.$array[$i];
        }else{
            $array2 = explode(',', $text2 );
            $type = $array2[0];
            $setting = $array2[1];
            switch ($type) {
                case 'PN':
                    $sql = "SELECT ProductsNumber FROM `Products` where `ProductsId`= ?";
                    $productnum = $sDB->query($sql, $productid)->fetchArray();
                    $lp=$lp.$productnum['ProductsNumber'];
                    break;
                case 'T':
                    switch ($setting) {
                        case 'MMDD':
                            $lp=$lp.date("md",strtotime('now'));
                            $key = date("md",strtotime('now'));
                            break;
                        case 'YYMMDD':
                            $lp=$lp.date("ymd",strtotime('now'));
                            $key = date("ymd",strtotime('now'));
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                    break;
                case 'S':
                    $lp=$lp.str_repeat("*", $setting);
                    $sqvalue =  $setting;              
                    break;

                default:
                    # code...
                    break;
            }
        }
    }
}
$returnar['lp']=$lp;
$returnar['sq']=$sqvalue;
}
return $returnar;
}