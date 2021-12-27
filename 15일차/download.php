<?php
ini_set("display_errors", "On");
include "_inc.php";

$db = new DB;


$sql = "SELECT CON_NO, FILE_PATH FROM {$_table_board} WHERE CON_NO = {$post["con_no"]}";
$db-> query($sql);
while ($db->next_record()) {
  $row = $db->Record;
}

$target_Dir = $row["FILE_PATH"];
  $file = basename($row["FILE_PATH"]);
  $down = $target_Dir.$file;
  $filesize = filesize($down);
  
  if(file_exists($down)){
    header("Content-Type:application/octet-stream");
    header("Content-Disposition:attachment;filename=$file");
    header("Content-Transfer-Encoding:binary");
    header("Content-Length:".filesize($target_Dir.$file));
    header("Cache-Control:cache,must-revalidate");
    header("Pragma:no-cache");
    header("Expires:0");
    if(is_file($down)){
        $fp = fopen($down,"r");
        while(!feof($fp)){
          $buf = fread($fp,8096);
          $read = strlen($buf);
          print($buf);
          flush();
        }
        fclose($fp);
    }
  }
?>


