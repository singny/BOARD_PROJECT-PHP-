<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if(!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);
?><!DOCTYPE html>
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
        background: #e7708d;
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
                <th scope="cols" style="width: 150px;">NO.</th>
                <th scope="cols" style="width: 250px;">작성일</th>
                <th scope="cols" style="width: 800px;">TITLE</th>
                <th scope="cols" style="width: 250px;">작성자</th>
                <th scope="cols" style="width: 150px;">조회수</th>
            </tr>
            </thead>
            <tbody>
<?php
$db = new DB;
//$db->Debug = true;
$sql = "SELECT * FROM board_contents";
$db->query($sql);
$count = $db->nf();
if($count > 0)
{
    $i = 0;
    while($db->next_record())
    {
        $row = $db->Record;
        $date = substr($row["con_datetime"],0,10)
?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td><?php echo $date;?></td>
                    <td><?php echo $row["con_title"]; ?></td>
                    <td><?php echo $row["wr_user"]; ?></td>
                    <td><?php echo $row["con_vc"]; ?></td>
                </tr>
<?php
        $i++;
    }
}
?>
            </tbody>
        </table>
    </body>
</html>
