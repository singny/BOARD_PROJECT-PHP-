<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if(!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include "_inc.php";
//Fun::print_r($post);
$db = new DB;

Fun::trimAll($post);
//Fun::convVal2DBAll($post);
function GetUserIDCheck($user_id){
    global $db;
    if($user_id){
        $n = $db->query_one("SELECT COUNT(*) AS CNT FROM EX_USER_SET WHERE USER_ID = '{$user_id}'");
        if($n > 0){
            return "<font style='color:red'>중복되셨어요</font>";
        }
        else
        {
            return null;
        }
    }
    else{
        return "<font style='color:red'>아이가 입력되지 않음.</font>";
    }
}

if($post["mode"] == "userid_check" && @$post["user_id"]){
    $user_id = $post["user_id"];
    echo GetUserIDCheck($user_id);
    exit;
}
if($post["mode"] == "modify" && $post["uno"] != ""){
    
    if(!$post["duty_id"]){
        echo Fun::Msg_Box("오류!", "직책 정보 참조 오류!");
        exit;
    }
    else if(!$post["dept_id"]){
        echo Fun::Msg_Box("오류!", "부서 정보 참조 오류!");
        exit;
    }
    else{
        $db->BeginTransaction();
        try{
            $sql = "UPDATE {$_table_user} SET "
                . "   USER_NAME = '{$post["user_name"]}' "
                . " , DUTY_ID = '{$post["duty_id"]}' "
                . " , DEPT_ID = '{$post["dept_id"]}' "
                . " , MOD_DATE = SYSDATE "
                . " WHERE UNO = '{$post["uno"]}'";
            $db->query($sql);
            $db->commit();
        } catch (Exception $ex) {
            $db->rollback();
            echo $ex;
            exit;
        }
        $db->EndTransaction();
        Fun::alert("정상적으로 회원 정보를 수정 완료하였습니다.", "jhpark_list.php");
    }
}
else if($post["mode"] == "write"){
    
    if(!trim($post["user_id"])){
        echo Fun::Msg_Box("오류!", "사용자 ID 참조 오류!");
        exit;
    }
    else if(!$post["duty_id"]){
        echo Fun::Msg_Box("오류!", "직책 정보 참조 오류!");
        exit;
    }
    else if(!$post["dept_id"]){
        echo Fun::Msg_Box("오류!", "부서 정보 참조 오류!");
        exit;
    }
    
    $resultChecked = GetUserIDCheck($post["user_id"]);
    if($$resultChecked){
        echo $$resultChecked;
        exit;
    }
    $db->BeginTransaction();
        try{
            $NO = $db->nextid("SEQ_" . $_table_user);
            $sql = "INSERT INTO {$_table_user} ("
                    . " UNO, USER_ID, USER_NAME, DUTY_ID, DEPT_ID, REG_DATE, MOD_DATE "
                    . ") VALUES ("
                    . " {$NO}, '{$post["user_id"]}', '{$post["user_name"]}', '{$post["duty_id"]}', '{$post["dept_id"]}', SYSDATE, SYSDATE "
                    . ")";
            $db->query($sql);
            $db->commit();
        } catch (Exception $ex) {
            $db->rollback();
            echo $ex;
            exit;
        }
        $db->EndTransaction();
        Fun::alert("정상적으로 회원을 추가 완료하였습니다.", "jhpark_list.php");
    
}
else{
    Fun::alert("정상적인 방법으로 접근하여 주세요. 참조 오류 입니다.");
}
?>
