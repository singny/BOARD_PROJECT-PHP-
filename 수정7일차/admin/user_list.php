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
    $user_mng = "<li class=\"active\" style=\"padding-top:15px\"><a href=\"user_list.php\">회원관리</a></li>";
};

// 유저 사진
$file_sql = "SELECT UNO, FILE_PATH FROM {$_table_user} WHERE user_id='{$_SESSION["user_id"]}'";
$db->query($file_sql);
while ($db->next_record()) {
    $row[1] = $db->Record;
    $uno = $row[1]["uno"];
}

@$file_name = basename($row[1]["FILE_PATH"]);
$user_img = null;

if ($file_name) {
    $user_img = "<div class=\"circle \" onclick=\"location.href='../my_page.php';\" style=\"background-image: url('../upload_file/user/{$uno}/$file_name');\" title=\"마이페이지\"></div>";
} else {
    $user_img = "<div class=\"circle \" onclick=\"location.href='./my_page.php';\" style=\"background-image: url('../image/user.png')\" title=\"마이페이지\"></div>";
}


// 쪽지함
$user_name_sql = "SELECT * FROM EX_USER_SET WHERE user_id='{$_SESSION["user_id"]}'";
$db->query($user_name_sql);

while ($db->next_record()) {
    $row[2] = $db->Record;
}

$message_sql = "SELECT COUNT(*) FROM {$_table_message} WHERE re_user = {$row[2]["uno"]} and is_read='N'";
$n = $db->query_one($message_sql);

if ($n == 0) {
    $message = "쪽지함[0]";
    $li_message = "쪽지함[0]";
} else {
    $message = "<img src=\"../image/message.png\" width=\"20px\" height=\"20px\" /> 쪽지함[" . $n . "]";
    $li_message = "쪽지함[" . $n . "]";
}

//안녕하세요
$hello = null;
if (@$_SESSION["user_id"]) {
    $user_name_sql = "SELECT * FROM EX_USER_SET WHERE user_id='{$_SESSION["user_id"]}'";
    $db->query($user_name_sql);

    while ($db->next_record()) {
        $row[3] = $db->Record;
    }

    $hello = $row[3]["user_name"] . "님, 안녕하세요";
} else {

    FUN::alert("로그인 후 접속하세요", "board_login.php");
}
// 알림 기능
$alert_sql = "SELECT b.con_no, b.con_title, de.dept_name, du.duty_name, u.user_name, a.is_read, to_char(a.a_datetime,'YYYY.MM.DD HH24:mi:ss') as a_datetime, u.is_active, u.is_use
                FROM board_contents b, board_alert a, ex_user_set u , ex_dept_set de, ex_duty_set du
                WHERE b.re_user = {$_SESSION["uno"]} and b.wr_user = a.wr_user and b.con_no = a.con_no and b.wr_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no
                ORDER BY con_datetime DESC";
$db->query($alert_sql);
$alert_data_row = null;
while ($db->next_record()) {
    $row[4] = $db->Record;
    if ($row[4]["is_use"] == 'N' || $row[4]["is_active"] == 'N') {
        $wr_user = "[" . $row[4]["dept_name"] . "] " . $row[4]["user_name"] . " " . $row[4]["duty_name"] . "&nbsp;<img src=\"../image/delete-user.png\" title=\"탈퇴한 계정\" width=\"25px\" height=\"25px\">";
    } else {
        $wr_user = "[". $row[4]["dept_name"] ."] ".$row[4]["user_name"]." ".$row[4]["duty_name"];
    }

    if($row[4]["is_read"] == 'N'){
        $alert_data_row .= "<tr onclick=\"location.href='../board_view.php?con_no={$row[4]["con_no"]}'\" style=\"cursor:pointer\">
                                <td>{$wr_user}께서 회원님을 추천하는 글을 작성하였습니다.<br /><br />글 제목 : {$row[4]["con_title"]}<br />{$row[4]["a_datetime"]}</td>
                                <td><img src=\"../image/read.png\" width=\"30px\" height=\"30px\"></td>
                            </tr>";
    }else{
        $alert_data_row .= "<tr onclick=\"location.href='../board_view.php?con_no={$row[4]["con_no"]}'\" style=\"cursor:pointer\">
                                <td>{$wr_user}께서 회원님을 추천하는 글을 작성하였습니다.<br /><br />글 제목 : {$row[4]["con_title"]}<br />{$row[4]["a_datetime"]}</td>
                                <td>&nbsp;</td>
                            </tr>";
    }
}

