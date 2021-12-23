<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include_once "_inc.php";

$db = new DB;

$sql = "SELECT user_pwd, is_use, is_active, user_id FROM EX_USER_SET WHERE user_id = '{$post['user_id']}'";
$db->query($sql);
$count = $db->nf();
$db->next_record();

$row = $db->Record;
if($count == 0){
    FUN::alert("해당하는 아이디가 없습니다.");
    header("Location : board_login.php");
}
else if($_POST["user_pwd"] == $row["user_pwd"]){
    $_SESSION['user_id'] = $_POST['user_id'];
    header("Location : board_list.php");
}
else if($row["user_id"] == $post['user_id'] && ($row["is_use"] == 'N' || $row["is_active"] == 'N')){
    FUN::alert("탈퇴한 계정입니다. 새로운 아이디로 가입해주세요");
    header("Location : board_login.php");
}
else if ($row["user_id"] == $post['user_id'] && $row["user_pwd"] != $post['user_pwd']){
    FUN::alert("비밀번호가 일치하지 않습니다.");
    header("Location : board_login.php");
}
?>
