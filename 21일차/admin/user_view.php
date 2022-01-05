<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if(!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include_once "_inc.php";

$db = new DB;

Fun::trimAll($post);

if($post["mode"] == "modify"){

        $db->BeginTransaction();
        try{
            $sql = "UPDATE {$_table_user} SET"
                . "   USER_NAME = '{$post["user_name"]}'"
                . " , USER_PWD = '{$post["user_pwd"]}'"
                . " , DEPT_ID = '{$post["dept_id"]}'"
                . " , DUTY_ID = '{$post["duty_id"]}'"
                . " , LOC_ID = '{$post["loc_id"]}'"
                . " WHERE uno = '{$post["uno"]}'";
            $db->query($sql);
            $db->commit();
        } catch (Exception $ex) {
            $db->rollback();
            echo $ex;
            exit;
        }
        $db->EndTransaction();
        Fun::alert("회원정보를 정상적으로 수정하였습니다.", "user_view.php?uno={$post["uno"]}");
}
else if($post["mode"] == "delete"){
    $db->BeginTransaction();
    try{
        $sql = "UPDATE {$_table_user} SET"
            . " USER_PWD = null"
            . " , IS_USE = 'N'"
            . " , IS_ACTIVE = 'N'"
            . " WHERE USER_ID = '{$_SESSION["user_id"]}'";
        $db->query($sql);
        $db->commit();
    } catch (Exception $ex) {
        $db->rollback();
        echo $ex;
        exit;
    }
    $db->EndTransaction();
    Fun::alert("탈퇴가 완료되었습니다.", "board_login.php");
}
?>
