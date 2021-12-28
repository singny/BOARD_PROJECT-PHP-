<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
include "_inc.php";
@error_reporting(E_ALL);
if($_REQUEST["con_no"]){
$con_no = $_REQUEST["con_no"];
$db = new DB;
//$db->Debug = true;
$main_sql = "SELECT b.con_datetime, b.con_title, u.user_name, de.dept_name, b.con_vc , du.duty_name, b.con_body, u.user_id
            FROM ex_user_set u, ex_dept_set de, board_contents b, ex_duty_set du
            WHERE b.wr_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no and b.con_no={$con_no}";
$sql = "WITH A as (
    {$main_sql}
)
SELECT COUNT(*) AS CNT FROM A";
$sql = $main_sql;
$db->query($sql);
$db->next_record();
$row[0] = $db->Record;

$date = substr($row[0]["con_datetime"], 0, 10);
$dept_user = "[" . $row[0]["dept_name"] . "] " . $row[0]["user_name"] . " " . $row[0]["duty_name"];
$con_title = $row[0]["con_title"];

$re_sql = "SELECT u.user_name, de.dept_name, du.duty_name, u.uno, de.dept_no
FROM ex_user_set u, ex_dept_set de, board_contents b, ex_duty_set du
WHERE b.re_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no and b.con_no={$con_no}";

$db->query($re_sql);
$db->next_record();
$row[1] = $db->Record;

$re_user = "[" . $row[1]["dept_name"] . "] " . $row[1]["user_name"] . " " . $row[1]["duty_name"];

$re_uno = $row[1]["uno"];
$re_dept = $row[1]["dept_no"];
$query_string = Fun::getParamUrl("con_no");
$list_uri = "board_list.php?" . $query_string;
$modify_uri = "board_write.php?" . $query_string . "&con_no={$con_no}&mode=modify&re_user={$re_uno}";
$delete_uri = "board_action.php?mode=delete" . $query_string . "&con_no={$con_no}";

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


if(!$row[3]){
  $view_sql = "INSERT INTO VIEW_COUNT(vc_no, con_no, user_id) VALUES($vc_no, $con_no,'{$_SESSION["user_id"]}')";
  $db->query($view_sql);
  $vc_sql = "UPDATE {$_table_board} SET con_vc= con_vc + 1 WHERE con_no = {$con_no}";
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
if($file_name){

  $attach = "<div class=\"row\">
  <div class=\"col-75\">
    <h4>첨부파일  &nbsp;&nbsp;<img src=\"./image/folder.png\" style=\"width:20px;height:20px;\">&nbsp;$file_name&nbsp;&nbsp;&nbsp;<a href=\"download.php?con_no={$con_no}\">다운로드</a></h4>
    </div>
  </div>";
}

// 좋아요
$db->query("SELECT COUNT(*) AS CNT FROM GOOD_COUNT WHERE USER_ID = '{$_SESSION["user_id"]}' AND CON_NO = {$con_no}");
$db->next_record();
$row[4] = $db->Record;
$n = $row[4]["CNT"];

if($n == 0){
  $good_no = $db->nextid("SEQ_" . "GOOD_COUNT");
  $good_sql = "INSERT INTO GOOD_COUNT(GOOD_NO,CON_NO,USER_ID,IS_GOOD) VALUES({$good_no},{$con_no},'{$_SESSION["user_id"]}','N')";
  $db->query($good_sql);
  $db->commit();
}

}
else{
  FUN::alert("잘못된 접근입니다.","board_list.php");
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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Gowun+Dodum&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Gowun Dodum', sans-serif;
    }

    * {
      box-sizing: border-box;
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
      background-color: #6495ED;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;

    }

    input[type=button]:hover {
      background-color: #4682B4;

    }

    .container {
      border-radius: 15px;
      background-color: #f2f2f2;
      padding: 80px;
      max-width: 900px;
      margin-left: 27%;
      margin-top: 2.5%;
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
  </style>
</head>

<body>
  <div class="container">
    <form>
      <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION["user_id"]?>" />
      <input type="hidden" id="con_no" name="con_no" value="<?php echo $con_no?>" />
      <h2><?php echo $con_title ?></h2>
      <div class="row">
        <div class="col-50">
          <h4>작성자 : <?php echo $dept_user ?></h4>
        </div>
        <div class="col-50">
          <h4>작성일 : <?php echo $row[0]["con_datetime"] ?></h4>
        </div>
      </div>
      <div class="row">
        <div class="col-75">
          <h4>추천사원 : <?php echo $re_user ?></h4>
        </div>
      </div>
      <?php echo $attach ?>
      <div class="row">
        <div class="col-75">
          <textarea id="subject" name="subject" style="height:200px" disabled readonly><?php echo $row[0]['con_body'] ?></textarea>
        </div>
      </div>
      <br /><br />
      <div class="row">
        <div style="margin-left:42%; cursor:pointer" id="good" name="good" onclick="good(this)"> 
          <img src="./image/no_good.png" id="no_good" name="no_good" style="width:60px; height:60px"/>
        </div>
      </div>
      <br /><br /> 
      <div class="row" style="text-align:center; padding-right:80px;">
        <span class="btn2" style="width:min-content">
          <?php echo $modify_btn ?>
        </span>
        <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="btn1" style="width:min-content">
          <input type="button" value="목록" onclick="goList()">
        </span>
        <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span class="btn3" style="width:min-content">
          <?php echo $delete_btn ?>
        </span>
      </div>
    </form>
  </div>
  <script type="text/javascript">
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

    function good(obj) {


      var xhttp = new XMLHttpRequest();
      // Define a callback function
      xhttp.onload = function() {
        // Here you can use the Data
        alert(this.responseText);
        if (this.responseText == "Yes") {
          document.getElementById("no_good").src = "./image/good.png";
        } else if (this.responseText == "No") {
          document.getElementById("no_good").src = "./image/no_good.png";
        } 
        
      }
      
      // Send a request
      xhttp.open("GET", "board_action.php?mode=good&user_id=" + document.getElementById("user_id").value + "&con_no=" + document.getElementById("con_no").value);
      xhttp.send();
    }

  </script>
</body>

</html>
