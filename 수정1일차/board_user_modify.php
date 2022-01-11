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
                . " WHERE USER_ID = '{$_SESSION["user_id"]}'";
            $db->query($sql);
            $db->commit();
        } catch (Exception $ex) {
            $db->rollback();
            echo $ex;
            exit;
        }
        $db->EndTransaction();

        @mkdir("upload_file/user/{$post["uno"]}", 0777, true);
        $uploaddir = __DIR__ . "/upload_file/user/{$post["uno"]}/";
        $uploadfile = $uploaddir . basename($_FILES['uploadfile']['name']);
        if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $uploadfile)) {
            //echo "파일이 유효하고, 성공적으로 업로드 되었습니다.\n";
            $file_sql = "UPDATE {$_table_user} SET FILE_PATH = '{$uploadfile}' WHERE UNO={$post["uno"]}";
            $db->query($file_sql);
            $db->commit();
        } else {
            //print "파일 업로드 공격의 가능성이 있습니다!\n";
        }
        Fun::alert("회원정보를 정상적으로 수정하였습니다.", "my_page.php");
}
else if($post["mode"] == "user_modify"){

    $db->BeginTransaction();
    try{
        $sql = "UPDATE {$_table_user} SET"
            . "   USER_NAME = '{$post["user_name"]}'"
            . " , USER_PWD = '{$post["user_pwd"]}'"
            . " , DEPT_ID = '{$post["dept_id"]}'"
            . " , DUTY_ID = '{$post["duty_id"]}'"
            . " , LOC_ID = '{$post["loc_id"]}'"
            . " WHERE UNO = '{$post["uno"]}'";
        $db->query($sql);
        $db->commit();
    } catch (Exception $ex) {
        $db->rollback();
        echo $ex;
        exit;
    }
    $db->EndTransaction();

    @mkdir("upload_file/user/{$post["uno"]}", 0777, true);
    $uploaddir = __DIR__ . "/upload_file/user/{$post["uno"]}/";
    $uploadfile = $uploaddir . basename($_FILES['uploadfile']['name']);
    if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $uploadfile)) {
        //echo "파일이 유효하고, 성공적으로 업로드 되었습니다.\n";
        $file_sql = "UPDATE {$_table_user} SET FILE_PATH = '{$uploadfile}' WHERE UNO={$post["uno"]}";
        $db->query($file_sql);
        $db->commit();
    } else {
        print "파일 업로드 공격의 가능성이 있습니다!\n";
    }
    Fun::alert("회원정보를 정상적으로 수정하였습니다.", "./admin/user_view.php?uno={$post["uno"]}");
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
    unset($_SESSION["user_id"]);
    Fun::alert("탈퇴가 완료되었습니다.", "board_login.php");
}
?>
