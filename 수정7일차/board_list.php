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
$mWhere = null;
$sel_opt = array();

@$s_word = htmlspecialchars(@$post["s_word"]);

$search_options = array(
    "user_id" => array("col" => "B.CON_TITLE", "text" => "TITLE"), "user_name" => array("col" => "U.USER_NAME", "text" => "사용자 명"), "dept_name" => array("col" => "DE.DEPT_NAME", "text" => "부서 명"), "duty_name" => array("col" => "DU.DUTY_NAME", "text" => "직위 명")
);
$search_options_html = "";
foreach ($search_options as $_col => $_txt) {
    $opt_sel = "";
    if (@$post["s_type"] == $_col) {
        $opt_sel = " selected";
        $mWhere = " AND {$_txt["col"]} LIKE '%{$s_word}%'";
    }
    $search_options_html .= "<option value=\"{$_col}\" {$opt_sel}>{$_txt["text"]}</option>";
}

if (@$post["mode"] == "search" && @trim($s_word) != "") {
    $col_name = null;
    if ($post["s_type"] == "all") {
        $sel_opt["all"] = " selected";
        $mWhere = " AND (";
        $mWhere .= " U.USER_NAME LIKE '%{$s_word}%'";
        $mWhere .= " OR B.CON_TITLE LIKE '%{$s_word}%'";
        $mWhere .= " OR DE.DEPT_NAME LIKE '{$s_word}%'";
        $mWhere .= " OR DU.DUTY_NAME = '{$s_word}%'";
        $mWhere .= ")";
    }
}

if(@$post["order"] == "con_good"){
    $order = "ORDER BY con_good desc";
} else if(@$post["order"] == "con_vc"){
    $order = "ORDER BY con_vc desc";
} else {
    $order = "ORDER BY con_datetime desc";
}

