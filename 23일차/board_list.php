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
$search_options = array(
    "user_id" => array("col" => "B.CON_TITLE", "text" => "TITLE"), "user_name" => array("col" => "U.USER_NAME", "text" => "사용자 명"), "dept_name" => array("col" => "DE.DEPT_NAME", "text" => "부서 명"), "duty_name" => array("col" => "DU.DUTY_NAME", "text" => "직위 명")
);
$search_options_html = "";
foreach ($search_options as $_col => $_txt) {
    $opt_sel = "";
    if (@$post["s_type"] == $_col) {
        $opt_sel = " selected";
        $mWhere = " AND {$_txt["col"]} LIKE '%{$post["s_word"]}%'";
    }
    $search_options_html .= "<option value=\"{$_col}\" {$opt_sel}>{$_txt["text"]}</option>";
}

if (@$post["mode"] == "search" && @trim($post["s_word"]) != "") {
    $col_name = null;
    if ($post["s_type"] == "all") {
        $sel_opt["all"] = " selected";
        $mWhere = " AND (";
        $mWhere .= " U.USER_NAME LIKE '%{$post["s_word"]}%'";
        $mWhere .= " OR B.CON_TITLE LIKE '%{$post["s_word"]}%'";
        $mWhere .= " OR DE.DEPT_NAME LIKE '{$post["s_word"]}%'";
        $mWhere .= " OR DU.DUTY_NAME = '{$post["s_word"]}%'";
        $mWhere .= ")";
    }
}

