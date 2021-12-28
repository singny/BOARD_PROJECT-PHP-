<?php
session_start();
require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
unset($_SESSION["user_id"]);
header("Location : board_login.php");
?>
