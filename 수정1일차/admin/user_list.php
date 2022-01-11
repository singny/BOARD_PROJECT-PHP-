<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);
include './_inc.php';
require_once  _LIB_PATH_ . "nav_page.class.php";
$nav = new NavPage;
$nav->start(10, 10);

$db = new DB;
$mWhere = null;
$sel_opt = array();
$search_options = array(
    "user_id" => array("col" => "U.USER_ID", "text" => "사용자 ID"), "user_name" => array("col" => "U.USER_NAME", "text" => "사용자 명"), "duty_name" => array("col" => "DU.DUTY_NAME", "text" => "직위 명"), "dept_name" => array("col" => "DE.DEPT_NAME", "text" => "부서 명"), "dept_id" => array("col" => "DE.DEPT_ID", "text" => "부서 ID")
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
        $mWhere .= " OR U.USER_ID LIKE '%{$post["s_word"]}%'";
        $mWhere .= " OR DE.DEPT_NAME LIKE '{$post["s_word"]}%'";
        $mWhere .= " OR DU.DUTY_NAME = '{$post["s_word"]}%'";
        $mWhere .= ")";
    }
}

$main_sql = "SELECT 
    U.*
    , DE.DEPT_NAME
    , DE.DEPT_NAME_PATH
    , DE.DEPT_NO_PATH
    , DU.DUTY_NAME
    , L.LOC_NAME
FROM EX_USER_SET U 
    , V_EX_DEPT_SET DE
    , EX_DUTY_SET DU
    , EX_LOC_CODE L
WHERE 1 = 1
    AND U.DEPT_ID = DE.DEPT_NO(+)
    AND U.DUTY_ID = DU.DUTY_NO(+)
    AND U.LOC_ID = L.LOC_NO(+)
    AND U.IS_ACTIVE = 'Y'
    AND U.IS_USE = 'Y'
    {$mWhere}
";
$sql = "WITH A as (
  {$main_sql}
)
SELECT COUNT(*) AS CNT FROM A";
//echo nl2br(str_replace(" ", "&nbsp;", $sql));
//echo nl2br( str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $sql) ); //str_replace, substr, explode, strpos
//Fun::print_($sql);

////$db->query($sql);
//$db->next_record();
//$count = $db->f(0);
$count = $db->query_one($sql);
$str_page_bar = $nav->navpage($count);
//$db->Debug = true;
$sql = "WITH A as (
  {$main_sql}
)
SELECT * FROM A
WHERE 1 = 1 
ORDER BY duty_id
";

$db->query_limit($sql, $nav->start_row, $nav->row_scale);
$query_row_count = $db->nf();

$str_data_row = null;
if ($query_row_count > 0) {
    $i = 0;
    while ($db->next_record()) {
        $rec = $db->Record;
        $no = $count - $nav->start_row - $i;
        $desc_no = $count - $no + 1;
        //$list_query = Fun::getParamUrl();
        $str_data_row .= "              
            <tr>
                <td style=\"text-align: center;\">{$desc_no}</td>
                <td style=\"text-align: center;\">{$db->f("user_id")}</td>
                <td style=\"text-align: center;\">{$db->f("user_name")}</td>
                <td style=\"text-align: center;\">{$rec["duty_name"]}</td>
                <td style=\"text-align: center;\">{$rec["dept_name"]}</td>
                <td style=\"text-align: center;\">{$rec["loc_name"]}</td>
                <td style=\"text-align: center;\">
                <a href=\"{$_view_uri}&uno={$rec["uno"]}\"><img src=\"/lib/images/btn_simple_view.gif\" title=\"조회\" style=\"cursor: pointer\" /></a>
                <a href=\"{$_modify_uri}&uno={$rec["uno"]}\"><img src=\"/lib/images/btn_simple_mod.gif\" title=\"수정\" style=\"cursor: pointer\" /></a>
                </td>
            </tr>
               ";
        $i++;
    }
} else {
    $str_data_row = '      
            <tr>
                <td colspan="7" style="text-align: center;">직원 정보를 찾을수가 없습니다.</td>
            </tr>
          ';
}
$user_mng = null;

if ($_SESSION["user_id"] == "admin") {
    $user_mng = "<li><a class=\"active\" href=\"user_list.php\">회원관리</a></li>";
};

// 쪽지함

$user_name_sql = "SELECT * FROM EX_USER_SET WHERE user_id='{$_SESSION["user_id"]}'";
$db->query($user_name_sql);

while ($db->next_record()) {
    $row[2] = $db->Record;
}

$message_sql = "SELECT COUNT(*) FROM {$_table_message} WHERE re_user = {$row[2]["uno"]} and is_read='N'";
$n = $db->query_one($message_sql);

