<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if(!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include_once "_inc.php";

$db = new DB;

Fun::trimAll($post);

$user_id =null;

if($post["mode"] == "modify" && $post["con_no"] != ""){
    

        if(!$post["re_user"]){
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
        //파일 업로드
        $uploaddir = __DIR__."/upload_file/{$post["con_no"]}/";
        $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
            //echo "파일이 유효하고, 성공적으로 업로드 되었습니다.\n";
                $file_sql="UPDATE BOARD_CONTENTS SET FILE_PATH = '{$uploadfile}' WHERE CON_NO={$post["con_no"]}";
                $db->query($file_sql);
                $db->commit();
                
        
        } else {
            print "파일 업로드 공격의 가능성이 있습니다!\n";
        }
        Fun::alert("정상적으로 글를 수정하였습니다.", "board_list.php");
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
                    . " {$con_no}, to_char(sysdate,'yyyy.mm.dd hh24:mi:ss'),'{$post["uno"]}', '{$post["con_title"]}','{$post["con_body"]}', {$post["re_user"]}"
                    . ")";
            $db->query($sql);
            $db->commit();
        } catch (Exception $ex) {
            $db->rollback();
            echo $ex;
            exit;
        }
        $db->EndTransaction();



        //파일 업로드
        // $uploadFile = Fun::uploadFile("userfile", "upload_file/{$con_no}");
        mkdir("upload_file/{$con_no}", 0777, true);
        $uploaddir = __DIR__."/upload_file/{$con_no}/";
        $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
            //echo "파일이 유효하고, 성공적으로 업로드 되었습니다.\n";
                $file_sql="UPDATE BOARD_CONTENTS SET FILE_PATH = '{$uploadfile}' WHERE CON_NO={$con_no}";
                $db->query($file_sql);
                $db->commit();
                
        
        } else {
            print "파일 업로드 공격의 가능성이 있습니다!\n";
        }
         Fun::alert("정상적으로 글을 추가하였습니다.", "board_list.php");
    
}
else if($post["mode"] == "delete"){
$db->BeginTransaction();
try{
    $sql = "DELETE FROM {$_table_board}
            WHERE con_no ='{$post["con_no"]}'"
    ;
    $db->query($sql);
    $db->commit();
} catch (Exception $ex) {
    $db->rollback();
    echo $ex;
    exit;
}
$db->EndTransaction();
Fun::alert("정상적으로 글을 삭제하였습니다.", "board_list.php");
}
else if($post["mode"] == "good"){

    function good($user_id){
        $user_id = $_GET["user_id"];
        global $db;
        if($user_id){
            $sql = "SELECT IS_GOOD FROM GOOD_COUNT WHERE USER_ID='{$_GET["user_id"]}'";
            $db->query($sql);
            $db->next_record();
            $row[0] = $db->Record;

            $db->query("SELECT COUNT(*) AS CNT FROM GOOD_COUNT WHERE USER_ID = '{$user_id}' and CON_NO = {$_GET["con_no"]}");
            $db->next_record();
            $row[1] = $db->Record;
            $n = $row[1]["CNT"];

            // if($n == 0){
            //     $good_no = $db->nextid("SEQ_" . "GOOD_COUNT");
            //     $good_sql = "INSERT INTO GOOD_COUNT(GOOD_NO,CON_NO,USER_ID,IS_GOOD) VALUES({$good_no},{$_GET["con_no"]},'{$user_id}','Y')";
            //     $db->query($good_sql);
            //     $count_sql = "UPDATE BOARD_CONTENTS SET CON_GOOD = CON_GOOD + 1 WHERE CON_NO='{$_GET["con_no"]}'";
            //     $db->query($count_sql);
            //     $db->commit();
            //     return "Yes";
            // } 
            if($n == 1 && $row[0]["is_good"] == 'Y'){
                $good_sql = "UPDATE GOOD_COUNT SET IS_GOOD = 'N' WHERE USER_ID = '{$user_id}' AND CON_NO = {$_GET["con_no"]}";
                $db->query($good_sql);
                $count_sql = "UPDATE BOARD_CONTENTS SET CON_GOOD = CON_GOOD - 1 WHERE CON_NO={$_GET["con_no"]}";
                $db->query($count_sql);
                $db->commit();
                return "No";
            }
            else if($n == 1 && $row[0]["is_good"] == 'N'){
                $good_sql = "UPDATE GOOD_COUNT SET IS_GOOD = 'Y' WHERE USER_ID = '{$user_id}' AND CON_NO = {$_GET["con_no"]}";
                $db->query($good_sql);
                $count_sql = "UPDATE BOARD_CONTENTS SET CON_GOOD = CON_GOOD + 1 WHERE CON_NO= {$_GET["con_no"]}";
                $db->query($count_sql);
                $db->commit();
                return "Yes";
            }
        }

    }

    echo good($user_id);
    exit;
}
else{
    Fun::alert("정상적인 방법으로 접근하여 주세요. 참조 오류 입니다.");
}

