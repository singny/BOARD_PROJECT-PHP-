<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
include "_inc.php";
@error_reporting(E_ALL);
if (@$_REQUEST["con_no"]) {
  $con_no = $_REQUEST["con_no"];
  $db = new DB;
  //$db->Debug = true;
  $main_sql = "SELECT to_char(b.con_datetime,'YYYY.MM.DD HH24:mi:ss') as con_datetime, b.con_title, u.user_name, de.dept_name, b.con_vc , du.duty_name, b.con_body
              , u.user_id, b.con_good, u.is_use, u.is_active
              FROM ex_user_set u, ex_dept_set de, {$_table_board} b, ex_duty_set du
              WHERE b.wr_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no and b.con_no={$con_no}";
  $sql = "WITH A as (
    {$main_sql}
)
SELECT COUNT(*) AS CNT FROM A";
  $sql = $main_sql;
  $db->query($sql);
  $db->next_record();
  $row[0] = $db->Record;
  $date = substr($row[0]["con_datetime"][0], 0, 10);
  if ($row[0]["is_use"] == 'N' || $row[0]["is_active"] == 'N') {
    $dept_user = "[" . $row[0]["dept_name"] . "] " . $row[0]["user_name"] . " " . $row[0]["duty_name"] . "&nbsp;<img src=\"image/delete-user.png\" title=\"탈퇴한 계정\" width=\"25px\" height=\"25px\">";
  } else {
    $dept_user = "[" . $row[0]["dept_name"] . "] " . $row[0]["user_name"] . " " . $row[0]["duty_name"];
  }
  // $dept_user = "[" . $row[0]["dept_name"] . "] " . $row[0]["user_name"] . " " . $row[0]["duty_name"];
  $con_title = Fun::convDB2Val($row[0]["con_title"]);
  $con_good = $row[0]["con_good"];
  $body = Fun::convDB2Val($row[0]["con_body"]);
  $con_body = nl2br($body);

  $re_sql = "SELECT u.user_name, de.dept_name, du.duty_name, u.uno, de.dept_no, u.is_use, u.is_active, u.uno
