<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);
require_once  _LIB_PATH_ . "nav_page.class.php";
include "_inc.php";
$nav = new NavPage;
$nav->start(5, 10);

$db = new DB;
$mWhere = null;
$sel_opt = array();
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
    } else if ($post["s_type"] == "user_name") {
        $sel_opt["user_id"] = " selected";
        $mWhere = " AND U.USER_NAME LIKE '%{$post["s_word"]}%'";
    } else if ($post["s_type"] == "con_title") {
        $sel_opt["con_title"] = " selected";
        $mWhere = " AND B.CON_TITLE LIKE '%{$post["s_word"]}%'";
    } else if ($post["s_type"] == "dept_name") {
        $sel_opt["dept_name"] = " selected";
        $mWhere = " AND DE.DEPT_NAME LIKE '{$post["s_word"]}%'";
    } else if ($post["s_type"] == "duty_name") {
        $sel_opt["duty_name"] = " selected";
        $mWhere = " AND DU.DUTY_NAME = '{$post["s_word"]}%'";
    }
}

//$db->Debug = true;
$main_sql = "SELECT b.con_datetime, b.con_title, u.user_name, de.dept_name, b.con_vc , du.duty_name, b.con_no
            FROM ex_user_set u, ex_dept_set de, board_contents b, ex_duty_set du
            WHERE b.wr_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no
            {$mWhere}";
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
        $list_query = Fun::getParamUrl();

        $str_data_row .= "              
        <tr>
            <td style=\"text-align: center;\">{$no}</td>
            <td style=\"text-align: center;\">{$date}</td>
            <td><b><a href='board_view.php?{$list_query}&con_no={$row["con_no"]}'>{$row["con_title"]}</b></a></td>
            <td>{$dept_user}</td>
            <td style=\"text-align: center;\">{$row["con_vc"]}</td>
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

$user_name_sql = "SELECT * FROM EX_USER_SET WHERE user_id='{$_SESSION["user_id"]}'";
$db->query($user_name_sql);

while($db->next_record()){
    $row[1] = $db->Record;
}

$hello = $row[1]["user_name"]."님, 안녕하세요";

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
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Page</title>
    <style>
        table.type07 {
            border-collapse: collapse;
            text-align: left;
            line-height: 1.5;
            border: 1px solid #ccc;
            margin: 0px 400px;
        }

        table.type07 thead {
            border-right: 1px solid #ccc;
            border-left: 1px solid #ccc;
            background: #6666FF;
        }

        table.type07 thead th {
            padding: 10px;
            font-weight: bold;
            vertical-align: top;
            color: #fff;
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
            background-color: #7B68EE;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type=button]:hover {
            background-color: #6A5ACD;

        }
    </style>
</head>

<body>
    <div style="text-align:right; margin-top:2.5%; margin-right:21.5%"><b><?php echo $hello?></b></div>
    <br />
    <div style="text-align:right; margin-right:21.5%;" >
        <b><?php
        echo "총 게시물 : {$nav->total_row} / 전체 페이지 : {$nav->total_page}";
        ?></b>
    </div>
    <table class="type07">
        <thead>
            <tr>
                <th scope="cols" style="width: 25px; text-align :center;">NO.</th>
                <th scope="cols" style="width: 250px; text-align :center;">작성일</th>
                <th scope="cols" style="width: 650px;">TITLE</th>
                <th scope="cols" style="width: 300px;">작성자</th>
                <th scope="cols" style="width: 75px; text-align :center;">조회수</th>
            </tr>
        </thead>
        <tbody>
            <?php echo $str_data_row; ?>
        </tbody>
    </table>
    <br />
    <div style="text-align:right; margin-right:21.5%">
        <input type="button" value="글쓰기" onclick="goWrite()" />
    </div>
    <div style="text-align:center;">
        <?php echo $str_page_bar ?>
    </div>
    <br /> <br /> <br /> <br />
    <div style="text-align:center;">
        <form id="frmSearch" name="frmSearch" method="get" aciton="<?php echo $_SERVER["SCRIPT_NAME"] ?>" onsubmit="goSearch(this); return false;">
            <input type="hidden" id="mode" name="mode" value="search" />
            <select id="s_type" name="s_type">
                <option value="all" <?php echo @$sel_opt["all"]; ?>>전체</option>
                <option value="con_title" <?php echo @$sel_opt["con_title"]; ?>>TITLE</option>
                <option value="user_name" <?php echo @$sel_opt["user_name"]; ?>>사용자 명</option>
                <option value="dept_name" <?php echo @$sel_opt["dept_name"]; ?>>부서 명</option>
                <option value="duty_name" <?php echo @$sel_opt["duty_name"]; ?>>직위 명</option>
            </select>
            <input type="text" id="s_word" name="s_word" value="<?php echo @trim($post["s_word"]); ?>" />
            <input type="submit" id="btnSearch" name="btnSearch" value=" 검색 " onclick="goSearch(this.form);" />
            <input type="submit" id="btnSearch" name="btnSearch" value=" 초기화 " onclick="this.form.reset()" />
            <input type="submit" id="btnSearch" name="btnSearch" value=" 검색 취소 " onclick="location.href='<?php echo $_SERVER["SCRIPT_NAME"] ?>'" />
        </form>
    </div>
    <br /><br />
</body>
<script>
    function goWrite() {
        location.href = "<?php echo $_write_uri; ?>";
    }

    function goSearch(f) {
        if (f == null || !f) {
            alert("선언되지 않은 잘못된 접근");
            return;
        }
        //alert(f.name);
        //var word = rtrim(ltrim(f.s_word.value));
        if (!trim(f.s_word.value)) {
            alert("검색어를 입력하여 주세요.;")
            f.s_word.focus();
            return;
        } else {
            f.submit();
        }
    }
</script>

</html>
