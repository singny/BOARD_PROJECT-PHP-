<?php
$_table_user = "EX_USER_SET";
$_table_dept = "V_EX_DEPT_SET";
$_table_duty = "EX_DUTY_SET";
$_table_board = "BOARD_CONTENTS";
$_table_vc = "VIEW_COUNT";
$_table_gc = "GOOD_COUNT";
$_table_comment = "BOARD_COMMENT";

$_query_string = Fun::getParamUrl("uno,mode");
$_list_uri = "user_list.php?" . $_query_string;
$_view_uri = "user_view.php?" . $_query_string;
$_write_uri = "user_write.php?mode=write" . $_query_string;
$_modify_uri = "user_write.php?mode=modify" . $_query_string;
?>