FROM ex_user_set u, ex_dept_set de, {$_table_board} b, ex_duty_set du
WHERE b.re_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no and b.con_no={$con_no}";

  $db->query($re_sql);
  $db->next_record();
  $row[1] = $db->Record;

  if ($row[1]["is_use"] == 'N' || $row[1]["is_active"] == 'N') {
    $re_user = "[" . $row[1]["dept_name"] . "] " . $row[1]["user_name"] . " " . $row[1]["duty_name"] . "&nbsp;<img src=\"image/delete-user.png\" title=\"탈퇴한 계정\" width=\"25px\" height=\"25px\">";
  } else {
    $re_user = "[" . $row[1]["dept_name"] . "] " . $row[1]["user_name"] . " " . $row[1]["duty_name"];
  }

  $re_uno = $row[1]["uno"];
  $re_dept = $row[1]["dept_no"];
  $query_string = Fun::getParamUrl("con_no");
  $list_uri = "board_list.php?" . $query_string;
  $modify_uri = "board_write.php?" . $query_string . "&con_no={$con_no}&mode=modify&re_user={$re_uno}";
  $delete_uri = "board_action.php?mode=delete" . $query_string . "&con_no={$con_no}";
  $delete_uri = "board_action.php?mode=delete&con_no={$con_no}";
  $delete_uri = "board_action.php?mode=delete" . $query_string . "&con_no={$con_no}";

  // 수정, 삭제 버튼
  $modify_btn = null;
  $delete_btn = null;
  if ($_SESSION["user_id"] == $row[0]["user_id"] || $_SESSION["user_id"] == "admin") {
    $modify_btn = "<input type=\"button\" value=\"수정\" onclick=\"goModify()\">";
    $delete_btn = "<input type=\"button\" value=\"삭제\" onclick=\"goDelete()\">";
  };

  //조회수
  $vc_no = $db->nextid("SEQ_" . "VIEW_COUNT");
  $view_date = date("Y/m/d");


  $yn_sql = "SELECT user_id FROM VIEW_COUNT WHERE user_id = '{$_SESSION["user_id"]}' and con_no={$con_no}";
  $db->query($yn_sql);
  $db->next_record();
  @$row[3] = $db->Record;


  if (!$row[3]) {
    $view_sql = "INSERT INTO VIEW_COUNT(vc_no, con_no, user_id) VALUES($vc_no, $con_no,'{$_SESSION["user_id"]}')";
    $db->query($view_sql);
    $vc_sql = "UPDATE {$_table_board} SET con_vc= con_vc + 1 WHERE con_no = {$con_no}";
    $row[0]["con_vc"] += 1;
    $db->query($vc_sql);
    $db->commit();
  }

  // 파일 업로드
  $file_sql = "SELECT CON_NO, FILE_PATH FROM {$_table_board} WHERE CON_NO = {$con_no}";
  $db->query($file_sql);
  while ($db->next_record()) {
    $row[2] = $db->Record;
  }


  @$file_name = basename($row[2]["FILE_PATH"]);
  $attach = null;
  if ($file_name) {

    $attach = "
    <div class=\"row\">
  <div class=\"col-75\">
 <img src=\"./upload_file/{$con_no}/$file_name\" class = \"image\" onerror=\"this.style.display='none'\" alt=\"\">
    </div>
  </div>
    <div class=\"row\">
  <div class=\"col-75\">
    <h4>첨부파일  &nbsp;&nbsp;<img src=\"./image/folder.png\" style=\"width:17.5px;height:17.5px;\">&nbsp;$file_name&nbsp;&nbsp;&nbsp;<a href=\"download.php?con_no={$con_no}\">다운로드</a></h4>
    </div>
  </div>";
  }


  // 좋아요
  $db->query("SELECT COUNT(*) AS CNT FROM GOOD_COUNT WHERE USER_ID = '{$_SESSION["user_id"]}' AND CON_NO = {$con_no}");
  $db->next_record();
  $row[4] = $db->Record;
  $n = $row[4]["CNT"];

  if ($n == 0) {
    $good_no = $db->nextid("SEQ_" . "GOOD_COUNT");
    $good_sql = "INSERT INTO GOOD_COUNT(GOOD_NO,CON_NO,USER_ID,IS_GOOD) VALUES({$good_no},{$con_no},'{$_SESSION["user_id"]}','N')";
    $db->query($good_sql);
    $db->commit();
  }

  $good_sql = "SELECT IS_GOOD FROM GOOD_COUNT WHERE USER_ID='{$_SESSION["user_id"]}' AND CON_NO = {$con_no}";
  $db->query($good_sql);
  $db->next_record();
  $row[4] = $db->Record;

  if ($row[4]["is_good"] == 'Y') {
    $goodsave = "<div style=\"margin-left:42%; cursor:pointer\" onclick=\"good(this)\">
              <img src=\"./image/no_good.png\" id=\"no_good\" style=\"width:60px; height:60px\" hidden/>
              <img src=\"./image/good.png\" id=\"good\" style=\"width:60px; height:60px\" />
              </div>";
  } else {
    $goodsave = "<div style=\"margin-left:42%; cursor:pointer\" onclick=\"good(this)\">
  <img src=\"./image/no_good.png\" id=\"no_good\" style=\"width:60px; height:60px\" />
  <img src=\"./image/good.png\" id=\"good\" style=\"width:60px; height:60px\" hidden/>
  </div>";
  }

  // 댓글
  $user_sql = "SELECT u.user_name, de.dept_name, du.duty_name
              FROM ex_user_set u, ex_dept_set de, {$_table_board} b, ex_duty_set du
              WHERE u.dept_id = de.dept_no and u.duty_id = du.duty_no and u.user_id = '{$_SESSION["user_id"]}'";
  $db->query($user_sql);
  $db->next_record();
  $row[5] = $db->Record;
  $comment_user = "[" . $row[5]["dept_name"] . "] " . $row[5]["user_name"] . " " . $row[5]["duty_name"];

  $comment = null;
  $row["com_body"] = null;
  $row["com_datetime"] = null;
  $comment_sql = "SELECT u.user_name, de.dept_name, du.duty_name, b.com_no, b.con_no , b.user_id, b.com_body, b.com_en, b.scroll
  , to_char(b.com_datetime,'YYYY.MM.DD HH24:mi:ss') as com_datetime, u.is_active,u.is_use
  FROM ex_user_set u, ex_dept_set de, board_comment b, ex_duty_set du
  WHERE u.dept_id = de.dept_no and u.duty_id = du.duty_no and u.user_id = b.user_id and b.con_no = {$con_no} ORDER BY com_no";
  $db->query($comment_sql);
  while ($db->next_record()) {
    $row[6] = $db->Record;

    if ($_SESSION["user_id"] == $row[6]["user_id"] || $_SESSION["user_id"] == "admin") {

      if ($row[6]["is_use"] == 'N' || $row[6]["is_active"] == 'N') {
        $commented_user = "[" . $row[6]["dept_name"] . "] " . $row[6]["user_name"] . " " . $row[6]["duty_name"] . "&nbsp;<img src=\"image/delete-user.png\" title=\"탈퇴한 계정\" width=\"20px\" height=\"20px\">";
      } else {
        $commented_user = "[" . $row[6]["dept_name"] . "] " . $row[6]["user_name"] . " " . $row[6]["duty_name"];
      }
      $com_body = Fun::convDB2Val($row[6]["com_body"]);
      @$nl_combody = nl2br($com_body);
      $comment .= "<div class=\"dat_view\" >
                    <form method=\"post\" id=\"frm{$row[6]["com_no"]}\" name=\"frm{$row[6]["com_no"]}\" action=\"board_action.php?mode=cmt_modify&com_no={$row[6]["com_no"]}&con_no={$con_no}\">
                      <input type=\"hidden\" id=\"com_no\" value=\"{$row[6]["com_no"]}\" />
                      <input type=\"hidden\" id=\"con_no\" value=\"{$con_no}\" />
                      <div><b>{$commented_user}</b></div>
                      <span id=\"register{$row[6]["com_no"]}\">
                        <div class=\"dap_to comt_edit\" style=\"padding-top:5px;\">{$nl_combody}</div>
                        <div class=\"rep_me dap_to\" style=\"color:#C0C0C0; font-size:smaller\">{$row[6]["com_datetime"]}</div>
                        <div class=\"rep_me rep_menu\"style=\"text-align:right; \" >
                          <a role=\"button\" id=\"{$row[6]["com_no"]}\" onclick=\"cmtModify(this)\">수정</a>&nbsp;&nbsp;
                          <a role=\"button\" id=\"{$row[6]["com_no"]}\" onclick=\"cmtDelete(this)\">삭제</a>
                        </div>
                      </span>
                      <span id=\"modify{$row[6]["com_no"]}\" hidden>
                        <textarea id=\"com_body{$row[6]["com_no"]}\" name=\"com_body\" class=\"comment_inbox_text\" style=\"overflow: hidden; overflow-wrap: break-word; height: 70px;\">{$com_body}</textarea>
                        <br />  
                        <div class=\"register_box\" style=\"text-align:right\" >
                          <a role=\"button\" id=\"{$row[6]["com_no"]}\" onclick=\"cmtCancel(this)\">취소</a>&nbsp;&nbsp;
                          <a role=\"button\" id=\"{$row[6]["com_no"]}\" onclick=\"modifyCmt(this)\">수정</a>
                        </div>
                      </span>
                    </form>
                  </div>";
    } else {
      if ($row[6]["is_use"] == 'N' || $row[6]["is_active"] == 'N') {
        $commented_user = "[" . $row[6]["dept_name"] . "] " . $row[6]["user_name"] . " " . $row[6]["duty_name"] . "&nbsp;<img src=\"image/delete-user.png\" title=\"탈퇴한 계정\" width=\"20px\" height=\"20px\">";
      } else {
        $commented_user = "[" . $row[6]["dept_name"] . "] " . $row[6]["user_name"] . " " . $row[6]["duty_name"];
      }
      $com_body = Fun::convDB2Val($row[6]["com_body"]);
      @$nl_combody = nl2br($com_body);
      $comment .= "<div class=\"dat_view\" >
                    <div><b>{$commented_user}</b></div>
                    <div class=\"dap_to comt_edit\" style=\"padding-top:5px;\">{$nl_combody}</div>
                    <div class=\"rep_me dap_to\" style=\"color:#C0C0C0; font-size:smaller\">{$row[6]["com_datetime"]}</div>
                    <div>&nbsp;</div>
                  </div>";
    }
  }
  $cc_sql = "SELECT COUNT(*) AS CNT FROM {$_table_comment} WHERE con_no={$con_no}";
  $db->query($cc_sql);
  $db->next_record();
  $row[7] = $db->Record;

  $scroll_sql = "SELECT SCROLL, COM_EN FROM (SELECT * FROM board_comment WHERE com_en='Y' ORDER BY COM_NO DESC) WHERE ROWNUM=1";
  $db->query($scroll_sql);
  $db->next_record();
  @$row[8] = $db->Record;

  // 이전글 다음글
  $pre_sql = "SELECT MAX(CON_NO) as con_no FROM {$_table_board} WHERE CON_NO < {$con_no}";
  $db->query($pre_sql);
  $db->next_record();
  $row[9] = $db->Record;

  if ($row[9]["con_no"] == null) {
    // $pre_title = "이전글이 존재하지 않습니다.";
    $pre_tr = null;
  } else {
    $pre_contents = "SELECT con_title, file_path, con_comment FROM {$_table_board} WHERE CON_NO = {$row[9]["con_no"]}";
    $db->query($pre_contents);
    $db->next_record();
    $row[10] = $db->Record;

    if ($row[10]["file_path"]) {
      $pre_title = Fun::convDB2Val($row[10]["con_title"]) . " [" . $row[10]["con_comment"] . "]&nbsp;<img src=\"./image/folder.png\" style=\"width:17.5px;height:17.5px;\">";
    } else {
      $pre_title = Fun::convDB2Val($row[10]["con_title"]) . " [" . $row[10]["con_comment"] . "]";
    }

    $pre_tr = "<tr onclick=\"location.href='board_view.php?con_no={$row[9]["con_no"]}{$query_string}'\">
                <td style=\"width:100px\"><b>이전글</b></td>
                <td style=\"width:400px; text-align:left\"><b>{$pre_title}</b></td>
              </tr>";
  }


  $next_sql = "SELECT MIN(CON_NO) as con_no FROM {$_table_board} WHERE CON_NO > {$con_no}";
  $db->query($next_sql);
  $db->next_record();
  $row[11] = $db->Record;

  if ($row[11]["con_no"] == null) {
    // $next_title = "다음글이 존재하지 않습니다.";
    $next_tr = null;
  } else {
    $next_contents = "SELECT con_title, file_path, con_comment FROM {$_table_board} WHERE CON_NO = {$row[11]["con_no"]}";
    $db->query($next_contents);
    $db->next_record();
    $row[12] = $db->Record;

    if ($row[12]["file_path"]) {
      $next_title = Fun::convDB2Val($row[12]["con_title"]) . " [" . $row[12]["con_comment"] . "]&nbsp;<img src=\"./image/folder.png\" style=\"width:17.5px;height:17.5px;\">";
    } else {
      $next_title = Fun::convDB2Val($row[12]["con_title"]) . " [" . $row[12]["con_comment"] . "]";
    }

    $next_tr = "<tr onclick=\"location.href='board_view.php?con_no={$row[11]["con_no"]}{$query_string}'\">
                  <td style=\"width:50px\"><b>다음글</b></td>
                  <td style=\"width:400px; text-align:left\"><b>{$next_title}</b></td>
                </tr>";
  }

  // 알림창
  if($_SESSION["uno"] == $row[1]["uno"]){
    $alert_sql = "UPDATE {$_table_alert} SET is_read = 'Y' WHERE con_no = {$con_no}";
    $db->query($alert_sql);
    $db->commit();
  }
} else {
  FUN::alert("잘못된 접근입니다.", "board_login.php");
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="ko"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="ko"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="ko">
<!--<![endif]-->

<head>
  <meta charset="UTF-8">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="content-language" content="kr">
  <meta name="google-site-verification" content="8_SyZg2Wg3LNnCmFXzETp7ld4yjZB8ny17m8QsYsLwk">

  <meta name="referrer" content="unsafe-url">
  <title>우수사원 추천 게시판</title>
  <link rel="stylesheet" type="text/css" href="//nstatic.dcinside.com/dc/w/css/reset.css?v=2">
  <link rel="stylesheet" type="text/css" href="https://nstatic.dcinside.com/dc/w/css/common_210913.css?101"><!-- 211025 파일명 수정-->
  <link rel="stylesheet" type="text/css" href="https://nstatic.dcinside.com/dc/w/css/contents.css">
  <link rel="stylesheet" type="text/css" href="https://nstatic.dcinside.com/dc/w/css/minor_210913.css?1026-20"><!-- 211025 파일명 수정-->
  <link rel="stylesheet" type="text/css" href="https://nstatic.dcinside.com/dc/w/css/popup_210913.css"><!-- 211025 파일명 수정-->

  <!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="//nstatic.dcinside.com/dc/w/css/ie7.css"/>
	<![endif]-->
  <!--[if lt IE 9]>
	<script src="/_js/jquery/jquery-1.7.2.min.js"></script>
	<![endif]-->
  <!--[if gte IE 9]>
	<script src="/_js/jquery/jquery-3.2.1.min.js"></script>
	<![endif]-->
  <!--[if !IE]> -->

  <!-- <![endif]-->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <!-- 합쳐지고 최소화된 최신 CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

  <!-- 부가적인 테마 -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

  <!-- 합쳐지고 최소화된 최신 자바스크립트 -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="apple-touch-icon" sizes="57x57" href="image/icon/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="image/icon/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="image/icon/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="image/icon/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="image/icon/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="image/icon/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="image/icon/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="image/icon/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="image/icon/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192" href="image/icon/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="image/icon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="image/icon/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="image/icon/favicon-16x16.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Gowun+Dodum&display=swap" rel="stylesheet">
  <!-- 합쳐지고 최소화된 최신 CSS -->
  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css"> -->

  <!-- 부가적인 테마 -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

  <!-- 합쳐지고 최소화된 최신 자바스크립트 -->
  <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script> -->

  <!-- Taboola -->
  <script type="text/javascript">
    window._taboola = window._taboola || [];
    _taboola.push({
      category: 'auto'
    });
    ! function(e, f, u, i) {
      if (!document.getElementById(i)) {
        e.async = 1;
        e.src = u;
        e.id = i;
        f.parentNode.insertBefore(e, f);
      }
    }(document.createElement('script'),
      document.getElementsByTagName('script')[0],
      '//cdn.taboola.com/libtrc/dcinside/loader.js',
      'tb_loader_script');
    if (window.performance && typeof window.performance.mark == 'function') {
      window.performance.mark('tbl_ic');
    }
  </script>
  <style>
    body {
      font-family: 'Gowun Dodum', sans-serif;
    }

    * {
      box-sizing: border-box;
    }

    .top {
      margin-top: 5%;
      margin-left: 26%;
      margin-right: 25%
    }

    @media(max-width:500px) {
      .top {
        margin-top: 5%;
        margin-left: 25%;
        font-size: xx-small;
      }
    }

    input[type=text],
    select,
    textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 4px;
      resize: vertical;
    }

    label {
      padding: 12px 12px 12px 0;
      display: inline-block;
    }

    input[type=submit] {
      background-color: #4CAF50;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-left: 40%;
    }

    input[type=submit]:hover {
      background-color: #45a049;

    }

    input[type=button] {
      /* background-color: #6495ED;
      color: white; */
      background-color: white;
      box-shadow: rgba(30, 22, 54, 0.4) 0 0px 0px 2px inset;
      color: rgba(30, 22, 54, 0.6);
      padding: 10px 15px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-family: 'Gowun Dodum', sans-serif;

    }

    input[type=button]:hover {
      color: rgba(255, 255, 255, 0.85);
      background-color: rgba(255, 255, 255, 0.85);
      box-shadow: rgba(30, 22, 54, 0.7) 0 80px 0px 2px inset;
    }

    .container {
      border-radius: 10px;
      background-color: ghostwhite;
      padding: 20px;
      max-width: 900px;
      margin-left: 21.5%;
      margin-top: 1%;
      margin-bottom: 3%;
    }

    @media(min-width:1900px) {
      .container {
        border-radius: 10px;
        background-color: ghostwhite;
        padding: 20px;
        max-width: 900px;
        margin-left: 27%;
        margin-top: 1%;
        margin-bottom: 5%;
      }
    }

    @media(max-width:500px) {
      .container {
        border-radius: 10px;
        background-color: ghostwhite;
        padding: 20px;
        margin-left: 0%;
        margin-top: 1%;
      }
    }

    .col-25 {
      float: left;
      width: 1%;
      margin-top: 6px;
    }

    .col-75 {
      float: left;
      width: 90%;
      margin-top: 6px;
    }

    .col-50 {
      float: left;
      width: 50%;
      margin-top: 6px;
    }


    .button {
      text-align: center;
      padding: 10px;
    }

    /* Clear floats after the columns */
    .row:after {
      content: "";
      display: table;
      clear: both;
    }

    /* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
    @media screen and (max-width: 600px) {

      .col-25,
      .col-75,
      input[type=submit] {
        width: 100%;
        margin-top: 100px;
      }
    }

    .CommentWriter {
      --themeColorTC1: #000;
      --themeColorTC2: #323232;
      --themeColorTC3: #676767;
      --themeColorTC4: #878787;
      --themeColorTC5: #949494;
      --themeColorTC6: #03c75a;
      --themeColorTC7: #009f47;
      --themeColorTC8: #fb6400;
      --themeColorTC9: #f53535;
      --themeColorTC10: #0076ff;
      --themeColorTC11: #0076ff;
      --themeColorTC12: #009f47;
      --themeColorTC13: #f53535;
      --themeColorTC14: #fff;
      --themeColorTC15: #676767;
      --themeColorTC16: #a6a6a6;
      --themeColorTC17: #ff7921;
      --themeColorTC18: #222;
      --themeColorTC19: #a6a6a6;
      --themeColorTC20: #2761c2;
      --themeColorLN1: #eee;
      --themeColorLN2: #e6e6e6;
      --themeColorLN3: rgba(0, 0, 0, 0.06);
      --themeColorLN4: #e6e6e6;
      --themeColorLN5: #eee;
      --themeColorLN6: #eee;
      --themeColorBG1: #fff;
      --themeColorBG2: #fff;
      --themeColorBG3: #fff;
      --themeColorBG4: #f5fdf5;
      --themeColorBG5: #f5f6f8;
      --themeColorBG6: #fff;
      --themeColorBG7: #fbfefb;
      --themeColorBT1: rgba(3, 199, 90, 0.12);
      --themeColorBT2: #009f47;
      --themeColorBT3: rgba(0, 118, 255, 0.1);
      --themeColorBT4: #f5f6f8;
      --themeColorBT5: #ebecef;
      --themeColorBT6: #e5e7ea;
      --themeColorBT7: #f5f6f8;
      --themeColorBT8: rgba(245, 53, 53, 0.06);
      --themeColorBT9: #a6a6a6;
      --themeColorBT10: rgba(0, 159, 71, 0.4);
      --themeColorBT11: #323232;
      --themeColorHC1: #ffff9f;
      --skinSpecialMenuWidth: 1px;
      --skinSpecialMenuBorder: #74bbbe;
      --skinSpecialMenuBg: #74bbbe;
      --skinSpecialMenuColor: #fff;
      --skinSpecialMenuDot: #fff;
      --skinTextColor: #000000;
      --skinText323232: #323232;
      --skinText676767: #676767;
      --skinText979797: #979797;
      --skinTextb7b7b7: #b7b7b7;
      --GnbLink: #333;
      --GnbLinkBar: #e5e5e5;
      --GnbLinkArrow: #777;
      --LayoutHeaderTitleBg: url(https://ssl.pstatic.net/static/cafe/cafe_pc/bg_default_title_white_180724.png);
      --LayoutHeaderTitleName: #212121;
      --LayoutHeaderTitleUrl: #666;
      --skinListBorder: #f2f2f2;
      --skinThumbBorder: rgba(0, 0, 0, 0.06);
      --skinBar: rgba(0, 0, 0, 0.15);
      --skinArticleLink: -webkit-link;
      --skinSourceBadge: #323232;
      --skinTagLinkColor: #323232;
      --skinToggleSwitchBg: #a7a7a7;
      --skinTempSaveBorder: rgba(0, 0, 0, 0.2);
      --skinCommentWriterBorder: rgba(0, 0, 0, 0.1);
      --skinCommentWriterBg: #ffffff;
      --skinCommentWriterText: #b7b7b7;
      --skinCommentWriterFocus: #eeeeee;
      --skinBox: #f9f9fa;
      --skinSvgIconPostBtnArrowUp: #323232;
      --skinSvgIconPostTop: #323232;
      --skinLayoutBorder: #ebecef;
      --skinBaseButtonDefaultBg: #eff0f2;
      --skinBaseButtonDefaultColor: #000000;
      --skinBaseButtonPointBg: rgba(3, 199, 90, 0.12);
      --skinBaseButtonPointColor: #009f47;
      --skinBgPostRefresh: url(https://ca-fe.pstatic.net/web-pc/static/img/ico-post-refresh.svg?f332c2e8d26157445fa480d35d5b0ef4);
      --skinSvgIconNpay: url(https://ca-fe.pstatic.net/web-pc/static/img/pc-ico-npay.svg?d0a9d54243d73135189ed084c99de534);
      --skinSvgIconSolidWriting: #009F47;
      --skinColor: #ffffff;
      --skinCommentMineBg: #f9f9fa;
      --skinListSelectedBg: #f9f9fa;
      --skinBaseButtonDefaultBorder: transparent;
      --skinCommentRefreshButtonBorder: transparent;
      --skinNoticeBadgeRequiredBg: #ffe3e4;
      --skinNoticeBadgeMenuBg: #ffffff;
      --skinNoticeBadgeMenuBorder: #ffe3e4;
      --skinNoticeBadgeColor: #f53535;
      -webkit-text-size-adjust: none;
      font-weight: 400;
      color: #333;
      font-size: 12px;
      margin: 12px 0 29px;
      padding: 16px 10px 10px 18px;
      border: 2px solid var(--skinCommentWriterBorder);
      border-radius: 6px;
      box-sizing: border-box;
      background: var(--skinCommentWriterBg);
    }

    textarea {
      padding: 10px;
      max-width: 100%;
      line-height: 1.5;
      border-radius: 5px;
      border: 1px solid #ccc;
      box-shadow: 1px 1px 1px #999;
    }

    /* 댓글 css */
    .reply_view {
      width: 825px;
      margin-top: 0px;
      word-break: break-all;
    }

    .dat_view {
      font-size: 14px;
      padding: 10px 0 15px 0;
      border-bottom: solid 1px #E0E0E0;
      width: 825px;
    }

    @media(max-width:500px) {
      .dat_view {
        font-size: 14px;
        padding: 10px 0 15px 0;
        border-bottom: solid 1px #E0E0E0;
        width: 280px;
      }
    }

    .dat_ins {
      margin-top: 50px;
    }

    .dat_edit_t {
      width: 520px;
      height: 70px;
      position: absolute;
      top: 40px;
    }

    .rep_con {
      width: 700px;
      height: 56px;
    }

    .rep_btn {
      position: absolute;
      width: 100px;
      height: 56px;
      font-size: 16px;
      margin-left: 10px;
    }

    .image {
      width: 300px;
    }

    @media(max-width:500px) {
      .image {
        width: 150px;
      }
    }

    .line {
      padding: 0px 0 15px 0;
      border-bottom: solid 1px #E0E0E0
    }

    @media(max-width:500px) {
      .line {
        padding: 0px 0 15px 0;
        border-bottom: solid 1px #E0E0E0;
        width: 280px;
      }
    }

    tr {
      cursor: pointer;
      background-color: white;
    }

    .dat_del_btn {
      color: #ccc;
    }
  </style>
