<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if(!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include_once "_inc.php";

$db = new DB;

Fun::trimAll($post);

function GetUserIDCheck($user_id){
    global $db;
    if($user_id){
        $n = $db->query_one("SELECT COUNT(*) AS CNT FROM EX_USER_SET WHERE USER_ID = '{$user_id}'");
        if($n > 0){
            return "중복된 아이디입니다.";
        }
        else if(mb_strlen($user_id) < 4 ){
            return "4자리 이상의 아이디를 입력하세요.";
        }
        else
        {
            return "사용가능한 아이디입니다.";
        }
    }
    else{
        return "아이디를 입력하세요.";
    }
}


if($post["mode"] == "userid_check"){

    $user_id = $post["user_id"];
    echo GetUserIDCheck($user_id);
    exit;
}

if($post["mode"] == "join"){
$db->BeginTransaction();
try{
    $uno = $db->nextid("SEQ_" . $_table_board);
    $sql = "INSERT INTO {$_table_user} ("
            . " UNO, USER_ID, USER_PWD, USER_NAME, DEPT_ID, TEMP_ID, DUTY_ID, LOC_ID, IS_ACTIVE, IS_USE"
            . ") VALUES ("
            . " {$uno},'{$post["user_id"]}','{$post["user_pwd"]}', '{$post["user_name"]}','{$post["dept_id"]}','{$post["dept_id"]}','{$post["duty_id"]}','{$post["loc_id"]}','Y','Y'"
            . ")";
    $db->query($sql);
    $db->commit();
} catch (Exception $ex) {
    $db->rollback();
    echo $ex;
    exit;
}
$db->EndTransaction();
Fun::alert("회원가입이 완료되었습니다.", "board_login.php");
}
?>
