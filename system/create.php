<?php
if (isset($_POST['action'])) {
  //var_dump($_POST);
  // array(6) { ["action"]=> string(6) "update" ["target"]=> string(7) "Company" ["key_hidden"]=> string(1) "1" ["Department_Name"]=> string(3) "ADM" ["Department_Information"]=> string(5) "Admin" ["Select_Company"]=> string(1) "1" }

  $table = safe($_POST['target']);
  $keyid = safe($_POST[$table.'Id']);

$columntext = '';
$valuetext = '';
foreach ($_POST as $key => $value) {
  if ($key=='action'||$key=='target'||$key==$table.'Id') {
    
  }else{
    $columntext = $columntext.$key.",";
    $valuetext = $valuetext."'".safe($value)."',";
  }
}
$columntext = rtrim($columntext, ',');
$valuetext = rtrim($valuetext, ',');
//echo $text;

  $update_sql = "
  INSERT INTO ".$table." (".$columntext.")
  VALUES (".$valuetext.");
  ";

//echo $update_sql;
  $oDB -> query($update_sql);
} else {
  //echo 'NA';
}



$sql = "Select *
        from ".$target." 
        Order by ".$target."Id DESC
        ";
$ketqua = $oDB-> fetchOne($sql);
$headerar = (array_keys($ketqua));

//var_dump($headerar);


?>
  <form action="?<?php echo $actionkey ?>" method="Post" style='width: 100%;'>
  <input type="hidden" name="action" value='create'>
  <input type="hidden" name="target" value='<?php echo $target ?>'>
        <div class="form-group row">
    <?php
      foreach ($headerar as $key => $value) {
          echo "<div class='col-md-6'>";
          echo "<span>".$oDB->lang($value)."</span>";
          if ($value==$target.'Id') {
            echo "<input type='text' name='".$value."' id='".$value."' value='".($ketqua[$value]+1)."' class='form-control' readonly>";
          }else{
            if (strpos( $value, $target ) !== false) {

              if (strpos( $value, 'Date' ) !== false) {
                // echo "<input type='Date' name='".$value."' id='".$value."' value='".($ketqua[$value]->format('Y-m-d'))."' class='form-control'>";
                //echo "<p>".$ketqua[$value]."</p>";
              } else {
                echo "<input type='text' name='".$value."' id='".$value."' value='' class='form-control' required>";
              }
              
            }else{
              //echo $value;
              $SelectTable = str_replace('Id', '', $value);
              $SelectSql = "Select ".$SelectTable."Id, ".$SelectTable."Name From ".$SelectTable;
              $SelectAr = $oDB-> fetchAll($SelectSql);

              // $selectvalue = explode('_',$value);
              echo   "<select name='".$value."' id='".$value."' class='selectpicker show-tick' data-live-search='true' data-style='btn-info' data-width='100%' required>";
              foreach ($SelectAr as $SelectKey => $SelectValue) {
                if ($ketqua[$value]==$SelectValue[$SelectTable.'Id']) {
                  echo   "<option value='".$SelectValue[$SelectTable.'Id']."' Selected>".$SelectValue[$SelectTable.'Name']."</option>";
                } else {
                  echo   "<option value='".$SelectValue[$SelectTable.'Id']."'>".$SelectValue[$SelectTable.'Name']."</option>";
                }
                
                
              }
              echo   "</select>";
            }
            
          }
          echo "</div>";
      }
    ?>
        </div>
        <div class="form-group row">
          <button type="submit" class='btn btn-success form-control'>Submit</button>
        </div>
  </form>    