$main_sql = "SELECT to_char(b.con_datetime,'YYYY.MM.DD HH24:mi:ss') as con_datetime, b.con_title, u.user_name, de.dept_name, b.con_vc , du.duty_name, b.con_no, b.con_good, b.con_comment, u.uno
            ,b.file_path, u.is_use, u.is_active
            FROM ex_user_set u, ex_dept_set de, {$_table_board} b, ex_duty_set du
            WHERE b.wr_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no
            {$mWhere}
            {$order}";
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
        $datetime = explode(" ", $row["con_datetime"]);
        $date = explode(".", $datetime[0]);
        $time = explode(":", $datetime[1]);
        $datetype = date("Y-m-d H:i:s", mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0]));
        $backdate = date("Y-m-d H:i:s");

        $start_date = new DateTime($datetype);
        $end_date = new DateTime($backdate);

        $diff = date_diff($start_date, $end_date);

        if ($diff->days >= 1) {
            $no = $count - $nav->start_row - $i;
            $date = substr($row["con_datetime"], 0, 10);
            if ($row["file_path"]) {
                $con_title = Fun::convDB2Val($row["con_title"]) . " [" . $row["con_comment"] . "]&nbsp;<img src=\"./image/folder.png\" style=\"width:17.5px;height:17.5px;\">";
            } else {
                $con_title = Fun::convDB2Val($row["con_title"]) . " [" . $row["con_comment"] . "]";
            }
            $list_query = Fun::getParamUrl();
            
            if ($row["is_use"] == 'N' || $row["is_active"] == 'N') {
                $dept_user = "[" . $row["dept_name"] . "] " . $row["user_name"] . " " . $row["duty_name"] . "&nbsp;<img src=\"image/delete-user.png\" title=\"탈퇴한 계정\" width=\"25px\" height=\"25px\">";
                $str_data_row .= "              
                <tr>
                    <td style=\"text-align: center;\">{$no}</td>
                    <td style=\"text-align: center;\">{$date}</td>
                    <td onclick=\"location.href='board_view.php?{$list_query}&con_no={$row["con_no"]}'\" style=\"cursor:pointer\">{$con_title}</td>
                    <td id=\"{$row["uno"]}\" name = \"{$row["uno"]}\" onclick=\"dropDown(this)\" style=\"cursor:pointer\">{$dept_user}</td>
                    <td style=\"text-align: center;\">{$row["con_vc"]}</td>
                    <td style=\"text-align: center;\">{$row["con_good"]}</td>
                </tr>";
            } else {
                $dept_user = "[" . $row["dept_name"] . "] " . $row["user_name"] . " " . $row["duty_name"];
                $str_data_row .= "              
                <tr>
                    <td style=\"text-align: center;\">{$no}</td>
                    <td style=\"text-align: center;\">{$date}</td>
                    <td onclick=\"location.href='board_view.php?{$list_query}&con_no={$row["con_no"]}'\" style=\"cursor:pointer\">{$con_title}</td>
                    <td id=\"{$row["uno"]}\" name = \"{$row["uno"]}\" onclick=\"dropDown(this)\" style=\"cursor:pointer\">{$dept_user}
                        <ul class=\"contextmenu\" id=\"menu{$row["uno"]}\" name = \"menu{$row["uno"]}\">
                            <div style=\"background-color:#CCCCFF\">$dept_user</div>
                            <li><a href=\"your_page.php?uno={$row["uno"]}\">프로필보기</a></li>
                            <li><a id=\"{$row["uno"]}\" name = \"{$row["uno"]}\" onclick=\"sendMessage(this)\">쪽지보내기</a></li>
                        </ul>
                    </td>
                    <td style=\"text-align: center;\">{$row["con_vc"]}</td>
                    <td style=\"text-align: center;\">{$row["con_good"]}</td>
                </tr>";
            }
            $i++;
        } else {
            $no = $count - $nav->start_row - $i;
            $date = substr($row["con_datetime"], 0, 10);
            if ($row["file_path"]) {
                $con_title = Fun::convDB2Val($row["con_title"]) . " <img src=\"./image/new.png\" width=\"25px\" height=\"25px\" /> [" . $row["con_comment"] . "]&nbsp;<img src=\"./image/folder.png\" style=\"width:17.5px;height:17.5px;\">";
            } else {
                $con_title = Fun::convDB2Val($row["con_title"]) . " <img src=\"./image/new.png\" width=\"25px\" height=\"25px\" /> [" . $row["con_comment"] . "] ";
            }
            $list_query = Fun::getParamUrl();
            
            if ($row["is_use"] == 'N' || $row["is_active"] == 'N') {
                $dept_user = "[" . $row["dept_name"] . "] " . $row["user_name"] . " " . $row["duty_name"] . "&nbsp;<img src=\"image/delete-user.png\" title=\"탈퇴한 계정\" width=\"25px\" height=\"25px\">";
                $str_data_row .= "              
                <tr>
                    <td style=\"text-align: center;\">{$no}</td>
                    <td style=\"text-align: center;\">{$date}</td>
                    <td onclick=\"location.href='board_view.php?{$list_query}&con_no={$row["con_no"]}'\" style=\"cursor:pointer\">{$con_title}</td>
                    <td id=\"{$row["uno"]}\" name = \"{$row["uno"]}\" style=\"cursor:pointer\">{$dept_user}</td>
                    <td style=\"text-align: center;\">{$row["con_vc"]}</td>
                    <td style=\"text-align: center;\">{$row["con_good"]}</td>
                </tr>";
            } else {
                $dept_user = "[" . $row["dept_name"] . "] " . $row["user_name"] . " " . $row["duty_name"];
                $str_data_row .= "              
                <tr>
                    <td style=\"text-align: center;\">{$no}</td>
                    <td style=\"text-align: center;\">{$date}</td>
                    <td onclick=\"location.href='board_view.php?{$list_query}&con_no={$row["con_no"]}'\" style=\"cursor:pointer\">{$con_title}</td>
                    <td id=\"{$row["uno"]}\" name = \"{$row["uno"]}\" onclick=\"dropDown(this)\" style=\"cursor:pointer\">{$dept_user}
                        <ul class=\"contextmenu\" id=\"menu{$row["uno"]}\" name = \"menu{$row["uno"]}\">
                            <div style=\"background-color:#CCCCFF\">$dept_user</div>
                            <li><a href=\"your_page.php?uno={$row["uno"]}\">프로필보기</a></li>
                            <li><a id=\"{$row["uno"]}\" name = \"{$row["uno"]}\" onclick=\"sendMessage(this)\">쪽지보내기</a></li>
                        </ul>
                    </td>
                    <td style=\"text-align: center;\">{$row["con_vc"]}</td>
                    <td style=\"text-align: center;\">{$row["con_good"]}</td>
                </tr>";
            }
            $i++;
        }
    }
} else {
    $str_data_row = '      
            <tr>
                <td style="text-align: center;" colspan="5">글을 찾을 수 없습니다.</td>
            </tr>
          ';
}