</head>

<body>
  <div class="top">

    <div class="gallview_head clear ub-content">
      <!-- 모바일에서 작성 icon_write_mbl -->
      <h3 class="title ub-word">

        <span class="title_subject" style="font-size:x-large;"><?php echo $con_title ?></span>

        <!-- <em class="blind">앱에서 작성</em>
                                <em class="sp_img icon_write_app"></em> -->
        </span>
        </span>
      </h3>
      <br />
      <div class="gall_writer ub-writer" data-loc="view">
        <div class="fl">
          <span class="nickname" style="font-size:medium;"><em><?php echo $dept_user ?></em></span>
          <span class="gall_date" style="font-size:medium;"><?php echo $row[0]["con_datetime"] ?></span>
        </div>
        <div class="fr">
          <span class="gall_count" style="font-size:small;">조회 <?php echo $row[0]["con_vc"] ?></span>
          <span id="active_good" class="gall_reply_num" style="font-size:small;">좋아요 <?php echo $row[0]["con_good"] ?></span>
          <span class="gall_comment" style="font-size:small;"><a>댓글 <?php echo $row[7]["cnt"] ?></a></span>
        </div>
      </div>
    </div>
  </div>
  <div class="container">
    <div style="font-size:large; padding:10px">
      <h4><b>추천사원 : <?php echo $re_user ?></b></h4>
    </div>
    <div class="CommentWriter" style="font-size:large">
      <?php echo $con_body ?>
      <div style="font-size:medium"><?php echo $attach ?></div>
    </div>
    <br /><br />


    <div class="row">
      <?php echo $goodsave; ?>
      <div id="ajax_message" style="margin-left:45%; padding:5px"><b><?php echo $con_good ?></b></div>
    </div>
    <br /> <br />

    <div class="CommentWriter">
      <!-- 댓글 불러오기 -->
      <div>
        <div class="reply_view">
          <h3 class="line">댓글목록</h3>
          <?php echo $comment ?>
        </div>
      </div>
      <footer></footer>
      <!-- 댓글 불러오기 끝 -->
      <br />
      <form method="post" action="board_action.php?mode=comment" id="frm" name="frm">
        <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION["user_id"] ?>" />
        <input type="hidden" id="con_no" name="con_no" value="<?php echo $con_no ?>" />
        <input type="hidden" id="goodchk" name="goodchk" value="false" />
        <input type="hidden" id="com_en" name="com_en" value="<?php echo $row[8]["com_en"] ?>" />
        <input type="hidden" id="scroll" name="scroll" value="<?php echo $row[8]["scroll"] ?>" />
        <div class="comment_inbox">
          <strong style="font-size:14px"><?php echo $comment_user ?></strong>

          <em class="comment_inbox_name"></em>
          <br />
          <textarea placeholder="댓글을 남겨보세요" id="com_body" name="com_body" class="comment_inbox_text" style="overflow: hidden; overflow-wrap: break-word; height: 70px;"></textarea>
        </div>
        <br />
        <div class="register_box" style="text-align:right">
          <a role="button" onclick="comment()">등록</a>
        </div>
    </div>
    <br />
    <div class="table_position">
      <table class="table table-hover">
        <tbody>
          <?php echo $pre_tr;
          echo $next_tr ?>
        </tbody>
      </table>
    </div>
    <br /><br />
    <div class="button">
      <span class="btn2" style="width:min-content">
        <?php echo $modify_btn ?>
      </span>
      <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
      <span class="btn1" style="width:min-content">
        <input type="button" value="목록" onclick="goList()">
      </span>
      <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
      <span class="btn3" style="width:min-content">
        <?php echo $delete_btn ?>
        </form>

    </div>
    <br /><br />

  </div>
  <script type="text/javascript">
    window.onload = function() {
      if (document.getElementById("com_en").value == 'Y') {
        window.scrollTo({
          top: document.getElementById("scroll").value
        });
      }
    }

    function goList() {
      location.href = "<?php echo $list_uri ?>";
    }

    function goModify() { //== EDIT == UPDATE == MODIFY
      location.href = "<?php echo $modify_uri; ?>";
    }

    function goDelete() {
      var a = confirm("정말 삭제하시겠습니까?");
      if (a == true) {
        location.href = "<?php echo $delete_uri; ?>";
      }
    }

    function comment() {

      var com_body = document.getElementById("com_body").value
      if (trim(com_body) == '') {
        alert("내용을 입력하세요.");
        document.getElementById("com_body").focus();
      } else {
        var scrollPosition = window.scrollY || document.documentElement.scrollTop;
        document.getElementById("scroll").value = scrollPosition;
        document.getElementById("frm").submit();

      }
    }

    function trim(str) {
      //정규 표현식을 사용하여 화이트스페이스를 빈문자로 전환
      str = str.replace(/^\s*/, '').replace(/\s*$/, '');
      return str; //변환한 스트링을 리턴.
    }

    function modifyCmt(c) {
      var com_no = document.getElementById(c.getAttribute('id')).getAttribute('id');
      var com_body = document.getElementById("com_body" + com_no).value

      if (trim(com_body) == '') {
        alert("내용을 입력하세요.");
        document.getElementById("com_body" + com_no).focus();
      } else {
        var scrollPosition = window.scrollY || document.documentElement.scrollTop;
        document.getElementById("scroll").value = scrollPosition;
        document.getElementById("frm" + com_no).submit();
      }
    }

    function cmtDelete(c) {
      var com_no = document.getElementById(c.getAttribute('id')).getAttribute('id');
      var a = confirm("댓글을 삭제하시겠습니까?");
      if (a == true) {
        location.href = 'board_action.php?mode=cmt_delete&com_no=' + com_no + '&con_no=<?php echo $con_no ?>';
      }
    }

    function cmtModify(c) {
      var com_no = document.getElementById(c.getAttribute('id')).getAttribute('id');

      document.getElementById('register' + com_no).hidden = true;
      document.getElementById('modify' + com_no).hidden = false;
      document.getElementById("com_body" + com_no).focus();
    }

    function cmtCancel(c) {
      var com_no = document.getElementById(c.getAttribute('id')).getAttribute('id');


      document.getElementById('register' + com_no).hidden = false;
      document.getElementById('modify' + com_no).hidden = true;
    }

    function good(obj) {

      var xhttp = new XMLHttpRequest();

      // Define a callback function
      xhttp.onload = function() {
        // Here you can use the Data
        document.getElementById("ajax_message").innerHTML = "<b>" + this.responseText + "</b>";
        document.getElementById("active_good").innerHTML = "좋아요 " + this.responseText;
      }

      var no_good = document.getElementById("no_good").hidden;
      var good = document.getElementById("good").hidden;
      if (no_good == false) {
        document.getElementById("no_good").hidden = true;
        document.getElementById("good").hidden = false;
        document.getElementById("goodchk").value = "false";
      } else {
        document.getElementById("no_good").hidden = false;
        document.getElementById("good").hidden = true;
        document.getElementById("goodchk").value = "true";
      }

      xhttp.open("GET", "board_action.php?mode=good&user_id=" + document.getElementById("user_id").value + "&con_no=" + document.getElementById("con_no").value + "&goodchk=" + document.getElementById("goodchk").value);
      xhttp.send();
    }
  </script>
</body>

</html>
