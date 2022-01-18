<?php

session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);
require_once  _LIB_PATH_ . "nav_page.class.php";
include "_inc.php";

$nav = new NavPage;
$nav->start(10, 10);

$db = new DB;

$user_sql = "SELECT * FROM {$_table_user} WHERE user_id = '{$_SESSION["user_id"]}'";
$db->query($user_sql);
$db->next_record();
$row[1] = $db->Record;

// 게시글
$main_sql = "SELECT to_char(m.m_datetime,'YYYY.MM.DD HH24:mi:ss') as m_datetime, m.m_title, m.wr_user, de.dept_name, du.duty_name, u.user_name, m.is_read, m.mno, u.is_use, u.is_active
FROM ex_user_set u, ex_dept_set de, board_message m, ex_duty_set du
WHERE m.wr_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no and m.re_user = {$row[1]["uno"]}
ORDER BY mno desc";
$sql = "WITH A as (
{$main_sql}
)
SELECT COUNT(*) AS CNT FROM A";

$count = $db->query_one($sql);
$str_page_bar = $nav->navpage($count);
$sql = $main_sql;
$db->query_limit($sql, $nav->start_row, $nav->row_scale);
$query_row_count = $db->nf();

$str_data_row = null;
if ($query_row_count > 0) {
    $i = 0;
    while ($db->next_record()) {
        // 시간
        $row = $db->Record;
        $datetime = explode(" ", $row["m_datetime"]);
        $date = explode(".", $datetime[0]);
        $time = explode(":", $datetime[1]);
        $datetype = date("Y-m-d H:i:s", mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]));
        $backdate = date("Y-m-d H:i:s");

        $start_date = new DateTime($datetype);
        $end_date = new DateTime($backdate);

        $diff = date_diff($start_date, $end_date);
        if ($row["is_read"] == "Y") {

            $state = "읽음";

            $no = $count - $nav->start_row - $i;
            $date = substr($row["m_datetime"], 0, 10);
            if ($row["is_use"] == 'N' || $row["is_active"] == 'N') {
                $dept_user = "[" . $row["dept_name"] . "] " . $row["user_name"] . " " . $row["duty_name"] . "&nbsp;<img src=\"image/delete-user.png\" title=\"탈퇴한 계정\" width=\"25px\" height=\"25px\">";
            } else {
                $dept_user = "[" . $row["dept_name"] . "] " . $row["user_name"] . " " . $row["duty_name"];
            }
            $m_title = $row["m_title"];
            $list_query = Fun::getParamUrl();

            $str_data_row .= "              
                            <tr onclick=\"location.href='message_view.php?{$list_query}&mno={$row["mno"]}'\" style=\"cursor:pointer\">
                                <td style=\"text-align: center;\">{$no}</td>
                                <td>{$m_title}</td>
                                <td>{$dept_user}</td>
                                <td style=\"text-align: center;\">{$date}</td>
                                <td style=\"text-align: center;\">{$state}</td>
                            </tr>";
            $i++;
        } else {

            $state = "읽지않음";

            $no = $count - $nav->start_row - $i;
            $date = substr($row["m_datetime"], 0, 10);
            $dept_user = "[" . $row["dept_name"] . "] " . $row["user_name"] . " " . $row["duty_name"];
            $m_title = $row["m_title"] . " <img src=\"./image/read.png\" width=\"25px\" height=\"25px\" />";
            $list_query = Fun::getParamUrl();

            $str_data_row .= "              
                            <tr onclick=\"location.href='message_view.php?{$list_query}&mno={$row["mno"]}'\" style=\"cursor:pointer\" style=\"cursor:pointer\">
                                <td style=\"text-align: center;\">{$no}</td>
                                <td>{$m_title}</td>
                                <td>{$dept_user}</td>
                                <td style=\"text-align: center;\">{$date}</td>
                                <td style=\"text-align: center;\">{$state}</td>
                            </tr>";
            $i++;
        }
    }
} else {
    $str_data_row = '      
<tr>
    <td style="text-align: center;" colspan="5">쪽지함이 비었습니다.</td>
</tr>
';
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>쪽지함</title>
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

    <!-- 부가적인 테마 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

    <!-- 합쳐지고 최소화된 최신 자바스크립트 -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <style>
        /* 쪽지함 전체영역의 가로사이즈와 가운데 정렬 */
        body {
        font-family: 'Gowun Dodum', sans-serif;
        }
        #message_box {
            width: 900px;
            margin: 0 auto;
        }

        /* 제목영역 */
        #message_box h3 {
            margin-top: 30px;
            padding: 10px;
            border-bottom: solid 2px #516e7f;
            font-size: 20px;
        }

        /* 쪽지함 리스트 항목마다 아래 경계선 */
        #message li {
            padding: 10px;
            border-bottom: solid 1px #dddddd;
        }

        /* 제목줄 글씨는 굵게 */
        #message li:nth-child(1) {
            font-weight: bold;
        }

        /* span은 사이즈 조절이 안되서 */
        #message span {
            display: inline-block;
            text-align: center;
        }

        /* 각 칸(컬룸)마다 가로사이즈 지정 */
        #message .col1 {
            width: 80px;
        }

        #message .col2 {
            width: 410px;
            text-align: left;
        }

        #message .col3 {
            width: 120px;
        }

        #message .col4 {
            width: 150px;
        }

        /* 페이지 네이션 가운데 정렬 및 옆으로 배치 */
        #page_num {
            text-align: center;
            margin: 30px 0;
        }

        #page_num li {
            display: inline;
        }

        /* 버튼들... 오른쪽 정렬 */
        #message_box .buttons {
            text-align: right;
            margin: 20px 0 40px 0;
        }

        /* 버튼들 옆으로 배치 */
        #message_box .buttons li {
            display: inline;
        }

        /* 버튼들의 글씨가 버튼박스에 너무 꽉차있어서 안이쁨. 그래서 패딩 */
        #message_box .buttons button {
            padding: 5px 10px;
            cursor: pointer;
        }

        input[type=button] {
            background-color: white;
            box-shadow: rgba(30, 22, 54, 0.4) 0 0px 0px 2px inset;
            color: rgba(30, 22, 54, 0.6);
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        @media(max-width:500px) {
            input[type=button] {
            background-color: white;
            box-shadow: rgba(30, 22, 54, 0.4) 0 0px 0px 2px inset;
            padding: 5px 10px;
            color: rgba(30, 22, 54, 0.6);
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        }

        input[type=button]:hover {
            color: rgba(255, 255, 255, 0.85);
            background-color:rgba(255, 255, 255, 0.85);
            box-shadow: rgba(30, 22, 54, 0.7) 0 80px 0px 2px inset;

        }
    </style>
</head>

<body>

    <section>
        <div id="main_content">
            <div id="message_box">
            <h3>수신 쪽지함 > 목록보기</h3>

                <!-- 쪽지 리스트가 보여지는 영역(게시판 모양) -->
                <!-- <div>
                        <ul id="message">
                            리스트의 제목줄
                            <li>
                                <span class="col1">번호</span>
                                <span class="col2">제목</span>
                                <span class="col3">보낸이</span>
                                <span class="col4">등록일</span>
                            </li>
                    </div> -->

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="cols" style="width: 20px; text-align :center;">NO.</th>
                            <th scope="cols" style="width: 525px;">TITLE</th>
                            <th scope="cols" style="width: 360px;">보낸이</th>
                            <th scope="cols" style="width: 250px; text-align :center;">등록일</th>
                            <th scope="cols" style="width: 100px; text-align:center;">상태</th>
                            <!-- <th scope="cols" style="width: 80px; text-align:center;">좋아요</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $str_data_row; ?>
                    </tbody>
                </table>
                <br />
                <div style="text-align:center"><?php echo $str_page_bar?></div><br />
                <span><input type="button"  value="발신 쪽지함"  onclick="goMessage()" style="margin-left:75%; font-size:small; width:min-content"></span>
                <span><input type="button"  value="쪽지 보내기"  onclick="goSend()" style="margin-left:4%; font-size:small; width:min-content"></span>
            </div>
        </div>
    </section>
    <script>
        function goSend(){
            location.href = 'message_send.php';s
        }
        function goMessage(){
            location.href = 'send_list.php'; 
        }
    </script>
</body>

</html>
