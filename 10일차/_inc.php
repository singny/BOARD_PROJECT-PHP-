<?php
$_table_user = "EX_USER_SET";
$_table_dept = "V_EX_DEPT_SET";
$_table_duty = "EX_DUTY_SET";
$_table_board = "BOARD_CONTENTS";

$_query_string = Fun::getParamUrl("uno,mode");
$_list_uri = "board_list.php?" . $_query_string;
$_view_uri = "board_view.php?" . $_query_string;
$_modify_uri = "board_write.php?mode=modify" . $_query_string;
$_write_uri = "board_write.php?mode=write" . $_query_string;
?>