$hello = null;
if (@$_SESSION["user_id"]) {
    $user_name_sql = "SELECT * FROM EX_USER_SET WHERE user_id='{$_SESSION["user_id"]}'";
    $db->query($user_name_sql);

    while ($db->next_record()) {
        $row[1] = $db->Record;
    }

    $hello = $row[1]["user_name"] . "님, 안녕하세요";
} else {

    FUN::alert("로그인 후 접속하세요", "board_login.php");
}

$user_mng = null;
if (@$_SESSION["user_id"] == "admin") {
    $user_mng = "<li style=\"padding-top:15px\"><a href=\"./admin/user_list.php\" >회원관리</a></li>";
};

//view scroll 초기화
$scroll_sql = "UPDATE board_comment SET com_en = 'N'";
$db->query($scroll_sql);
$db->commit();


// 유저 사진
$file_sql = "SELECT UNO, FILE_PATH FROM {$_table_user} WHERE user_id='{$_SESSION["user_id"]}'";
$db->query($file_sql);
while ($db->next_record()) {
    $row[2] = $db->Record;
    $uno = $row[2]["uno"];
}

@$file_name = basename($row[2]["FILE_PATH"]);
$user_img = null;

if ($file_name) {
    $user_img = "<div class=\"circle \" onclick=\"location.href='my_page.php';\" style=\"background-image: url('./upload_file/user/{$uno}/$file_name');\" title=\"마이페이지\"></div>";
} else {
    $user_img = "<div class=\"circle \" onclick=\"location.href='my_page.php';\" style=\"background-image: url('./image/user.png')\" title=\"마이페이지\"></div>";
}

// 쪽지함
$message_sql = "SELECT COUNT(*) FROM {$_table_message} WHERE re_user = {$row[2]["uno"]} and is_read='N'";
$n = $db->query_one($message_sql);

if ($n == 0) {
    $message = "쪽지함[0]";
    $li_message = "쪽지함[0]";
} else {
    $message = "<img src=\"./image/message.png\" width=\"20px\" height=\"20px\" /> 쪽지함[" . $n . "]";
    $li_message = "쪽지함[" . $n . "]";
}

// 알림 기능
$alert_sql = "SELECT b.con_no, b.con_title, de.dept_name, du.duty_name, u.user_name, a.is_read, to_char(a.a_datetime,'YYYY.MM.DD HH24:mi:ss') as a_datetime, u.is_active, u.is_use
                FROM board_contents b, board_alert a, ex_user_set u , ex_dept_set de, ex_duty_set du
                WHERE b.re_user = {$_SESSION["uno"]} and b.wr_user = a.wr_user and b.con_no = a.con_no and b.wr_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no
                ORDER BY con_datetime DESC";
$db->query($alert_sql);
$alert_data_row = null;
while ($db->next_record()) {
    $row[3] = $db->Record;
    if ($row[3]["is_use"] == 'N' || $row[3]["is_active"] == 'N') {
        $wr_user = "[" . $row[3]["dept_name"] . "] " . $row[3]["user_name"] . " " . $row[3]["duty_name"] . "&nbsp;<img src=\"image/delete-user.png\" title=\"탈퇴한 계정\" width=\"25px\" height=\"25px\">";
    } else {
        $wr_user = "[" . $row[3]["dept_name"] . "] " . $row[3]["user_name"] . " " . $row[3]["duty_name"];
    }

    if ($row[3]["is_read"] == 'N') {
        $alert_data_row .= "<tr onclick=\"location.href='board_view.php?{$list_query}&con_no={$row[3]["con_no"]}'\" style=\"cursor:pointer\">
                                <td>{$wr_user}께서 회원님을 추천하는 글을 작성하였습니다.<br /><br />글 제목 : {$row[3]["con_title"]}<br />{$row[3]["a_datetime"]}</td>
                                <td><img src=\"image/read.png\" width=\"30px\" height=\"30px\"></td>
                            </tr>";
    } else {
        $alert_data_row .= "<tr onclick=\"location.href='board_view.php?{$list_query}&con_no={$row[3]["con_no"]}'\" style=\"cursor:pointer\">
                                <td>{$wr_user}께서 회원님을 추천하는 글을 작성하였습니다.<br /><br />글 제목 : {$row[3]["con_title"]}<br />{$row[3]["a_datetime"]}</td>
                                <td>&nbsp;</td>
                            </tr>";
    }
}

