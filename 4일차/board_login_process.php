<?php
$id = "separk2111";
$password = "separk2111";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(!(empty($_POST['id']) && empty($_POST['password']))) {
        if($_POST['id'] == $id && $_POST['password'] == $password){
            header("Location: ./board_view.php");
        }
    }
}
echo "로그인에 실패하였습니다."
?> 
