<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if(!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include_once "_inc.php";

$db = new DB;

Fun::trimAll($post);

if($post["mode"] == "modify" && $post["con_no"] != ""){
    
    if(!$post["re_dept"]){
        echo Fun::Msg_Box("오류!", "부서 정보 참조 오류!");
        exit;
    }
    else if(!$post["re_user"]){
        echo Fun::Msg_Box("오류!", "직원 정보 참조 오류!");
        exit;
    }
    else{
        $db->BeginTransaction();
        try{
            $sql = "UPDATE {$_table_board} SET"
                . "   CON_TITLE = '{$post["con_title"]}'"
                . " , CON_BODY = '{$post["con_body"]}'"
                . " , RE_USER = '{$post["re_user"]}'"
                . " WHERE CON_NO = '{$post["con_no"]}'";
            $db->query($sql);
            $db->commit();
        } catch (Exception $ex) {
            $db->rollback();
            echo $ex;
            exit;
        }
        $db->EndTransaction();
        Fun::alert("정상적으로 회원 정보를 수정 완료하였습니다.", "board_list.php");
    }
}
else if($post["mode"] == "write"){
    
    // if(!trim($post["user_id"])){
    //     echo Fun::Msg_Box("오류!", "사용자 ID 참조 오류!");
    //     exit;
    // }
    // else if(!$post["duty_id"]){
    //     echo Fun::Msg_Box("오류!", "직책 정보 참조 오류!");
    //     exit;
    // }
    // else if(!$post["dept_id"]){
    //     echo Fun::Msg_Box("오류!", "부서 정보 참조 오류!");
    //     exit;
    // }
    $db->BeginTransaction();
        try{
            $con_no = $db->nextid("SEQ_" . $_table_board);
            $sql = "INSERT INTO {$_table_board} ("
                    . " CON_NO, CON_DATETIME, WR_USER, CON_TITLE, CON_BODY, RE_USER"
                    . ") VALUES ("
                    . " {$con_no}, to_char(sysdate,'yyyy.mm.dd hh24:mi:ss'), 1, '{$post["con_title"]}','{$post["con_body"]}', {$post["re_user"]}"
                    . ")";
            $db->query($sql);
            $db->commit();
        } catch (Exception $ex) {
            $db->rollback();
            echo $ex;
            exit;
        }
        $db->EndTransaction();
        Fun::alert("정상적으로 회원을 추가 완료하였습니다.", "board_list.php");
    
}
else{
    Fun::alert("정상적인 방법으로 접근하여 주세요. 참조 오류 입니다.");
}
?>
