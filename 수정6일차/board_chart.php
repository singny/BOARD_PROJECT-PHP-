<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);
require_once  _LIB_PATH_ . "nav_page.class.php";
include "_inc.php";
$nav = new NavPage;
$nav->start(10, 10);

$db = new DB;

//차트
if (@$_GET["con_year"] == 2021) {
    $_table_score = "V_SCORE21";
    $count_sql = "SELECT COUNT(*) FROM {$_table_score}";
    $n = $db->query_one($count_sql);
    if ($n < 5) {
        FUN::alert("데이터가 부족합니다.", "board_list.php");
    }
    $_sel21 = "selected";
    $_sel22 = "";
} else {
    $_table_score = "V_SCORE22";
    $count_sql = "SELECT COUNT(*) FROM {$_table_score}";
    $n = $db->query_one($count_sql);
    if ($n < 5) {
        FUN::alert("현재년도의 데이터가 부족합니다.", "board_chart.php?con_year=2021");
    }
    $_sel21 = "";
    $_sel22 = "selected";
}

$score_sql = "SELECT ROWNUM AS RANK ,RE_USER, SCORE FROM (SELECT * FROM {$_table_score} ORDER BY SCORE DESC)";
$db->query($score_sql);
$row = $db->RecordAll;

for ($i = 0; $i <= $n; $i++) {
    if(@$row[$i]["RE_USER"] == $_SESSION["uno"]){
    $rank = $row[$i]["RANK"];
    @$rank_ment = "해당 년도 나의 순위는 {$rank}위 입니다. ";
    }
}

for ($i = 0; $i <= 5; $i++) {
    $re_sql = "SELECT u.user_name, de.dept_name, du.duty_name, v.re_user, b.con_no
FROM ex_user_set u, ex_dept_set de, ex_duty_set du, {$_table_score} v, {$_table_board} b
WHERE u.uno = {$row[$i]["RE_USER"]} and u.dept_id = de.dept_no and u.duty_id = du.duty_no";


    $db->query($re_sql);
    $db->next_record();
    $row[$i + 5] = $db->Record;

    $re_user[$i] = "[" . $row[$i + 5]["dept_name"] . "] " . $row[$i + 5]["user_name"];    
}

// 유저 사진
$file_sql = "SELECT UNO, FILE_PATH FROM {$_table_user} WHERE user_id='{$_SESSION["user_id"]}'";
$db->query($file_sql);
while ($db->next_record()) {
    $row[11] = $db->Record;
    $uno = $row[11]["uno"];
}

@$file_name = basename($row[11]["FILE_PATH"]);
$user_img = null;

if ($file_name) {
    $user_img = "<div class=\"circle \" onclick=\"location.href='my_page.php';\" style=\"background-image: url('./upload_file/user/{$uno}/$file_name');\" title=\"마이페이지\"></div>";
} else {
    $user_img = "<div class=\"circle \" onclick=\"location.href='my_page.php';\" style=\"background-image: url('./image/user.png')\" title=\"마이페이지\"></div>";
}

// 쪽지함
$message_sql = "SELECT COUNT(*) FROM {$_table_message} WHERE re_user = {$row[11]["uno"]} and is_read='N'";
$n = $db->query_one($message_sql);

if ($n == 0) {
    $message = "쪽지함[0]";
    $li_message = "쪽지함[0]";
} else {
    $message = "<img src=\"./image/message.png\" width=\"20px\" height=\"20px\" /> 쪽지함[" . $n . "]";
    $li_message = "쪽지함[" . $n . "]";
}

// 알림 기능
$alert_sql = "SELECT b.con_no, b.con_title, de.dept_name, du.duty_name, u.user_name, a.is_read, to_char(a.a_datetime,'YYYY.MM.DD HH24:mi:ss') as a_datetime, u.is_active, u.is_use
                FROM board_contents b, board_alert a, ex_user_set u , ex_dept_set de, ex_duty_set du
                WHERE b.re_user = {$_SESSION["uno"]} and b.wr_user = a.wr_user and b.con_no = a.con_no and b.wr_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no
                ORDER BY con_datetime DESC";
