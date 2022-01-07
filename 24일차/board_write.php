<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

@include_once "_inc.php";

$db = new DB;

$dept_id = null;
$uno = null;
$re_user = null;


$mode = $post["mode"];

if ($post["mode"] == "write") {
    $con_date = date("Y.m.d");
    $con_title = "";
    $con_body = "";
    $con_no = "";


    $sql = "SELECT * FROM {$_table_dept} WHERE LEVEL_NO=3 OR LEVEL_NO=4";
    $opt_dept_id = null;
    $opt_user_name = null;
    $db->query($sql);

    if ($db->nf() > 0) {
        while ($db->next_record()) {
            $row[0] = $db->Record;
            $_sel = "";


            $txt = str_replace(" ", "&nbsp", $db->f("dept_name_lvl"));
            $opt_dept_id .= "<option value=\"{$db->f("dept_no")}\" title=\"{$db->f("dept_name_path")}\">{$txt}</option>\n";
        }
    }

    $user_sql = "SELECT * FROM EX_USER_SET";
    $db->query($user_sql);

    if ($db->nf() > 0) {
        while ($db->next_record()) {
            $row[1] = $db->Record;

            $txt2 = str_replace(" ", "&nbsp", $db->f("user_name"));
            $opt_user_name .= "<option value=\"{$db->f("uno")}\" title=\"{$db->f("user_name")}\">{$txt2}</option>\n";
        }
    }

    $wr_user = null;
    $user_sql = "SELECT u.user_name, de.dept_name, du.duty_name, u.uno
FROM ex_user_set u, ex_dept_set de, ex_duty_set du
WHERE u.user_id = '{$_SESSION["user_id"]}' and u.dept_id = de.dept_no and u.duty_id = du.duty_no";

    $db->query($user_sql);
    while ($db->next_record()) {
        $row[3] = $db->Record;
        $wr_user = "[" . $row[3]["dept_name"] . "] " . $row[3]["user_name"] . " " . $row[3]["duty_name"];
        $uno = $row[3]["uno"];
    }

    $file = "<div class=\"row\" style=\"margin-left:70px\">
            <input type=\"file\" name=\"userfile\" onchange=\"previewFile()\"><br>
            <img src=\"\" id=\"image\" width=\"300px\" class=\"img-thumbnail\" onError=\"this.style.visibility='hidden'\">
            </div>";

    // 뒤로 가기 버튼
    $button = "goList()";
} else if ($post["mode"] == "modify") {
    $con_no = $_REQUEST["con_no"];
    $main_sql = "SELECT b.con_datetime, b.con_title, u.user_name, de.dept_name, b.con_vc , du.duty_name, b.con_body, de.dept_no, u.uno, du.duty_no
                FROM ex_user_set u, ex_dept_set de, board_contents b, ex_duty_set du
                WHERE b.wr_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no and b.con_no={$con_no}";
    $db->query($main_sql);
    $db->next_record();
    $row[2] = $db->Record;
    $con_date = substr($row[2]["con_datetime"], 0, 10);
    $wr_user = "[" . $row[2]["dept_name"] . "] " . $row[2]["user_name"] . " " . $row[2]["duty_name"];
    $con_title = $row[2]["con_title"];
    $wr_dept = $row[2]["dept_no"];
    $wr_duty = $row[2]["duty_no"];
    $body = Fun::convDB2Val($row[2]["con_body"]);
    $con_body = nl2br($body);

    $user_sql = "SELECT * FROM EX_USER_SET WHERE IS_USE='Y' AND IS_ACTIVE='Y'";
    $db->query($user_sql);
    $opt_user_name = null;
    if ($db->nf() > 0) {
        while ($db->next_record()) {
            $row[1] = $db->Record;
            $_sel2 = "";
            if ($db->f("uno") == $_REQUEST["re_user"]) {
                $_sel2 = " selected";
            }
            $txt2 = str_replace(" ", "&nbsp", $db->f("user_name"));
            $opt_user_name .= "<option value=\"{$db->f("uno")}\"{$_sel2} title=\"{$db->f("user_name")}\">{$txt2}</option>\n";
        }
    }

    $file_sql = "SELECT CON_NO, FILE_PATH FROM {$_table_board} WHERE CON_NO = {$con_no}";
    $db->query($file_sql);
    while ($db->next_record()) {
        $row[4] = $db->Record;
    }


    @$file_name = basename($row[4]["FILE_PATH"]);

    if ($file_name) {
        $file = "<div class=\"row\" id=\"attach\">
        <div class=\"col-25\">
        <label for=\"re_user\">첨부파일</label>
        </div>
        <div class=\"col-75\">
        <h4>&nbsp;$file_name</h4>
        </div>
        </div>
        <div class=\"row\">
        <div class=\"col-25\">
        <label for=\"re_user\">파일변경</label>
        </div>
        <div class=\"col-75\" style=\"margin-top:15px\">
        <input type=\"file\" name=\"userfile\" onchange=\"previewFile()\"><br>
        <img src=\"\" id=\"image\" class=\"img-thumbnail\" height=\"300\" onError=\"this.style.visibility='hidden'\">
        </div>
        </div>";
    } else {
        $file = "<div class=\"row\" style=\"margin-left:88px\">
        <input type=\"file\" name=\"userfile\" onchange=\"previewFile()\"><br>
        <img src=\"\" id=\"image\" class=\"img-thumbnail\" height=\"300\" onError=\"this.style.visibility='hidden'\">
        </div>";
    }

    // 뒤로가기 버튼
    $button = "goView()";
} else {
    Fun::alert("정상적인 방법으로 접속하여 주세요.");
    exit;
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
    <!-- 합쳐지고 최소화된 최신 CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

<!-- 부가적인 테마 -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

<!-- 합쳐지고 최소화된 최신 자바스크립트 -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gowun+Dodum&display=swap" rel="stylesheet">
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
            /* background-color: #6495ED;
            color: white; */
            background-color: white;
            box-shadow: rgba(30, 22, 54, 0.4) 0 0px 0px 2px inset;
            color: rgba(30, 22, 54, 0.6);
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 41%;
        }

        input[type=button]:hover {
            color: rgba(255, 255, 255, 0.85);
            background-color:rgba(255, 255, 255, 0.85);
            box-shadow: rgba(30, 22, 54, 0.7) 0 80px 0px 2px inset;

        }

        .container {
            border-radius: 15px;
            background-color: ghostwhite;
            padding: 50px;
            max-width: 900px;
            margin-left: 27%;
            margin-top: 3%;
            margin-bottom: 3%;
        }

        .col-25 {
            float: left;
            width: 10%;
            margin-top: 6px;
        }

        .col-75 {
            float: left;
            width: 85%;
            margin-top: 6px;
        }

        .col-50 {
            float: left;
            width: 37.5%;
            margin-top: 6px;
        }

        .col-10 {
            float: left;
            width: 2%;
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
        <form id="frmid01" name="frmid01" method="post" enctype="multipart/form-data" action="board_action.php" onsubmit="return false;">
            <input type="hidden" id="mode" name="mode" value="<?php echo $post["mode"]; ?>" />
            <input type="hidden" id="con_no" name="con_no" value="<?php echo $con_no; ?>" />
            <input type="hidden" id="uno" name="uno" value="<?php echo $uno; ?>" />
            <h2>올해의 우수직원을 추천하세요</h2>
            <div class="row">
                <div class="col-25">
                    <label for="con_title">제목</label>
                </div>
                <div class="col-75">
                    <input type="text" id="con_title" name="con_title" placeholder="제목을 입력하세요" value="<?php echo $con_title; ?>" />
                </div>
            </div>
            <div class="row" style="text-align:left;">
                <span class="col-25">
                    <label for="wr_user">작성자</label>
                </span>
                <span class="col-50">
                    <input type="text" id="wr_user" name="wr_user" value="<?php echo $wr_user; ?>" disabled readonly />
                </span>
                <span class="col-25">
                    <label for="con_datetime">&nbsp;&nbsp;작성일</label>
                </span>
                <span class="col-50">
                    <input type="text" id="con_datetime" name="con_datetime" value="<?php echo $con_date; ?>" disabled readonly>
                </span>
            </div>
            <div class="row">
                <span class="col-25">
                    <label for="re_user">추천직원</label>
                </span>
                <span class="col-50">
                    <select id="re_user" name="re_user">
                        <option value="">-- 직원 선택 --</option>
                        <?php echo $opt_user_name; ?>
                    </select>
                </span>
            </div>
            <div class="row">
                <span class="col-25">
                    <label for="con_body">본문</label>
                </span>
                <span class="col-75">
                    <textarea id="con_body" name="con_body" style="height:200px"><?php echo $con_body; ?></textarea>
                </span>
            </div>

            <?php echo $file ?>
            <br> <br>
            <div class="row">
                <input type="button" value="뒤로" onclick="<?php echo $button?>">
                <input type="button" value="저장" onclick="goSubmit()">
            </div>
        </form>
        
        <br />
    </div>
    <script type="text/javascript">
        function goList() {
            location.href = "<?php echo $_list_uri; ?>";
        }

        function goView(){
            location.href = "board_view.php?con_no=<?php echo $con_no?>"
        }

        function goSubmit() {
            var frm = document.forms["frmid01"];

            if (!frm.con_title.value) {
                alert("제목을 입력하세요.");
                frm.con_title.focus();
            } else if (!frm.re_user.value) {
                alert("추천직원을 선택하세요.");
                frm.re_user.focus();
            } else if (!frm.con_body.value) {
                alert("본문을 입력하세요.");
                frm.con_body.focus();
            } else {
                frm.submit();
            }
        }

        function previewFile() {
            var preview = document.querySelector('img');
            var file = document.querySelector('input[type=file]').files[0];
            var reader = new FileReader();

            reader.addEventListener("load", function() {
                preview.src = reader.result;
            }, false);

            if (file) {
                reader.readAsDataURL(file);
                var reader_image = document.getElementById("image").style.visibility = "visible";
            }
            var attach = document.getElementById("attach").hidden = true;

        }
    </script>
</body>

</html>
