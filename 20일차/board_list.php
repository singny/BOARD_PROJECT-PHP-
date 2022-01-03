<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);
require_once  _LIB_PATH_ . "nav_page.class.php";
include "_inc.php";
$nav = new NavPage;
$nav->start(12, 10);

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
        $row = $db->Record;
        $no = $count - $nav->start_row - $i;
        $date = substr($row["con_datetime"], 0, 10);
        $dept_user = "[" . $row["dept_name"] . "] " . $row["user_name"] . " " . $row["duty_name"];
        $con_title = $row["con_title"]. " [".$row["con_comment"]."]";
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

//테스트
// $datetype = strtotime($row["con_datetime"]);
// $datetype = date()


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
        }

        table.type07 {
            border-collapse: collapse;
            text-align: left;
            line-height: 1.5;
            border: 1px solid #ccc;
            margin: 0px 225px;
        }

        @media(min-width:1900px){
        table.type07 {
            border-collapse: collapse;
            text-align: left;
            line-height: 1.5;
            border: 1px solid #ccc;
            margin: 0px 400px;
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
        }

        table.type07 td {
            /* width: 350px; */
            padding: 5px;
            vertical-align: top;
            border-bottom: 1px solid #ccc;
        }

        input[type=button] {
            background-color: #6495ED;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        input[type=button]:hover {
            background-color: #4682B4;

        }

        body {
            margin: 0;
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
        .col-25 {
            float: left;
            width: 10%;
            margin-top: 7px;
            margin-left: 32%;
        }
        @media(min-width:1900px){
        .col-25 {
            float: left;
            width: 10%;
            margin-top: 7px;
            margin-left: 32%;
                }
        }
        
        .col-75 {
            float: left;
            width: 37%;
            margin-top: 6px;
            margin-bottom : 30px;
        }

        @media(min-width:1900px){
            .col-75 {
                float: left;
            width: 26.5%;
            margin-top: 6px;
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
            margin-left:16.5%;
            background-image: url('./image/hello.jpg');
             padding:8px;
             border-radius: 4px;
        }

        @media(min-width:1900px){
            .page {
                margin-left:23.5%;
            background-image: url('./image/hello.jpg');
             padding:8px;
             border-radius: 4px;
                }
        }

        .write {
            text-align:right; 
            margin-right:16.5%
        }

        @media(min-width:1900px){
            .write {
            text-align:right; 
            margin-right:23.5%
                }
        }
    </style>
</head>

<body>
    <ul>
        <li><a class="active" href="board_list.php">우수직원 게시판</a></li>
        <li><a href="board_chart.php">우수직원 순위현황</a></li>
        <?php echo $user_mng ?>
        <!-- <li><a href="#">소개</a></li>
  <li><a href="#">자유게시판</a></li> -->
    </ul>
    <div style="margin-left:8%;padding:1px 16px;height:900px;">
        <br />
        
        <span style="margin-left:63%;">
            <span style="background-image: url('./image/hello.jpg'); padding:8px;border-radius: 4px; cursor:pointer;" title="마이페이지" onclick="location.href='my_page.php';"><b><?php echo $hello ?></b></span>
            &nbsp;<input type="button" onclick="logout()" value="로그아웃" />
        </span>
        <br /><br />
        <div style="text-align:center; cursor:pointer;">
            <img src="./image/logo3.png" style="max-width:900px" onclick="location.href='board_list.php'"/>
        </div>
        <br />
        <span class="page" >
            <b><?php
                echo "총 게시물 : {$nav->total_row} / 전체 페이지 : {$nav->total_page}";
                ?></b>
        </span>
        <br /><br />
        <table class="type07">
            <thead>
                <tr>
                    <th scope="cols" style="width: 25px; text-align :center;">NO.</th>
                    <th scope="cols" style="width: 250px; text-align :center;">작성일</th>
                    <th scope="cols" style="width: 550px;">TITLE</th>
                    <th scope="cols" style="width: 350px;">작성자</th>
                    <th scope="cols" style="width: 80px; text-align:center;">조회수</th>
                    <th scope="cols" style="width: 80px; text-align:center;">좋아요</th>
                </tr>
            </thead>
            <tbody>
                <?php echo $str_data_row; ?>
            </tbody>
        </table>
        <br />
        <div class="write">
            <input type="button" value="글쓰기" onclick="goWrite()" />
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
                <select id="s_type" name="s_type" aria-label=".form-select-sm example"  style="width:175px; height:30px; margin-left:30%">
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