$art_sql = "SELECT COUNT(*) FROM {$_table_alert} WHERE uno = {$_SESSION["uno"]} and is_read='N'";
$an = $db->query_one($art_sql);

if ($an == 0) {
    $alert = "<img src=\"../image/nobell.png\" width=\"20px\" height=\"20px\" /> 알림창[" . $an . "]";;
} else {
    $alert = "<img src=\"../image/bell.png\" width=\"20px\" height=\"20px\" /> 알림창[" . $an . "]";
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- 합쳐지고 최소화된 최신 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

    <!-- 부가적인 테마 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

    <!-- 합쳐지고 최소화된 최신 자바스크립트 -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <!-- <link rel="stylesheet" type="text/css" href="http://www.htenc.co.kr/css/style.css" /> -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&family=Nanum+Pen+Script&family=Poor+Story&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Jua', sans-serif;
            font-size:medium;
        }
        @media(max-width:500px) {
            body {
                font-family: 'Jua', sans-serif;
                font-size: xx-small;
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
            padding-top:9%;
        }

        .styleguide th {
            text-align: center;
            vertical-align: middle;
            justify-content: center;
            align-items: center;
        }

        .styleguide td {
            vertical-align: middle;
            height:57px;

        }
        .filter{
            height:30px;
        }
        @media(max-width:500px) {
            .filter{
            height:20px;
        }
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
                        <img class="H" alt="Brand" src="../image/letter-h.png">
                    </a></div>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li style="padding-top:15px"><a href="../board_list.php">우수직원 추천 게시판 <span class="sr-only">(current)</span></a></li>
                    <li style="padding-top:15px"><a href="../board_chart.php">우수직원 순위현황</a></li>
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
                    <li><span style=" padding-top:30px;padding-right:15px;border-radius: 4px; float:right; background-image:url(../image/love.gif)"><b><?php echo $hello ?></b></li>
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

    <div class="container">
        <div class="styleguide">
            <h3 style="text-align: left"><img src="/lib/images/title_De.gif">직원 목록</h3>
            <table class="table table-striped" style="text-align: center;width: 100%">
                <colgroup>
                    <col style="width:50px" />
                    <col style="width:150px" />
                    <col style="width:150px" />
                    <col style="width:120px" />
                    <col style="width:150px" />
                    <col style="width:100px" />
                    <col style="width:100px" />
                </colgroup>
                <thead>
                    <tr>
                        <th style="text-align: center;">No.</th>
                        <th style="text-align: center;">ID</th>
                        <th style="text-align: center;"> 성명</th>
                        <th style="text-align: center;">직위</th>
                        <th style="text-align: center;">부서</th>
                        <th style="text-align: center;">지역</th>
                        <th style="text-align: center;">관리</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $str_data_row; ?>
                </tbody>
                <tfoot>
                    
                    <tr>
                        <td colspan="7" style="text-align:center;padding-top:50px"><?php echo $str_page_bar; ?></td>
                    </tr>
                </tfoot>
            </table>
            <br />
            <form id="frmSearch" name="frmSearch" method="get" aciton="<?php echo $_SERVER["SCRIPT_NAME"] ?>" onsubmit="goSearch(this); return false;">
                <input type="hidden" id="mode" name="mode" value="search" />
                <select id="s_type" name="s_type" class="filter">
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
<br />
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
    function logout() {
        location.href = "../board_logout.php";
    }
    </script>
</body>

</html>
