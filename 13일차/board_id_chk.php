<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include_once "_inc.php";

$db = new DB;
@$sql = "SELECT user_id FROM EX_USER_SET WHERE user_id = '{$post['user_id']}'";
$db->query($sql);
$count = $db->nf();

$chk = null;
if (@$post['user_id'] == ''){
    $chk = "아이디를 입력하세요";
} else if ($count == "0") {
    $chk =  "사용가능한 아이디 입니다.";
} else if ($count == "1") {
    $chk =  "중복된 아이디 입니다.";
}
@$script = "<script>    function id_close(){
    if('{$post["user_id"]}' == ''){
        var parent = window.opener;
        window.close(\"board_id_chk.php\");
        parent.document.getElementById('user_id').focus();  
    }
    if({$count} == \"0\"){
    window.close(\"board_id_chk.php\");
    }else if ({$count} == \"1\"){
        
        var parent = window.opener;
        window.close(\"board_id_chk.php\");
        parent.document.getElementById('user_id').value = '';
        parent.document.getElementById('user_id').focus();
    }
}</script>"
?>
<!DOCTYPE html>
<html>

<body>
    <h3 style="text-align:center"><?php echo $chk ?></h3>
    <br /><br />
    <div style="text-align:center">
        <input type="button" onclick="id_close()" value="확인">
    </div>
</body>
<?php echo $script?>
</html>
