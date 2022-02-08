<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include "_inc.php";

$db = new DB;

$count = "SELECT count(*) from {$_table_board}";
$n = $db->query_one($count);

$sql = "SELECT con_body from {$_table_board}";
$db->query($sql);

    $row = $db->RecordAll;
    
    for($i=0;$i<=$n;$i++){
        @$con_body[$i] = @$row[$i]["CON_BODY"];
        @preg_match('/(data\:)(image)(\/)(png|gif|jpg|jpeg)(\;)(base64)(\,)([A-Za-z0-9\/\+]{32,}\={0,2})/',$con_body[$i], $img[$i]);
        @$image .= "<img src=\"{$img[$i][0]}\">";
    }

?>
<html>
    <?php echo $image?>
</html>