$db->query($alert_sql);
$alert_data_row = null;
while ($db->next_record()) {
    $row[12] = $db->Record;

    if ($row[12]["is_use"] == 'N' || $row[12]["is_active"] == 'N') {
        $wr_user = "[" . $row[12]["dept_name"] . "] " . $row[12]["user_name"] . " " . $row[12]["duty_name"] . "&nbsp;<img src=\"image/delete-user.png\" title=\"탈퇴한 계정\" width=\"25px\" height=\"25px\">";
    } else {
        $wr_user = "[". $row[12]["dept_name"] ."] ".$row[12]["user_name"]." ".$row[12]["duty_name"];
    }

    if ($row[12]["is_read"] == 'N') {
        $alert_data_row .= "<tr onclick=\"location.href='board_view.php?con_no={$row[12]["con_no"]}'\" style=\"cursor:pointer\">
                                <td>{$wr_user}께서 회원님을 추천하는 글을 작성하였습니다.<br /><br />글 제목 : {$row[12]["con_title"]}<br />{$row[12]["a_datetime"]}</td>
                                <td><img src=\"image/read.png\" width=\"30px\" height=\"30px\"></td>
                            </tr>";
    } else {
        $alert_data_row .= "<tr onclick=\"location.href='board_view.php?con_no={$row[12]["con_no"]}'\" style=\"cursor:pointer\">
                                <td>{$wr_user}께서 회원님을 추천하는 글을 작성하였습니다.<br /><br />글 제목 : {$row[12]["con_title"]}<br />{$row[12]["a_datetime"]}</td>
                                <td>&nbsp;</td>
                            </tr>";
    }
}

$art_sql = "SELECT COUNT(*) FROM {$_table_alert} WHERE uno = {$_SESSION["uno"]} and is_read='N'";
$an = $db->query_one($art_sql);

if ($an == 0) {
    $alert = "<img src=\"./image/nobell.png\" width=\"20px\" height=\"20px\" /> 알림창[" . $an . "]";;
} else {
    $alert = "<img src=\"./image/bell.png\" width=\"20px\" height=\"20px\" /> 알림창[" . $an . "]";
}

// 관리자 계정
$user_mng = null;
if (@$_SESSION["user_id"] == "admin") {
    $user_mng = "<li style=\"padding-top:15px\"><a href=\"./admin/user_list.php\" >회원관리</a></li>";
};

