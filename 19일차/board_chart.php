<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
include "_inc.php";
@error_reporting(E_ALL);

$db = new DB;

$score_sql = "SELECT ROWNUM AS RANK ,RE_USER, SCORE FROM (SELECT * FROM V_SCORE ORDER BY SCORE DESC) WHERE ROWNUM <= 5";
$db->query($score_sql);
$row = $db->RecordAll;

for ($i = 0; $i <= 5; $i++) {
    $re_sql = "SELECT u.user_name, de.dept_name, du.duty_name, v.re_user, b.con_no
FROM ex_user_set u, ex_dept_set de, ex_duty_set du, v_score v, board_contents b
WHERE u.uno = {$row[$i]["RE_USER"]} and u.dept_id = de.dept_no and u.duty_id = du.duty_no";

    $db->query($re_sql);
    $db->next_record();
    $row[$i + 5] = $db->Record;

    $re_user[$i] = "[" . $row[$i + 5]["dept_name"] . "] " . $row[$i + 5]["user_name"] . " " . $row[$i + 5]["duty_name"];
}
?>
<!DOCTYPE html>
<html lang="en">
<!-- <html lang="en" style="height: 100%"> -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>부트스트랩 차트그리기</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> <!-- 차트 링크 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <style>
        .container {
            padding-top: 4%;
            width: 1100px;
            position: absolute;
            left: 220px;
            top: 130px;
        }

        @media(min-width:1900px) {
            .container {
                padding-top: 4%;
                position: relative;
                left: 0px;
                top: 0px;
            }
        }

        .logo {
            text-align: center;
            cursor: pointer;
            margin-top: 2%;
        }

        @media(min-width:1900px) {
            .logo {
                text-align: center;
                cursor: pointer;
                margin-top: 4%;
            }
        }
    </style>
</head>

<body>
    <div class="logo">
        <img src="./image/chart.png" style="max-width:900px" onclick="location.href='board_chart.php'" />
    </div>
    <!-- <div style="position:absolute;left:700px;top:250px"><img src="./image/hidden.png"></div> -->
    <div class="container"> <canvas id="myChart"></canvas> </div> <!-- 부트스트랩 -->
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
                    label: '차트숨김',
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
    </script>
</body>

</html>
