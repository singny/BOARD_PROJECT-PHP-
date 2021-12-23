<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if(!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);
include "_inc.php";

$db = new DB;

$NO = null;

$sql = "WITH A AS
(
SELECT 
    U.*
    , DE.DEPT_NAME
    , DE.DEPT_NAME_PATH
    , DE.DEPT_NO_PATH
    , DU.DUTY_NAME
FROM EX_USER_SET U 
    , V_EX_DEPT_SET DE
    , EX_DUTY_SET DU
WHERE 1 = 1
    AND U.DEPT_ID = DE.DEPT_NO(+)
    AND U.DUTY_ID = DU.DUTY_NO(+)
)
SELECT * FROM A
WHERE 1 = 1
";

if($post["mode"] == "write")
{
    $duty_id = null;
    $dept_id = null;
    $rec = array();
} 
else if($post["mode"] == "modify")
{
    $NO = $_REQUEST["uno"];
    $sql .= " AND UNO = {$NO}";
	//Fun::print_($sql);
	//exit;
	$db->query($sql);
	$db->next_record();
	$rec = $db->Record;
        $duty_id = $rec["duty_id"];
        $dept_id = $rec["dept_id"];
}
else
{
    Fun::alert("정상적인 방법으로 접속하여 주세요.");
    exit;
}
//Fun::print_r($row);
//$query_string = Fun::getParamUrl("uno");
//Fun::print_r($_GET);
//exit;

$sql = "SELECT * FROM {$_table_duty} ORDER BY SORT_NO asc";
$opt_duty_id = null;
$db->query($sql);
if($db->nf() > 0){
	while($db->next_record())
	{
            $_sel = "";
            if($duty_id == $db->f("duty_no")){
                $_sel = " selected";
            }
            $opt_duty_id .= "<option value=\"{$db->f("duty_no")}\"{$_sel}>{$db->f("duty_name")}</option>\n";
	}
}

$sql = "SELECT * FROM {$_table_dept} ORDER BY SORT_NO_PATH asc";
$opt_dept_id = null;
$db->query($sql);
if($db->nf() > 0)
{
	while($db->next_record())
	{
		$_sel = "";
		if($dept_id == $db->f("dept_no")){
                    $_sel = " selected";
		}
		$txt = str_replace(" ", "&nbsp", $db->f("dept_name_lvl"));
		$opt_dept_id .= "<option value=\"{$db->f("dept_no")}\"{$_sel} title=\"{$db->f("dept_name_path")}\">{$txt}</option>\n";
	}
}
?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="ko"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="ko"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="ko"> <![endif]--> 
<!--[if gt IE 8]><!--> <html class="no-js" lang="ko"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--<meta http-equiv="refresh" content="0;url=http://www.daum.net">-->
        <title>List Page</title>
        <link rel="stylesheet" type="text/css" href="http://www.htenc.co.kr/css/style.css" />
        <style type="text/css">
            .styleguide th
            {
                background-color: #c0c0c0;
                text-align: center;
                vertical-align:middle;
                justify-content:center;
                align-items:center;
            }
            .styleguide td
            {
                padding: 5px;
                text-align: left;
                vertical-align:middle;
                
            }
        </style>
        <script type="text/javascript">
            function goList(){
                location.href = "<?php echo $_list_uri; ?>";
            }
            function goSubmit(){
                var frm = document.forms["frmname01"];
<?php
if($post["mode"] == "write")
{
                echo 'if(frm.mode.value == "write" && !frm.user_id.value){
                    alert("사용자 ID를 입력하여 주세요.");
                    frm.user_id.focus();
                    return;
                }
                else if(frm.user_id_check.value == ""){
                    alert("사용자 ID중복을 체크하여 주세요.");
                    frm.user_id.focus();
                    return;
                }
                else if(frm.user_id_check.value == "false"){
                    alert("사용자 ID가 중복됩니다.다른 아이디를 입력 후 중복체크하여 주세요.");
                    frm.user_id.focus();
                    return;
                }';
}
?>
                
                if(!frm.user_name.value){
                    alert("사용자 명을 입력하여 주세요.");
                    frm.user_name.focus();
                }
                else if(!frm.duty_id.value){
                    alert("직위을 선택하여 주세요.");
                    frm.duty_id.focus();
                }
                else if(!frm.dept_id.value){
                    alert("부서를 선택하여 주세요.");
                    frm.dept_id.focus();
                }
                else {
                    frm.submit();
                }
            }
            
            function GoUserIdCheck(obj){
                document.getElementById("user_id_check").value = "";
                document.getElementById("ajax_message").innerHTML = "";
                //console.log(obj);
                //alert(obj);
                // Create an XMLHttpRequest object
                var xhttp = new XMLHttpRequest();

                // Define a callback function
                xhttp.onload = function() {
                  // Here you can use the Data
                    if(this.responseText != "")
                    {
                        document.getElementById("user_id_check").value = "false";
                        document.getElementById("ajax_message").innerHTML =  this.responseText;
                    }
                    else 
                    {
                        document.getElementById("user_id_check").value = "true";
                    }
                }

                // Send a request
                xhttp.open("GET", "jhpark_action.php?mode=userid_check&user_id=" + document.getElementById("user_id").value );
                xhttp.send();
            }
        </script>
    </head>