//$db->Debug = true;
$main_sql = "SELECT b.con_datetime, b.con_title, u.user_name, de.dept_name, b.con_vc , du.duty_name, b.con_no, b.con_good, b.con_comment
            FROM ex_user_set u, ex_dept_set de, board_contents b, ex_duty_set du
            WHERE b.wr_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no
            {$mWhere}
            ORDER BY con_no desc";
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
            $dept_user = "[" . $row["dept_name"] . "] " . $row["user_name"] . " " . $row["duty_name"];
            $con_title = $row["con_title"] . " [" . $row["con_comment"] . "]";
            $list_query = Fun::getParamUrl();

            $str_data_row .= "              
            <tr onclick=\"location.href='board_view.php?{$list_query}&con_no={$row["con_no"]}'\" style=\"cursor:pointer\">
                <td style=\"text-align: center;\">{$no}</td>
                <td style=\"text-align: center;\">{$date}</td>
                <td>{$con_title}</td>
                <td>{$dept_user}</td>
                <td style=\"text-align: center;\">{$row["con_vc"]}</td>
                <td style=\"text-align: center;\">{$row["con_good"]}</td>
            </tr>";
            $i++;
        } else {
            $no = $count - $nav->start_row - $i;
            $date = substr($row["con_datetime"], 0, 10);
            $dept_user = "[" . $row["dept_name"] . "] " . $row["user_name"] . " " . $row["duty_name"];
            $con_title = $row["con_title"] . " <img src=\"./image/new.png\" width=\"25px\" height=\"25px\" /> [" . $row["con_comment"] . "] ";
            $list_query = Fun::getParamUrl();

            $str_data_row .= "              
            <tr onclick=\"location.href='board_view.php?{$list_query}&con_no={$row["con_no"]}'\" style=\"cursor:pointer\">
                <td style=\"text-align: center;\">{$no}</td>
                <td style=\"text-align: center;\">{$date}</td>
                <td>{$con_title}</td>
                <td>{$dept_user}</td>
                <td style=\"text-align: center;\">{$row["con_vc"]}</td>
                <td style=\"text-align: center;\">{$row["con_good"]}</td>
            </tr>";
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
    $user_mng = "<li><a href=\"./admin/user_list.php\">회원관리</a></li>";
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
    $user_img = "<div class=\"circle \" onclick=\"location.href='my_page.php';\" style=\"background-image: url('./upload_file/user/{$uno}/$file_name')\" title=\"마이페이지\"></div>";
} else {
    $user_img = "<div class=\"circle \" onclick=\"location.href='my_page.php';\" style=\"background-image: url('./image/user.png')\" title=\"마이페이지\"></div>";
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
<link href="css/bootstrap.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
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
        }

        @media(max-width:500px) {
            body {
                font-family: 'Jua', sans-serif;
                font-size: xx-small;
            }

        }

        /* table.type07 {
            border-collapse: collapse;
            text-align: left;
            line-height: 1.5;
            border: 1px solid #ccc;
            margin: 0px 220px;
        }

        @media(min-width:1900px) {
            table.type07 {
                border-collapse: collapse;
                text-align: left;
                line-height: 1.5;
                border: 2px solid #ccc;
                margin: 0px 390px;
            }
        }

        @media(max-width:500px) {
            table.type07 {
                border-collapse: collapse;
                text-align: center;
                line-height: 1.5;
                border: 1px solid #ccc;
                margin: 0px 15px;
            }
        }

        table.type07 thead {
            border-right: 1px solid #ccc;
            border-left: 1px solid #ccc;
            background-image: url('./image/thead.jpg');
            
        }

        table.type07 thead th {
            padding: 10px;
            font-weight: bold;
            vertical-align: top;
            color: black;
        }

        table.type07 tbody th {
            width: 150px;
            padding: 5px;
            font-weight: bold;
            vertical-align: top;
            border-bottom: 1px solid #ccc;
            background: #fcf1f4;
 
        } */

        table.type07 td {
            /* width: 350px; */
            padding: 5px;
            vertical-align: top;
            border-bottom: 1px solid #ccc;
            background-color:white;
        }

        input[type=button] {
            background-color: #6495ED;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        @media(max-width:500px) {
            input[type=button] {
                background-color: #6495ED;
                color: white;
                padding: 4px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }
        }

        input[type=button]:hover {
            background-color: #4682B4;

        }

        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            width: 12.5%;
            background-color: #F0F8FF;
            position: fixed;
            height: 100%;
            overflow: auto;
        }

        li a {
            display: block;
            color: #000;
            padding: 8px 16px;
            text-decoration: none;
        }

        li a.active {
            background-color: #000080;
            color: white;
        }

        li a:hover:not(.active) {
            background-color: #000080;
            color: white;
        }

        li img {
            padding-top: 20px;
            padding-left: 40px;
            width: 150px
        }

        @media(min-width:1900px) {
            li img {
                padding-top: 20px;
                padding-left: 40px;
                width: 200px
            }
        }

        @media(max-width:500px) {
            li img {
                padding: 3px;
                width: 45px
            }

        }

        .col-25 {
            float: left;
            width: 7.5%;
            margin-top: 7px;
            margin-left: 32%;
        }

        @media(min-width:1900px) {
            .col-25 {
                float: left;
                width: 7.25%;
                margin-top: 7px;
                margin-left: 32%;
            }
        }
        @media(max-width:500px) {
            .col-25 {
                float: left;
                width: 10%;
                margin-top: 7px;
                margin-left: 65px;
            }
        }

        .col-75 {
            float: left;
            width: 37%;
            margin-top: 6px;
            margin-bottom: 30px;
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
                margin-left:auto;
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
            background-image: url('./image/hello.jpg');
            padding: 8px;
            border-radius: 4px;
        }

        @media(min-width:1900px) {
            .page {
                margin-left: 23.5%;
                background-image: url('./image/hello.jpg');
                padding: 8px;
                border-radius: 4px;
            }
        }

        @media(max-width:500px) {
            .page {
                margin-left: 15px;
                background-image: url('./image/hello.jpg');
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
                margin-right: 5%
            }
        }

        .circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            float: right;
            margin-right: 5px;
            border: 3px solid #fff;
            box-shadow: 0 0 16px rgb(221, 221, 221);
            cursor: pointer;
            background-size: 47.5px 50px;

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
        }

        @media(max-width:500px) {
            .logo {
                max-width: 300px;
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
        .table_position{
            margin-left: 15%;
            margin-right: 15%;
        }
        @media(min-width:1900px) {
            .table_position{
            margin-left: 22%;
            margin-right: 22%;
        }
        }
        @media(max-width:500px) {
            .table_position{
            margin-left: 5%;
            margin-right: 0%;
        }
        }
        /* .wrap {
	position: absolute;
	top: 50%;
	left: 50%;
	margin-top: -86px;
	margin-left: -89px;
	text-align: center;
} */

a.button, a.button2 {
	-webkit-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
	-moz-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
	-ms-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
	-o-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
	transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
	display: block;
	/* margin: 20px auto; */
	width: 80px;
	text-decoration: none;
	border-radius: 4px;
	padding: 5px;
    text-align: center;
    cursor:pointer;
}

a.button {
	color: rgba(30, 22, 54, 0.6);
	box-shadow: rgba(30, 22, 54, 0.4) 0 0px 0px 2px inset;
}

a.button:hover {
	color: rgba(255, 255, 255, 0.85);
	box-shadow: rgba(30, 22, 54, 0.7) 0 0px 0px 40px inset;
}

a.button2 {
	color: rgba(30, 22, 54, 0.6);
	box-shadow: rgba(30, 22, 54, 0.4) 0 0px 0px 2px inset;
}

a.button2:hover {
	color: rgba(255, 255, 255, 0.85);
	box-shadow: rgba(30, 22, 54, 0.7) 0 80px 0px 2px inset;
}

    </style>
</head>

<body>
    <ul>
        <li><img src="./image/welcome2.png"></li>
        <li>&nbsp;</li>
        <li><a class="active" href="board_list.php">우수직원 게시판</a></li>
        <li><a href="board_chart.php">우수직원 순위현황</a></li>
        <?php echo $user_mng ?>
        <!-- <li><a href="#">소개</a></li>
  <li><a href="#">자유게시판</a></li> -->
    </ul>
    <div style="margin-left:8%;padding:1px 16px;height:900px;">
        <br />
        <div class="bar">
            <!-- <div style="float:right">&nbsp;<input type="button" onclick="logout()" value="로그아웃" /></div> -->
  		<div style="float:right"><a class="button2" onclick="logout()">로그아웃</a></div>

            <span style=" padding:8px;border-radius: 4px; float:right;"><b><?php echo $hello ?></b></span>
            <?php echo $user_img ?>
            <!-- <div class="circle " onclick="location.href='my_page.php';" title="마이페이지"></div> -->
        </div>
        <br /><br /> <br />
        <div style="text-align:center; cursor:pointer;">
            <img src="./image/logo3.png" class="logo" onclick="location.href='board_list.php'" />
        </div>
        <br />
        <span class="page">
            <b><?php
                echo "총 게시물 : {$nav->total_row} / 전체 페이지 : {$nav->total_page}";
                ?></b>
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
                </tr>
            </thead>
            <tbody>
                <?php echo $str_data_row; ?>
            </tbody>
        </table>
        </div>
        <br />
        <div class="write">
            <!-- <input type="button" value="글쓰기" onclick="goWrite()" /> -->
            <a class="button2" onclick="goWrite()" style="margin-left:95%">글쓰기</a>
        </div>

        <div style="text-align:center;">
            <?php echo $str_page_bar ?>
        </div>
        <br />
        <div style="text-align:center;">
            <form id="frmSearch" name="frmSearch" method="get" aciton="<?php echo $_SERVER["SCRIPT_NAME"] ?>" onsubmit="goSearch(this); return false;">
                <input type="hidden" id="mode" name="mode" value="search" />
                <div class="row">
                    <div class="col-25">
                        <select class="s_type" id="s_type" name="s_type" aria-label=".form-select-sm example">
                            <option value="all" <?php echo @$sel_opt["all"]; ?>>전체</option>
                            <?php echo $search_options_html; ?>
                        </select>
                    </div>
                    <div class="col-75">
                        <input type="text" id="s_word" name="s_word" value="<?php echo @trim($post["s_word"]); ?>" />
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
        //var word = rtrim(ltrim(f.s_word.value));
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
</script>

</html>
