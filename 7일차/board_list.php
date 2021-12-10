<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if(!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);
require_once  _LIB_PATH_ . "nav_page.class.php";
$nav = new NavPage;
$nav->start(5,10);

$db = new DB;
//$db->Debug = true;
$main_sql = "SELECT b.con_datetime, b.con_title, u.user_name, de.dept_name, b.con_vc , du.duty_name
            FROM ex_user_set u, ex_dept_set de, board_contents b, ex_duty_set du
            WHERE b.wr_user = u.user_id and b.wr_dept = de.dept_no and b.wr_duty = du.duty_no";
$sql = "WITH A as (
    {$main_sql}
)
SELECT COUNT(*) AS CNT FROM A";

$count = $db->query_one($sql);
$str_page_bar = $nav->navpage($count);
$sql = $main_sql;
$db->query_limit($sql, $nav->start_row, $nav->row_scale);
$query_row_count = $db->nf();

$str_data_row = null;
if($query_row_count > 0)
{
    $i = 0;
    while($db->next_record())
    {
        $row = $db->Record;
        $no = $count - $nav->start_row - $i;
        $date = substr($row["con_datetime"],0,10);
        $dept_user = "[".$row["dept_name"]."] ".$row["user_name"]." ".$row["duty_name"];
        $str_data_row .= "              
        <tr>
            <td style=\"text-align: center;\">{$no}</td>
            <td style=\"text-align: center;\">{$date}</a></td>
            <td>{$row["con_title"]}</td>
            <td>{$dept_user}</td>
            <td style=\"text-align: center;\">{$row["con_vc"]}</td>
        </tr>
           "  ;
    $i++;
    }
}else {
    $str_data_row = '      
            <tr>
                <td style="text-align: center;" colspan="5">페이지를 찾을 수 없습니다.</td>
            </tr>
          ';
}
?>

<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="ko"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="ko"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="ko"> <![endif]--> 
<!--[if gt IE 8]><!--> <html class="no-js" lang="ko"> <!--<![endif]-->
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>List Page</title>
        <style>
        table.type07 {
        border-collapse: collapse;
        text-align: left;
        line-height: 1.5;
        border: 1px solid #ccc;
        margin: 50px 400px;
        }
        table.type07 thead {
        border-right: 1px solid #ccc;
        border-left: 1px solid #ccc;
        background: #6666FF;
        }
        table.type07 thead th {
        padding: 10px;
        font-weight: bold;
        vertical-align: top;
        color: #fff;
        }
        table.type07 tbody th {
        width: 150px;
        padding: 5px;
        font-weight: bold;
        vertical-align: top;
        border-bottom: 1px solid #ccc;
        background: #fcf1f4;
        }
        table.type07 td {
        /* width: 350px; */
        padding: 5px;
        vertical-align: top;
        border-bottom: 1px solid #ccc;
        }
        </style>
    </head>
    <body>
        <table class="type07">
            <thead>
            <tr>
                <th scope="cols" style="width: 25px; text-align :center;">NO.</th>
                <th scope="cols" style="width: 250px; text-align :center;">작성일</th>
                <th scope="cols" style="width: 650px;">TITLE</th>
                <th scope="cols" style="width: 300px;">작성자</th>
                <th scope="cols" style="width: 75px; text-align :center;">조회수</th>
            </tr>
            </thead>
            <tbody>
                <?php echo $str_data_row;?>  
            </tbody>
        </table>
        <?php echo $str_page_bar;?>
    </body>
</html>