// ~님 안녕하세요
$hello = null;
if (@$_SESSION["user_id"]) {
    $user_name_sql = "SELECT * FROM EX_USER_SET WHERE user_id='{$_SESSION["user_id"]}'";
    $db->query($user_name_sql);

    while ($db->next_record()) {
        $row[13] = $db->Record;
    }

    $hello = $row[13]["user_name"] . "님, 안녕하세요";
} else {

    FUN::alert("로그인 후 접속하세요", "board_login.php");
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="ko"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="ko"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="ko">
<!--<![endif]-->
<html>

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- 합쳐지고 최소화된 최신 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

    <!-- 부가적인 테마 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

    <!-- 합쳐지고 최소화된 최신 자바스크립트 -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&family=Nanum+Pen+Script&family=Poor+Story&display=swap" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="57x57" href="image/icon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="image/icon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="image/icon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="image/icon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="image/icon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="image/icon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="image/icon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="image/icon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="image/icon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="image/icon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="image/icon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="image/icon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="image/icon/favicon-16x16.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

</head>
<style>
    body {
        font-family: 'Jua', sans-serif;
        /* background-image: url('/image/love.gif'); */
        /* background-repeat: no-repeat; */
        background-position: center;
        /* background-size: cover; */
        font-size: medium;
    }

    @media(max-width:500px) {
        body {
            font-family: 'Jua', sans-serif;
            font-size: xx-small;
        }

    }

    .circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        float: right;
        margin-right: 0px;
        border: 3px solid #fff;
        box-shadow: 0 0 16px rgb(221, 221, 221);
        cursor: pointer;
        background-size: 47.5px 50px;
        margin-bottom: 15px;

    }

    a.button,
    a.button2 {
        -webkit-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
        -moz-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
        -ms-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
        -o-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
        transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
        display: block;
        /* margin: 20px auto; */
        width: 100px;
        text-decoration: none;
        border-radius: 4px;
        padding: 5px;
        text-align: center;
        cursor: pointer;
    }

    @media(max-width:500px) {
        a.button2 {
            -webkit-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
            -moz-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
            -ms-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
            -o-transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
            transition: all 200ms cubic-bezier(0.390, 0.500, 0.150, 1.360);
            display: block;
            /* margin: 20px auto; */
            width: 45px;
            text-decoration: none;
            border-radius: 4px;
            padding: 5px;
            text-align: center;
            cursor: pointer;
        }
    }

    a.button2 {
        color: rgba(30, 22, 54, 0.6);
        box-shadow: rgba(30, 22, 54, 0.4) 0 0px 0px 2px inset;
    }

    a.button2:hover {
        color: rgba(255, 255, 255, 0.85);
        box-shadow: rgba(30, 22, 54, 0.7) 0 80px 0px 2px inset;
    }

    .H {
        width: 50px;
        height: 50px;
    }

    @media(max-width:500px) {
        .H {
            width: 20px;
            height: 20px;
        }
    }

    .logo {
        max-width: 900px;
        padding-top: 1%;
    }

    @media(max-width:500px) {
        .logo {
            max-width: 360px;
            padding-top : 50px;
        }
    }

    .position {
        padding: 1px 16px;
        text-align: center;
        cursor: pointer;
        margin-top: 5%
    }

    @media(max-width:500px) {
        .position {
            text-align: center;
            padding: 1px 16px;
        }
    }

    .my_score{
        float:left;
        width:5%;
        margin-left: 23%
    }

    @media(min-width:1900px) {
        .my_score{
        float:left;
        width:5%;
        margin-left: 22%
    }

    }
    @media(max-width:500px) {
        .my_score{
        float:left;
        width:5%;
        margin-left: 1%
    }
    }

    .standard {
        float: left;
        width: 7%;
        margin-left: 35%
    }

    @media(min-width:1900px) {
        .standard {
            float: left;
            width: 5.5%;
            margin-left: 40%
        }

    }

    @media(max-width:500px) {
        .standard {
            float: left;
            width: 15%;
            margin-left: 50%
        }
    }

    .year {
        float: left;
        width: 4.5%
    }

    @media(min-width:1900px) {
        .year {
            float: left;
            width: 3.5%
        }
    }

    @media(max-width:500px) {
        .year {
            float: left;
            width: 16%
        }
    }
    .search {
        float: left;
        width: 5%
    }

    @media(min-width:1900px) {
        .search {
            float: left;
            width: 5%
        }
    }

    .container {
        width: 900px;
    }

    @media(min-width:1900px) {
        .container {

            width: 1200px;

        }
    }

    @media(max-width:500px) {
        .container {
            margin-left: 0%;
            width: 375px;
            height: 0px;
        }
    }
</style>
</head>

