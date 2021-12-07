<?php
header("Content-Type:text/html;charset=utf-8");
$db = 'oci:dbname=testsvr.co.kr';

//OR connect using the Oracle Instant Client

$db_username = "timesheet";
$db_password = "timesheet1004a";
$db = "oci:dbname=testsvr.htenc.co.kr:1521/ORCL";
$conn = null;

try {
 $conn = new PDO($db, $db_username, $db_password);
 echo "Connection success";
} catch (PDOException $e) {
 echo 'Connection failed: ' . $e->getMessage();
 exit;
}
?>
