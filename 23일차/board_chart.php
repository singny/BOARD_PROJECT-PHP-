<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
include "_inc.php";
@error_reporting(E_ALL);

$db = new DB;



if (@$_GET["con_year"] == 2021) {
    $_table_score = "V_SCORE21";
    $count_sql = "SELECT COUNT(*) FROM {$_table_score}";
    $n = $db->query_one($count_sql);
    if ($n < 5) {
        FUN::alert("데이터가 부족합니다.", "board_chart.php?con_year=2022");
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

$score_sql = "SELECT ROWNUM AS RANK ,RE_USER, SCORE FROM (SELECT * FROM {$_table_score} ORDER BY SCORE DESC) WHERE ROWNUM <= 5";
$db->query($score_sql);
$row = $db->RecordAll;

for ($i = 0; $i <= 5; $i++) {
    $re_sql = "SELECT u.user_name, de.dept_name, du.duty_name, v.re_user, b.con_no
FROM ex_user_set u, ex_dept_set de, ex_duty_set du, {$_table_score} v, board_contents b
WHERE u.uno = {$row[$i]["RE_USER"]} and u.dept_id = de.dept_no and u.duty_id = du.duty_no";

    $db->query($re_sql);
    $db->next_record();
    $row[$i + 5] = $db->Record;

    $re_user[$i] = "[" . $row[$i + 5]["dept_name"] . "] " . $row[$i + 5]["user_name"];
}



// 회원관리 페이지
$user_mng = null;
if (@$_SESSION["user_id"] == "admin") {
    $user_mng = "<li><a href=\"./admin/user_list.php\">회원관리</a></li>";
};
?>
<!DOCTYPE html>
<html lang="en">
<!-- <html lang="en" style="height: 100%"> -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>우수직원 순위현황</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> <!-- 차트 링크 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&family=Nanum+Pen+Script&family=Poor+Story&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="apple-touch-icon" sizes="57x57" href="../image/icon/apple-icon-57x57.png">
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
    <style>
        body {
            font-family: 'Jua', sans-serif;
        }

        @media(max-width:500px) {
            body {
            font-family: 'Jua', sans-serif;
            font-size:xx-small;
        }
        }

        .container {
            padding-top: 4%;
            width: 900px;
            position: absolute;
            left: 380px;
            top: 250px;

        }

        @media(min-width:1900px) {
            .container {
                padding-top: 4%;
                position: absolute;
                left: 250px;
                top: 250px;
                margin-left: 10%;
                width: 1200px;
                height: 547px;
            }
        }

        @media(max-width:500px) {
            .container {
                padding-top: 4%;
                position: absolute;
                left: 130px;
                top: 200px;
                margin-left: 0%;
                width: 500px;
                height: 0px;
            }
        }

        .logo {
            text-align: center;
            cursor: pointer;

        }

        @media(min-width:1900px) {
            .logo {
                text-align: center;
                cursor: pointer;

            }
        }

        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            width: 12.5%;
            background-color: #F0F8FF;
            position: fixed;
            height: 100%;
            overflow: auto;
        }

        li a {
            display: block;
            color: #000;
            padding: 8px 16px;
            text-decoration: none;
        }

        li a.active {
            background-color: #000080;
            color: white;
        }

        li a:hover:not(.active) {
            background-color: #000080;
            color: white;
        }

        li img {
            padding-top: 20px;
            padding-left: 40px;
            width: 150px
        }

        @media(min-width:1900px) {
            li img {
                padding-top: 20px;
                padding-left: 40px;
                width: 200px
            }
        }

        @media(max-width:500px) {
            li img {
                padding: 3px;
                width: 80px
            }

        }

        input[type=button] {
            background-color: #6495ED;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type=submit]:hover {
            background-color: #4682B4;

        }

        .container-fluid {
            margin-left: 46%;
            width: 400px;
            position: absolute;
            left: 300px;
            top: 200px;
            margin-top: 10px;
        }


        @media(max-width:500px) {
            .container-fluid {
            margin-left: 46%;
            width: 400px;
            position: absolute;
            left: 100px;
            top: 125px;
            margin-top: 20px;
        }

        }

        .lmg {
            max-width: 900px;
        }

        @media(max-width:500px) {
            .lmg {
                max-width: 500px;
                padding: 15px;
            }
        }
        .position{
            margin-left:10%;
            padding:1px 16px;
            height:900px;
        }

        @media(max-width:500px) {
            .position{
            margin-left:40%;
            padding:1px 16px;
            height:900px;
        }
        }
        
    </style>
</head>

<body>
    <ul>
        <li>
            <img src="./image/welcome2.png">
        </li>
        <li>&nbsp;</li>
        <li><a href="board_list.php">우수직원 게시판</a></li>
        <li><a class="active" href="board_chart.php">우수직원 순위현황</a></li>
        <?php echo $user_mng ?>
        <!-- <li><a href="#">소개</a></li>
  <li><a href="#">자유게시판</a></li> -->
    </ul>



    <div class="logo">
        <br /><br />
        <div class="position">
            <img src="./image/chart.png" class="lmg" onclick="location.href='board_chart.php'" />
        </div>
    </div>
    <div class="container-fluid">
        <form class="d-flex" method="get" aciton="<?php echo $_SERVER["SCRIPT_NAME"] ?>">
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal">
                점수기준
            </button>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">점수 기준</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                        </div>
                    </div>
                </div>
            </div>
            &nbsp;
            <select id="con_year" name="con_year" class="form-select" aria-label="Default select example" style="width:min-content">
                <option value="2022" <?php echo $_sel22 ?>>2022</option>
                <option value="2021" <?php echo $_sel21 ?>>2021</option>
            </select>
            &nbsp;
            <button type="submit" class="btn btn-secondary">Search</button>
        </form>
    </div>
    <div class="container"> <canvas id="myChart"></canvas> </div>

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
    </script>
</body>

</html>