$art_sql = "SELECT COUNT(*) FROM {$_table_alert} WHERE uno = {$_SESSION["uno"]} and is_read='N'";
$an = $db->query_one($art_sql);

if ($an == 0) {
    $alert = "<img src=\"./image/nobell.png\" width=\"20px\" height=\"20px\" /> 알림창[" . $an . "]";;
} else {
    $alert = "<img src=\"./image/bell.png\" width=\"20px\" height=\"20px\" /> 알림창[" . $an . "]";
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="ko"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="ko"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="ko">
<!--<![endif]-->
<html>

<head>
    <meta property="og:url" content="http://separk2111.htenc.com">
    <meta property="og:title" content="(주) 하이테크엔지니어링">
    <meta property="og:type" content="website">
    <meta property="og:image" content="http://separk2111.htenc.com/image/thumbnail.png">
    <meta property="og:description" content="로그인하세요">
    <!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <script src="jquery/jquery.js"></script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- 합쳐지고 최소화된 최신 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

    <!-- 부가적인 테마 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

    <!-- 합쳐지고 최소화된 최신 자바스크립트 -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <!-- <script src="js/bootstrap.min.js"></script> -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"> -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&family=Nanum+Pen+Script&family=Poor+Story&display=swap" rel="stylesheet">
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
    <style>
        body {
            font-family: 'Jua', sans-serif;
            /* background-image: url('/image/love.gif'); */
            /* background-repeat: no-repeat; */
            background-position: center;
            /* background-size: cover; */
            font-size: medium;
        }

        @media(max-width:500px) {
            body {
                font-family: 'Jua', sans-serif;
                font-size: xx-small;
            }

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
            background-color: rgba(255, 255, 255, 0.85);
            box-shadow: rgba(30, 22, 54, 0.7) 0 80px 0px 2px inset;

        }

        .col-25 {
            float: left;
            width: 4.5%;
            margin-top: 7px;
            margin-left: 35%;
        }

        @media(min-width:1900px) {
            .col-25 {
                float: left;
                width: 5%;
                margin-top: 7px;
                margin-left: 37%;
            }
        }

        @media(max-width:500px) {
            .col-25 {
                float: left;
                width: 10%;
                margin-top: 7px;
                margin-left: 55px;
            }
        }

        .col-75 {
            float: left;
            width: 37%;
            margin-top: 6px;
            margin-bottom: 20px;
        }

        @media(min-width:1900px) {
            .col-75 {
                float: left;
                width: 26.5%;
                margin-top: 6px;
            }
        }

        @media(max-width:500px) {
            .col-75 {
                float: left;
                width: 89%;
                margin-top: 6px;
                margin-left: auto;
            }
        }

        .col-50 {
            float: left;
            width: 1%;
            margin-top: 6px;
        }

        .col-10 {
            float: left;
            width: 2%;
            margin-top: 6px;
        }

        .page {
            margin-left: 16.5%;
            background-image: url('./image/cute.gif');
            padding: 8px;
            border-radius: 4px;
        }

        @media(min-width:1900px) {
            .page {
                margin-left: 23.5%;
                background-image: url('./image/cute.gif');
                padding: 8px;
                border-radius: 4px;
            }
        }

        @media(max-width:500px) {
            .page {
                margin-left: 15px;
                background-image: url('./image/cute.gif');
                padding: 4px;
                border-radius: 4px;
            }
        }

        .write {
            text-align: right;
            margin-right: 16.5%
        }

        @media(min-width:1900px) {
            .write {
                text-align: right;
                margin-right: 23.5%
            }
        }

        @media(max-width:500px) {
            .write {
                text-align: right;
                margin-right: 10%
            }
        }

        .circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            float: right;
            margin-right: 0px;
            border: 3px solid #fff;
            box-shadow: 0 0 16px rgb(221, 221, 221);
            cursor: pointer;
            background-size: 47.5px 50px;
            margin-bottom: 15px;

        }

        .bar {
            margin-right: 20%
        }

        @media(min-width:1900px) {
            .bar {
                margin-right: 25%
            }
        }

        .logo {
            max-width: 900px;
            padding-top: 1%;
        }

        @media(max-width:500px) {
            .logo {
                max-width: 360px;
                padding: 15px;
            }
        }

        .s_type {
            width: 175px;
            height: 30px;
            margin-left: 0px;
        }

        @media(max-width:500px) {
            .s_type {
                width: 115px;
                height: 20px;
                margin-left: 0px;
            }
        }

        .table_position {
            margin-left: 15%;
            margin-right: 15%;
        }

        @media(min-width:1900px) {
            .table_position {
                margin-left: 22%;
                margin-right: 22%;
            }
        }

        @media(max-width:500px) {
            .table_position {
                margin-left: 3.5%;
                margin-right: 0%;
            }
        }

        a.button,
        a.button2 {
            -webkit-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
            -moz-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
            -ms-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
            -o-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
            transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
            display: block;
            /* margin: 20px auto; */
            width: 100px;
            text-decoration: none;
            border-radius: 4px;
            padding: 5px;
            text-align: center;
            cursor: pointer;
        }

        @media(max-width:500px) {
            a.button2 {
                -webkit-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
                -moz-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
                -ms-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
                -o-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
                transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
                display: block;
                /* margin: 20px auto; */
                width: 45px;
                text-decoration: none;
                border-radius: 4px;
                padding: 5px;
                text-align: center;
                cursor: pointer;
            }
        }

        a.button2 {
            color: rgba(30, 22, 54, 0.6);
            box-shadow: rgba(30, 22, 54, 0.4) 0 0px 0px 2px inset;
        }

        a.button2:hover {
            color: rgba(255, 255, 255, 0.85);
            box-shadow: rgba(30, 22, 54, 0.7) 0 80px 0px 2px inset;
        }

        .order {
            margin-left: 600px;
        }

        @media(max-width:500px) {
            .order {
                margin-left: 180px;
            }
        }

        .H {
            width: 50px;
            height: 50px;
        }

        @media(max-width:500px) {
            .H {
                width: 20px;
                height: 20px;
            }
        }

        .contextmenu {
            display: none;
            position: absolute;
            width: 200px;
            margin: 0;
            padding: 0;
            background: #FFFFFF;
            border-radius: 5px;
            list-style: none;
            box-shadow:
                0 15px 35px rgba(50, 50, 90, 0.1),
                0 5px 15px rgba(0, 0, 0, 0.07);
            overflow: hidden;
            z-index: 999999;
        }

        .contextmenu li {
            border-left: 3px solid transparent;
            transition: ease .2s;
        }

        .contextmenu li a {
            display: block;
            padding: 10px;
            color: #B0BEC5;
            text-decoration: none;
            transition: ease .2s;
        }

        .contextmenu li:hover {
            background: #CE93D8;
            border-left: 3px solid #9C27B0;
        }

        .contextmenu li:hover a {
            color: #FFFFFF;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div><a class="navbar-brand" href="board_list.php">
                        <img class="H" alt="Brand" src="image/letter-h.png">
                    </a></div>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li class="active" style="padding-top:15px"><a href="board_list.php">우수직원 추천 게시판 <span class="sr-only">(current)</span></a></li>
                    <li style="padding-top:15px"><a href="board_chart.php">우수직원 순위현황</a></li>
                    <?php echo $user_mng ?>
                    <!-- <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li class="divider"></li>
            <li><a href="#">One more separated link</a></li>
          </ul>
        </li> -->
                </ul>
                <!-- <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form> -->
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#"><?php echo $user_img ?></a></li>
                    <li><span style=" padding-top:30px;padding-right:15px;border-radius: 4px; float:right; background-image:url(image/love.gif)"><b><?php echo $hello ?></b></li>
                    <li>
                        <div style="float:right; padding-top:22px;padding-right:10px"><a class="button2" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal"><?php echo $alert ?></a></div>
                    </li>
                    <li>
                        <div style="float:right; padding-top:22px"><a class="button2" onclick="goMessage()"><?php echo $message ?></a></div>
                    </li>
                    <li>
                        <div style="float:right;padding-top:22px;padding-left:10px;padding-right:20px;"><a class="button2" onclick="logout()">로그아웃</a></div>
                    </li>

                    <!-- Button trigger modal -->


                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">알림창</h4>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-hover">
                                        <tbody>
                                            <?php echo $alert_data_row; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
          </ul>
        </li> -->
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    <!-- <ul>
        <li><img src="./image/welcome2.png"></li>
        <li>&nbsp;</li>
        <li><a class="active" href="board_list.php">우수직원 게시판</a></li>
        <li><a href="board_chart.php">우수직원 순위현황</a></li>
        <?php //echo $user_mng 
        ?>
    </ul> -->
    <div style="height:900px;width:98%">
        <br />
        <div class="bar">
            <!-- <div style="float:right">&nbsp;<input type="button" onclick="logout()" value="로그아웃" /></div> -->
            <!-- <div style="float:right"><a class="button2" onclick="logout()">로그아웃</a></div>
            <div style="float:right; padding-right:8px"><a class="button2" onclick="goMessage()"><?php echo $message ?></a></div>

            <span style=" padding:8px;border-radius: 4px; float:right; background-image:url(image/love.gif)"><b><?php echo $hello ?></b></span>
            <?php //echo $user_img 
            ?> -->
            <!-- <div class="circle " onclick="location.href='my_page.php';" title="마이페이지"></div> -->
        </div>
        <br /><br /><br />
        <div style="text-align:center; cursor:pointer;">
            <img src="./image/logo3.png" class="logo" onclick="location.href='board_list.php'" />
        </div>
        <br />
        <span class="page">
            <b><?php
                echo "총 게시물 : {$nav->total_row} / 전체 페이지 : {$nav->total_page}";
                ?></b>
        </span>
        <span class="order">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-default" onclick="orderConvc()" style="font-size:small;">조회수</button>
                <button type="button" class="btn btn-default" onclick="goList()" style="font-size:small;">최신순</button>
                <button type="button" class="btn btn-default" onclick="orderGood()" style="font-size:small;">좋아요</button>
            </div>
        </span>
        <br /><br />
        <div class="table_position">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="cols" style="width: 20px; text-align :center;">NO.</th>
                        <th scope="cols" style="width: 250px; text-align :center;">작성일</th>
                        <th scope="cols" style="width: 525px;">TITLE</th>
                        <th scope="cols" style="width: 400px;">작성자</th>
                        <th scope="cols" style="width: 80px; text-align:center;">조회수</th>
                        <th scope="cols" style="width: 80px; text-align:center;">좋아요</th>
                </thead>
                <tbody>
                    <?php echo $str_data_row; ?>
                </tbody>
            </table>
        </div>
        <div class="write">
            <input type="button" value="글쓰기" onclick="goWrite()" style="margin-left:96%">
        </div>

        <div style="text-align:center;">
            <?php echo $str_page_bar ?>
        </div>
        <br />
        <div style="text-align:center;">
            <form id="frmSearch" name="frmSearch" method="get" action="<?php echo $_SERVER["SCRIPT_NAME"] ?>" onsubmit="goSearch(this); return false;">
                <input type="hidden" id="mode" name="mode" value="search" />
                <div class="row">
                    <div class="col-25">
                        <select class="s_type" id="s_type" name="s_type" aria-label=".form-select-sm example">
                            <option value="all" <?php echo @$sel_opt["all"]; ?>>전체</option>
                            <?php echo $search_options_html; ?>
                        </select>
                    </div>
                    <div class="col-75">
                        <input type="text" id="s_word" name="s_word" value="<?php echo @trim($s_word); ?>" />
                        <input type="button" id="btnSearch" name="btnSearch" value=" 검색 " onclick="goSearch(this.form);" />
                        <input type="button" id="btnSearch" name="btnSearch" value=" 검색 취소 " onclick="location.href='<?php echo $_SERVER["SCRIPT_NAME"] ?>'" />
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>
<script>
    function goWrite() {
        location.href = "<?php echo $_write_uri; ?>";
    }

    function trim(str) {
        //정규 표현식을 사용하여 화이트스페이스를 빈문자로 전환
        str = str.replace(/^\s*/, '').replace(/\s*$/, '');
        return str; //변환한 스트링을 리턴.
    }

    function goSearch(f) {
        if (f == null || !f) {
            alert("선언되지 않은 잘못된 접근");
            return;
        }
        //alert(f.name);
        if (!trim(f.s_word.value)) {
            alert("검색어를 입력해 주세요")
            f.s_word.focus();
            return;
        } else {
            f.submit();
        }
    }

    function logout() {
        location.href = "board_logout.php";
    }

    function goMessage() {
        var url = "message_list.php";
        var name = "popup test";
        var option = "width = 1000, height = 650, top = 100, left = 200, location = no"
        window.open(url, name, option);
    }

    function goList() {
        location.href = "<?php echo $_list_uri ?>";
    }

    function orderConvc() {
        location.href = "<?php echo $_convc_uri ?>";
    }

    function orderGood() {
        location.href = "<?php echo $_congood_uri ?>";
    }

    function dropDown(m) {
        var uno = document.getElementById(m.getAttribute('id')).getAttribute('id');
        var menu = document.getElementById("menu" + uno);
        menu.style.display = "block";

        setTimeout(function() {
            menu.style.display = "none";
        }, 2500);
    }

    function sendMessage(m){
        var uno = document.getElementById(m.getAttribute('id')).getAttribute('id');
        var url = "message_send.php?mode=menu&uno=" + uno;
        var name = "popup test";
        var option = "width = 1000, height = 650, top = 100, left = 200, location = no"
        window.open(url, name, option);
    }
    //     $(document).ready(function(){
    //   //Show contextmenu:
    //   $(document).contextmenu(function(e){
    //     //Get window size:
    //     var winWidth = $(document).width();
    //     var winHeight = $(document).height();
    //     //Get pointer position:
    //     var posX = e.pageX;
    //     var posY = e.pageY;
    //     //Get contextmenu size:
    //     var menuWidth = $(".contextmenu").width();
    //     var menuHeight = $(".contextmenu").height();
    //     //Security margin:
    //     var secMargin = 10;
    //     //Prevent page overflow:
    //     if(posX + menuWidth + secMargin >= winWidth
    //     && posY + menuHeight + secMargin >= winHeight){
    //       //Case 1: right-bottom overflow:
    //       posLeft = posX - menuWidth - secMargin + "px";
    //       posTop = posY - menuHeight - secMargin + "px";
    //     }
    //     else if(posX + menuWidth + secMargin >= winWidth){
    //       //Case 2: right overflow:
    //       posLeft = posX - menuWidth - secMargin + "px";
    //       posTop = posY + secMargin + "px";
    //     }
    //     else if(posY + menuHeight + secMargin >= winHeight){
    //       //Case 3: bottom overflow:
    //       posLeft = posX + secMargin + "px";
    //       posTop = posY - menuHeight - secMargin + "px";
    //     }
    //     else {
    //       //Case 4: default values:
    //       posLeft = posX + secMargin + "px";
    //       posTop = posY + secMargin + "px";
    //     };
    //     //Display contextmenu:
    //     $(".contextmenu").css({
    //       "left": posLeft,
    //       "top": posTop
    //     }).show();
    //     //Prevent browser default contextmenu.
    //     return false;
    //   });
    //   //Hide contextmenu:
    //   $(document).click(function(){
    //     $(".contextmenu").hide();
    //   });
    // });
</script>

</html>
