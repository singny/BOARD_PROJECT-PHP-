<?php
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
$_table_user = "EX_USER_SET";
$_table_dept = "V_EX_DEPT_SET";
$_table_duty = "EX_DUTY_SET";
$_table_board = "BOARD_CONTENTS";
$_table_vc = "VIEW_COUNT";
$_table_gc = "GOOD_COUNT";
$_table_comment = "BOARD_COMMENT";
$_table_message = "BOARD_MESSAGE";

$_query_string = Fun::getParamUrl("con_no,mode");
$_list_uri = "board_list.php?" . $_query_string;
$_view_uri = "board_view.php?" . $_query_string;
$_write_uri = "board_write.php?mode=write" . $_query_string;
?>
