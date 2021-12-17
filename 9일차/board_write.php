<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);
include "_inc.php";

$db = new DB;

$dept_id = null;
$uno = null;

$_table_user = "EX_USER_SET";
$_table_dept = "V_EX_DEPT_SET";
$_table_duty = "EX_DUTY_SET";

if ($post["mode"] == "write") {
    $con_date = date("Y.m.d");
    $dept_user = "";
    $con_title = "";
    $con_body = "";

    
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
        $_sel2 = "";

        $txt2 = str_replace(" ", "&nbsp", $db->f("user_name"));
        $opt_user_name .= "<option value=\"{$db->f("uno")}\" title=\"{$db->f("user_name")}\">{$txt2}</option>\n";
    }
}
} else if 
($post["mode"] == "modify") {
    $con_no = $_REQUEST["con_no"];
    $main_sql = "SELECT b.con_datetime, b.con_title, u.user_name, de.dept_name, b.con_vc , du.duty_name, b.con_body, b.re_dept
                FROM ex_user_set u, ex_dept_set de, board_contents b, ex_duty_set du
                WHERE b.wr_user = u.uno and b.wr_dept = de.dept_no and b.wr_duty = du.duty_no and b.con_no={$con_no}";
    $db->query($main_sql);
    $db->next_record();
    $row[2] = $db->Record;
        $con_date = substr($row[2]["con_datetime"], 0, 10);
        $dept_user = "[" . $row[2]["dept_name"] . "] " . $row[2]["user_name"] . " " . $row[2]["duty_name"];
        $con_title = $row[2]["con_title"];
        $con_body = $row[2]["con_body"];


        $sql = "SELECT * FROM {$_table_dept} WHERE LEVEL_NO=3 OR LEVEL_NO=4";
        $opt_dept_id = null;
        $opt_user_name = null;
        $db->query($sql);
        
        if ($db->nf() > 0) {
            while ($db->next_record()) {
                $row[0] = $db->Record;
                $_sel = "";
                
                if($db->f("dept_no") == $_REQUEST["re_dept"]){
                    $_sel = " selected";
                }
                $txt = str_replace(" ", "&nbsp", $db->f("dept_name_lvl"));
                $opt_dept_id .= "<option value=\"{$db->f("dept_no")}\"{$_sel} title=\"{$db->f("dept_name_path")}\">{$txt}</option>\n";
            }
        }
        
        $user_sql = "SELECT * FROM EX_USER_SET";
        $db->query($user_sql);
        
        if ($db->nf() > 0) {
            while ($db->next_record()) {
                $row[1] = $db->Record;
                $_sel2 = "";
        
                if($db->f("uno") == $_REQUEST["re_user"]){
                    $_sel2 = " selected";
                }
                $txt2 = str_replace(" ", "&nbsp", $db->f("user_name"));
                $opt_user_name .= "<option value=\"{$db->f("uno")}\"{$_sel2} title=\"{$db->f("user_name")}\">{$txt2}</option>\n";
            }
        }
}
else
{
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
            padding: 50px;
            max-width: 1100px;
            margin-left: 22.5%;
            margin-top: 7%;
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
        <form id="frmid01" name="frmid01" method="post" action="board_action.php" onsubmit="return false;">
        <input type="hidden" id="mode" name="mode" value="<?php echo $post["mode"];?>" />
        <input type="hidden" id="con_no" name="con_no" value="<?php echo $con_no;?>" />
            <h2>올해의 우수직원을 추천하세요</h2>
            <div class="row">
                <div class="col-25">
                    <label for="con_title">제목</label>
                </div>
                <div class="col-75">
                    <input type="text" id="con_title" name="con_title" placeholder="제목을 입력하세요" value="<?php echo $con_title;?>"/>
                </div>
            </div>
            <div class="row" style="text-align:left;">
                <span class="col-25">
                    <label for="wr_user">작성자</label>
                </span>
                <span class="col-50">
                    <input type="text" id="wr_user" name="wr_user" value="<?php echo $dept_user;?>">
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
                    <select id="re_dept" name="re_dept">
                        <option value="">::부서 선택::</option>
                        <?php echo $opt_dept_id; ?>
                    </select>
                </span>
                <span class="col-25">
                    <label for="re_user"></label>
                </span>
                <span class="col-50">
                    <select id="re_user" name="re_user">
                        <option value="">::직원 선택::</option>
                        <?php echo $opt_user_name; ?>
                    </select>
                </span>
            </div>
            <div class="row">
                <span class="col-25">
                    <label for="con_body">본문</label>
                </span>
                <span class="col-75">
                    <textarea id="con_body" name="con_body" style="height:200px"><?php echo $con_body;?></textarea>
                </span>
            </div>
            <br> <br>
            <div class="row">
                <input type="button" value="뒤로" onclick="goList()">
                <input type="button" value="저장" onclick="goSubmit()">
            </div>
        </form>
    </div>
    <script type="text/javascript">
            function goList(){
                location.href = "<?php echo $_list_uri; ?>";
            }
        function goSubmit(){
            var frm = document.forms["frmid01"];

            if(!frm.con_title.value){
                alert("제목을 입력하세요.");
                frm.con_title.focus();
            }
            else if(!frm.re_dept.value){
                alert("추천직원의 부서를 선택하세요.");
                frm.re_dept.focus();

            }
            else if(!frm.re_user.value){
                alert("추천직원을 선택하세요.");  
                frm.re_user.focus();
            }
            else if(!frm.con_body.value){
                alert("본문을 입력하세요.");
                frm.con_body.focus();
            }
            else {
                frm.submit();
            }
        }
    </script>
</body>

</html>
