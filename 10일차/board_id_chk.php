<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include_once "_inc.php";

$db = new DB;
$sql = "SELECT user_id FROM EX_USER_SET WHERE user_id = '{$post['user_id']}'";
$db->query($sql);
$count = $db->nf();
if ($count == "0") {
    echo "사용가능한 아이디 입니다.";
}else if($count == "1") {
    echo "중복된 아이디 입니다.";
} else {
    echo "아이디를 입력하세요.";
}
?>
