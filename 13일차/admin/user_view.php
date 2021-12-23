<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if(!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);
include './_inc.php';

$NO = $_REQUEST["uno"]??5;
$db = new DB;

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
    AND UNO = {$NO}
";
//Fun::print_($sql);
//exit;
$db->query($sql);
$db->next_record();
$rec = $db->Record;
//Fun::print_r($row);
//$query_string = Fun::getParamUrl("uno");
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
		function goModify(){ //== EDIT == UPDATE == MODIFY
			location.href = "<?php echo $_modify_uri; ?>&uno=<?php echo $NO; ?>";
		}
		</script>
    </head>
<body>
<div class="styleguide" style="text-align:center">
    <center>
    <h3 style="text-align: left; width: 800px"><img src="/lib/images/title_De.gif">직원 정보</h3>
    <table style="width:800px">
        <colgroup>
            <col style="width:250px" />
            <col />
        </colgroup>
        <caption>사업 Reference</caption>
        <tbody>
            <tr>
                <th class="th_col">USER_ID</th>
                <td><?php echo $rec["user_id"]; ?></td>
            </tr>
            <tr>
                <th>사용자명</th>
                <td><?php echo $rec["user_name"]; ?></td>
            </tr>
            <tr>
                <th>직위</th>
                <td><?php echo $rec["duty_name"] ?></td>
            </tr>
            <tr>
                <th>부서명</th>
                <td><?php echo str_replace("/(주)하이테크엔지니어링/", "", $rec["dept_name_path"]); ?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td><img src="/lib/images/btn_simple_list.gif" onclick="goList()" title="목록"/></td>
                <td style="text-align:right"><img src="/lib/images/btn_simple_mod.gif" onclick="goModify()" title="수정" align="right" /></td>
            </tr>
        </tfoot>
    </table>
    </center>
</div>
</body>
</html>
