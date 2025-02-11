<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

$con_no = $_REQUEST["con_no"];
$db = new DB;
//$db->Debug = true;
$main_sql = "SELECT b.con_datetime, b.con_title, u.user_name, de.dept_name, b.con_vc , du.duty_name, b.con_body
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
$modify_uri = "board_write.php?" . $query_string . "&con_no={$con_no}&mode=modify&re_user={$re_uno}&re_dept={$re_dept}";
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="ko"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="ko"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="ko">
<!--<![endif]-->

<head>
  <style>
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
      background-color: #4CAF50;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-left: 40%;
    }

    input[type=button]:hover {
      background-color: #45a049;

    }

    .container {
      border-radius: 15px;
      background-color: #f2f2f2;
      padding: 80px;
      max-width: 900px;
      margin-left: 27%;
      margin-top: 5%;
    }

    .col-25 {
      float: left;
      width: 10%;
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
      <div class="row">
        <div class="col-75">
          <textarea id="subject" name="subject" style="height:200px" disabled readonly><?php echo $row[0]['con_body'] ?></textarea>
        </div>
      </div>
      <br> <br>
      <div class="row">
        <input type="button" value="목록" onclick="goList()">
        <input type="button" value="수정" onclick="goModify()">
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
  </script>
</body>

</html>