<body>
<form id="frmid01" name="frmname01" method="post" action="jhpark_action.php" onsubmit="return false;">
<input type="hidden" id="mode" name="mode" value="<?php echo $post["mode"];?>" />
<input type="hidden" id="uno" name="uno" value="<?php echo $NO;?>" />
<input type="hidden" id="user_id_check" name="user_id_check" value="" />
    <div class="styleguide" style="text-align:center">
        <center>
            <h3 style="text-align: left; width: 800px"><img src="/lib/images/title_De.gif">직원 정보 <?php echo ($post["mode"] == "modify" ? " - 수정" : " - 추가"); ?></h3>
        <table style="width:800px">
            <colgroup>
                <col style="width:250px" />
                <col />
            </colgroup>
            <caption>사업 Reference</caption>
            <tbody>
                <tr>
                    <th class="th_col">USER_ID</th>
                    <td>
                        <?php
                        if($post["mode"] == "write"){
                            echo '<input type="text" id="user_id" name="user_id" value="" required /> <input type="button" id="btnUserIdCheck" name="btnUserIdCheck" value="중복체크" onclick="GoUserIdCheck(this);" />';
                        }
                        else {
                            echo $rec["user_id"]??null;
                        }
                        ?>
                        <div id="ajax_message">&nbsp;</div>
                    </td>
                </tr>
                <tr>
                    <th>사용자명</th>
                    <td><input type="text" id="user_name" name="user_name" value="<?php echo $rec["user_name"]??null; ?>" required /></td>
                </tr>
                <tr>
                    <th>직위</th>
                    <td><select id="duty_id" name="duty_id" required>
                            <option value="">::직위 선택::</option>
                            <?php echo $opt_duty_id; ?>
                        </select><?php //echo $member["duty_name"] ?>
                    </td>
                </tr>
                <tr>
                    <th>부서명</th>
                    <td><select id="dept_id" name="dept_id" required>
                            <option value="">::부서 선택::</option>
                            <?php echo $opt_dept_id; ?>
                        </select><?php //echo str_replace("/(주)하이테크엔지니어링/", "", $member["dept_name_path"]); ?>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td><img src="/lib/images/btn_simple_list.gif" onclick="goList()" title="목록"/></td>
                    <td style="text-align:right"><img src="/lib/images/btn_simple_save.gif" onclick="goSubmit()" title="저장" align="right" /></td>
                </tr>
            </tfoot>
        </table>
        </center>
    </div>
	<!--<input type="submit" value="Submit" />-->
</form>
</body>
</html>