if($n == 0){
   $message = "쪽지함[0]"; 
   $li_message = "쪽지함[0]"; 
} else{
    $message = "<img src=\"./image/message.png\" width=\"20px\" height=\"20px\" /> 쪽지함[" . $n . "]";
    $li_message = "쪽지함[" . $n . "]";
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
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--<meta http-equiv="refresh" content="0;url=http://www.daum.net">-->
    <title>List Page</title>
    <link rel="stylesheet" type="text/css" href="http://www.htenc.co.kr/css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&family=Nanum+Pen+Script&family=Poor+Story&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="apple-touch-icon" sizes="57x57" href="../image/icon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="../image/icon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="../image/icon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../image/icon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="../image/icon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../image/icon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="../image/icon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../image/icon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../image/icon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="../image/icon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../image/icon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="../image/icon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../image/icon/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <style>
        body {
            font-family: 'Jua', sans-serif;
            font-size: 100%;
        }


        @media(max-width:500px) {
            body {
                font-family: 'Jua', sans-serif;
                font-size: xx-small;
            }

        }

        .styleguide {
            display: '';
            text-align: center;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            /* font-family: Roboto, 'Noto Sans Korean', sans-serif; */
            font-weight: 400;
            letter-spacing: -1px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            color: #222;
            word-wrap: break-word;
            word-break: keep-all;
            box-sizing: border-box;
            border: 0;
            font-size: 100%;
            vertical-align: baseline;
            margin: 0;
            padding: 0;
            width: 75%;
        }

        .styleguide th {
            text-align: center;
            vertical-align: middle;
            justify-content: center;
            align-items: center;
        }

        .styleguide td {
            padding: 5px;
            text-align: left;
            vertical-align: middle;

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

        .container {
            margin-left: 30%;
            padding: 60px 16px;
        }

        @media(max-width:500px) {
            .container {
                margin-left: 20%;
                padding: 60px 16px;
            }
        }
    </style>
    <script type="text/javascript">
        
        function goView(uno) {
            location.href = "<?php echo $_view_uri ?>&uno=" + uno;
        }
        
        function goModify(uno) {
            location.href = "<?php echo $_modify_uri ?>&uno=" + uno;
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
            // var word = rtrim(ltrim(f.s_word.value));
            if (!trim(f.s_word.value)) {
                alert("검색어를 입력하여 주세요.");
                f.s_word.focus();
                return;
            } else {
                f.submit();
            }
        }

        function goMessage(){
        var url = "../message_list.php";
        var name = "popup test";
        var option = "width = 1000, height = 700, top = 100, left = 200, location = no"
        window.open(url, name, option);
    }
    </script>
</head>

<body>
    <ul>
        <li><img src="../image/welcome2.png" /></li>
        <li>&nbsp;</li>
        <li><a href="../board_list.php">우수직원 게시판</a></li>
        <li><a href="../board_chart.php">우수직원 순위현황</a></li>
        <li><a href="#" onclick="goMessage()"><?php echo $li_message ?></a></li>
        <?php echo $user_mng ?>
        <!-- <li><a href="#">소개</a></li>
  <li><a href="#">자유게시판</a></li> -->
    </ul>
    <div class="container">
        <div class="styleguide">
            <h3 style="text-align: left"><img src="/lib/images/title_De.gif">직원 목록</h3>
            <table style="text-align: center;width: 100%">
                <colgroup>
                    <col style="width:50px" />
                    <col style="width:150px" />
                    <col style="width:150px" />
                    <col style="width:120px" />
                    <col style="width:150px" />
                    <col style="width:100px" />
                    <col style="width:100px" />
                </colgroup>
                <caption>사업 Reference</caption>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>ID</th>
                        <th>성명</th>
                        <th>직위</th>
                        <th>부서</th>
                        <th>지역</th>
                        <th>관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $str_data_row; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align:center;"><?php echo $str_page_bar; ?></td>
                    </tr>
                </tfoot>
            </table>
            <br />
            <form id="frmSearch" name="frmSearch" method="get" aciton="<?php echo $_SERVER["SCRIPT_NAME"] ?>" onsubmit="goSearch(this); return false;">
                <input type="hidden" id="mode" name="mode" value="search" />
                <select id="s_type" name="s_type">
                    <option value="all" <?php echo @$sel_opt["all"]; ?>>전체</option>
                    <?php echo $search_options_html; ?>
                </select>
                <input type="text" id="s_word" name="s_word" value="<?php echo @trim($post["s_word"]); ?>" />
                <input type="button" id="btnSearch" name="btnSearch" value=" 검색 " onclick="goSearch(this.form);" />
                <!-- <input type="button" id="btnSearch" name="btnSearch" value=" 초기화 " onclick="this.form.reset()" /> -->
                <input type="button" id="btnSearch" name="btnSearch" value=" 검색 취소 " onclick="location.href='<?php echo $_SERVER["SCRIPT_NAME"] ?>'" />
            </form>
        </div>
    </div>
</body>

</html>
