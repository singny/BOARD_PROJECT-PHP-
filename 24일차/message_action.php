<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include_once "_inc.php";

$db = new DB;

$m_title = htmlspecialchars(Fun::convVal2DB($post["m_title"]));
$m_body = htmlspecialchars(Fun::convVal2DB($post["m_body"]));

$db->BeginTransaction();
try {
    $mno = $db->nextid("SEQ_" . $_table_message);
    $sql = "INSERT INTO {$_table_message} ("
        . " MNO, WR_USER, RE_USER, M_DATETIME, M_TITLE, M_BODY"
        . ") VALUES ("
        . " {$mno},{$post["wr_user"]},{$post["re_user"]}, to_char(sysdate,'yyyy.mm.dd hh24:mi:ss'),'{$m_title}','{$m_body}'"
        . ")";
    $db->query($sql);
    $db->commit();
} catch (Exception $ex) {
    $db->rollback();
    echo $ex;
    exit;
}
$db->EndTransaction();
Fun::alert("쪽지가 정상적으로 보내졌습니다.","message_list.php")
?>