<body>
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div><a class="navbar-brand" href="board_list.php">
                        <img class="H" alt="Brand" src="image/letter-h.png">
                    </a></div>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li style="padding-top:15px"><a href="board_list.php">우수직원 추천 게시판 <span class="sr-only">(current)</span></a></li>
                    <li class="active" style="padding-top:15px"><a href="board_chart.php">우수직원 순위현황</a></li>
                    <?php echo $user_mng ?>
                    <!-- <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li class="divider"></li>
            <li><a href="#">One more separated link</a></li>
          </ul>
        </li> -->
                </ul>
                <!-- <form class="navbar-form navbar-left" role="search">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form> -->
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#"><?php echo $user_img ?></a></li>
                    <li><span style=" padding-top:30px;padding-right:15px;border-radius: 4px; float:right; background-image:url(image/love.gif)"><b><?php echo $hello ?></b></li>
                    <li>
                        <div style="float:right; padding-top:22px;padding-right:10px"><a class="button2" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal"><?php echo $alert ?></a></div>
                    </li>
                    <li>
                        <div style="float:right; padding-top:22px"><a class="button2" onclick="goMessage()"><?php echo $message ?></a></div>
                    </li>
                    <li>
                        <div style="float:right;padding-top:22px;padding-left:10px;padding-right:20px;"><a class="button2" onclick="logout()">로그아웃</a></div>
                    </li>

                    <!-- Button trigger modal -->


                    <!-- Modal -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">알림창</h4>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-hover">
                                        <tbody>
                                            <?php echo $alert_data_row; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
          </ul>
        </li> -->
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>



    <div class="position">
        <img src="./image/chart.png" class="logo" onclick="location.href='board_chart.php'" />
    </div>
    <br />
    <div class="container-fluid">
        <form class="d-flex" method="get" action="<?php echo $_SERVER["SCRIPT_NAME"] ?>">
                        <!-- Modal Trigger -->
                        <div class="my_score"><a class="button2" class="btn btn-warning" data-toggle="modal" data-target="#my_score">내 순위</a></div>

<!-- Modal -->
<div class="modal fade" id="my_score" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">내 순위</h4>
            </div>
            <div class="modal-body">
                <?php echo $rank_ment??"랭크하지 못하였습니다 :)<br /> 분발하세요 !"?><br />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>
            <!-- Modal Trigger -->
            <div class="standard"><a class="button2" class="btn btn-warning" data-toggle="modal" data-target="#score">점수기준</a></div>

            <!-- Modal -->
            <div class="modal fade" id="score" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">점수 기준</h4>
                        </div>
                        <div class="modal-body">
                            추천 게시글 수 * 5<br />
                            추천 게시글에 달린 댓글의 수 * 3<br />
                            추천 게시글의 좋아요 수 * 2<br />
                            추천 게시글의 조회수 * 1<br />
                            <br />
                            -------------------------------<br />
                            <br />
                            위 항목의 합산으로 점수가 책정됩니다.
                            <br />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                        </div>
                    </div>
                </div>
            </div>

            &nbsp;
            <div class="year">
                <select id="con_year" name="con_year" class="form-select" aria-label="Default select example" style="height:32px;">
                    <option value="2022" <?php echo $_sel22 ?>>2022</option>
                    <option value="2021" <?php echo $_sel21 ?>>2021</option>
                </select>
            </div>
            &nbsp;
            <div class="search"><button type="submit" class="btn btn-secondary">Search</button></div>
        </form>
    </div>
    <div class="container"> <canvas id="myChart"></canvas> </div>
</body>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> <!-- 차트 -->
<script>
    var ctx = document.getElementById('myChart');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['1등 : <?php echo $re_user[0] ?>', '2등 : <?php echo $re_user[1] ?>', '3등 : <?php echo $re_user[2] ?>', '4등 : <?php echo $re_user[3] ?>', '5등 : <?php echo $re_user[4] ?>'],
            datasets: [{
                label: 'Score',
                data: [<?php echo $row[0]["SCORE"] ?>, <?php echo $row[1]["SCORE"] ?>, <?php echo $row[2]["SCORE"] ?>, <?php echo $row[3]["SCORE"] ?>, <?php echo $row[4]["SCORE"] ?>],
                backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)'],
                borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });

    var myModal = document.getElementById('myModal')
    var myInput = document.getElementById('myInput')

    myModal.addEventListener('shown.bs.modal', function() {
        myInput.focus()
    })

    function logout() {
        location.href = "board_logout.php";
    }

    function goMessage() {
        var url = "message_list.php";
        var name = "popup test";
        var option = "width = 1000, height = 650, top = 100, left = 200, location = no"
        window.open(url, name, option);
    }
</script>

</html>
