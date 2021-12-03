<?php
$id = "separk2111";
$password = "separk2111";
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if(!(empty($_GET['id']) && empty($_GET['password']))) {
        if($_GET['id'] == $id && $_GET['password'] == $password){
            header("Location: ./board_view.php");
        }
    }
}
echo "로그인에 실패하였습니다."
?> 
