<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include_once "_inc.php";

$db = new DB;

Fun::trimAll($post);

$user_id = null;

if ($post["mode"] == "modify" && $post["con_no"] != "") {


    if (!$post["re_user"]) {
        echo Fun::Msg_Box("오류!", "직원 정보 참조 오류!");
        exit;
    } else {
        $db->BeginTransaction();

        $con_title = htmlspecialchars(Fun::convVal2DB($post["con_title"]));
        $con_body = htmlspecialchars(Fun::convVal2DB($post["con_body"]));
        try {
            $sql = "UPDATE {$_table_board} SET"
                . "   CON_TITLE = '{$con_title}'"
                . " , CON_BODY = '{$con_body}'"
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
        $uploaddir = __DIR__ . "/upload_file/{$post["con_no"]}/";
        $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
            //echo "파일이 유효하고, 성공적으로 업로드 되었습니다.\n";
            $file_sql = "UPDATE BOARD_CONTENTS SET FILE_PATH = '{$uploadfile}' WHERE CON_NO={$post["con_no"]}";
            $db->query($file_sql);
            $db->commit();
        } else {
            //print "파일 업로드 공격의 가능성이 있습니다!\n";
        }
        Fun::alert("정상적으로 글를 수정하였습니다.", "board_view.php?con_no={$post["con_no"]}");
    }
} else if ($post["mode"] == "write") {

    $con_title = htmlspecialchars(Fun::convVal2DB($post["con_title"]));
    $con_body = htmlspecialchars(Fun::convVal2DB($post["con_body"]));

    $db->BeginTransaction();
    try {
        $con_no = $db->nextid("SEQ_" . $_table_board);
        $sql = "INSERT INTO {$_table_board} ("
            . " CON_NO, CON_DATETIME, WR_USER, CON_TITLE, CON_BODY, RE_USER"
            . ") VALUES ("
            . " {$con_no}, to_char(sysdate,'yyyy.mm.dd hh24:mi:ss'),'{$post["uno"]}', '{$con_title}','{$con_body}', {$post["re_user"]}"
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
    $uploaddir = __DIR__ . "/upload_file/{$con_no}/";
    $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        //echo "파일이 유효하고, 성공적으로 업로드 되었습니다.\n";
        $file_sql = "UPDATE BOARD_CONTENTS SET FILE_PATH = '{$uploadfile}' WHERE CON_NO={$con_no}";
        $db->query($file_sql);
        $db->commit();
    } else {
        print "파일 업로드 공격의 가능성이 있습니다!\n";
    }
    Fun::alert("정상적으로 글을 추가하였습니다.", "board_list.php");

} else if ($post["mode"] == "delete") {
    $db->BeginTransaction();
    try {
        $sql = "DELETE FROM {$_table_board}
            WHERE con_no ='{$post["con_no"]}'";
        $db->query($sql);
        $db->commit();
    } catch (Exception $ex) {
        $db->rollback();
        echo $ex;
        exit;
    }
    $db->EndTransaction();
    Fun::alert("정상적으로 글을 삭제하였습니다.", "board_list.php");
} else if ($post["mode"] == "good") {

    function good($user_id)
    {
        $user_id = $_GET["user_id"];
        global $db;
        if ($user_id) {
            $sql = "SELECT IS_GOOD FROM GOOD_COUNT WHERE USER_ID='{$_GET["user_id"]}'";
            $db->query($sql);
            $db->next_record();
            $row[0] = $db->Record;

            if ($_GET["goodchk"] == 'true') {
                $good_sql = "UPDATE GOOD_COUNT SET IS_GOOD = 'N' WHERE USER_ID = '{$user_id}' AND CON_NO = {$_GET["con_no"]}";
                $db->query($good_sql);
                $db->commit();

                $db->query("SELECT COUNT(*) AS con_good FROM GOOD_COUNT WHERE CON_NO = {$_GET["con_no"]} AND IS_GOOD = 'Y'");
                $db->next_record();
                $row[1] = $db->Record;
                $n = $row[1]["con_good"];
                $count_sql = "UPDATE board_contents SET con_good = {$n} WHERE CON_NO = {$_GET["con_no"]}";
                $db->query($count_sql);
                $db->commit();
                return $n;
            } else if ($_GET["goodchk"] == 'false') {
                $good_sql = "UPDATE GOOD_COUNT SET IS_GOOD = 'Y' WHERE USER_ID = '{$user_id}' AND CON_NO = {$_GET["con_no"]}";
                $db->query($good_sql);
                $db->commit();

                $db->query("SELECT COUNT(*) AS con_good FROM GOOD_COUNT WHERE CON_NO = {$_GET["con_no"]} AND IS_GOOD = 'Y'");
                $db->next_record();
                $row[1] = $db->Record;
                $n = $row[1]["con_good"];
                $count_sql = "UPDATE board_contents SET con_good = {$n} WHERE CON_NO = {$_GET["con_no"]}";
                $db->query($count_sql);
                $db->commit();
                return $n;
            }
        }
    }

    echo good($user_id);

    exit;
} else if ($post["mode"] == "comment") {
    $db->BeginTransaction();

    $com_body = htmlspecialchars(Fun::convVal2DB($post["com_body"]));

    try {
        $com_no = $db->nextid("SEQ_" . $_table_comment);
        $sql = "INSERT INTO {$_table_comment} ("
            . " COM_NO, CON_NO, COM_DATETIME, USER_ID, COM_BODY, COM_EN ,SCROLL"
            . ") VALUES ("
            . " {$com_no},{$post["con_no"]}, to_char(sysdate,'yyyy.mm.dd hh24:mi:ss'),'{$post["user_id"]}','{$com_body}','Y','{$post["scroll"]}'"
            . ")";
        $db->query($sql);
        $db->commit();
    } catch (Exception $ex) {
        $db->rollback();
        echo $ex;
        exit;
    }
    $comment_sql = "UPDATE board_contents set con_comment = con_comment+1 where con_no ={$post["con_no"]}";
    $db->query($comment_sql);
    $db->commit();
    $db->EndTransaction();
    header("Location:board_view.php?con_no={$post["con_no"]}");
} else if ($post["mode"] == "cmt_delete") {
    $db->BeginTransaction();
    try {
        $sql = "DELETE FROM {$_table_comment}
                WHERE com_no ='{$post["com_no"]}'";
        $db->query($sql);
        $db->commit();
    } catch (Exception $ex) {
        $db->rollback();
        echo $ex;
        exit;
    }
    $db->EndTransaction();
    $scroll_sql = "UPDATE board_comment SET com_en = 'Y'";
    $db->query($scroll_sql);
    $comment_sql = "UPDATE board_contents set con_comment = con_comment-1 where con_no ={$post["con_no"]}";
    $db->query($comment_sql);
    $db->commit();
    $prevPage = $_SERVER['HTTP_REFERER'];
    header('location:' . $prevPage);
} else if ($post["mode"] == "cmt_modify") {
    var_dump($post);

    // 스크롤 초기화
    $scroll_sql = "UPDATE board_comment SET com_en = 'N'";
    $db->query($scroll_sql);
    $db->commit();

    $db->BeginTransaction();

    $com_body = htmlspecialchars(Fun::convVal2DB($post["com_body"]));
    try {
        $sql = "UPDATE {$_table_comment} SET"
            . "   COM_DATETIME = to_char(sysdate,'yyyy.mm.dd hh24:mi:ss')"
            . " , COM_BODY = '{$com_body}'"
            . ", COM_EN = 'Y'"
            . " WHERE COM_NO = '{$post["com_no"]}'";
        $db->query($sql);
        $db->commit();
    } catch (Exception $ex) {
        $db->rollback();
        echo $ex;
        exit;
    }
    $db->EndTransaction();
    header("Location:board_view.php?con_no={$post["con_no"]}");
} else {
    Fun::alert("정상적인 방법으로 접근하여 주세요. 참조 오류 입니다.");
}
