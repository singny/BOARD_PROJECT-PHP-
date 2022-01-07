<?php

session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include "_inc.php";



$db = new DB;

$user_sql = "SELECT * FROM {$_table_user} WHERE user_id = '{$_SESSION["user_id"]}'";
$db->query($user_sql);
$db->next_record();
$row[1] = $db->Record;

// 게시글
$main_sql = "SELECT m.m_datetime, m.m_title, m.wr_user, de.dept_name, du.duty_name, u.user_name, m.is_read, m.m_body, m.re_user
FROM ex_user_set u, ex_dept_set de, board_message m, ex_duty_set du
WHERE m.re_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no and m.wr_user = {$row[1]["uno"]} and mno={$post["mno"]}";

$db->query($main_sql);
$db->next_record();
$row[0] = $db->Record;
$dept_user = "[" . $row[0]["dept_name"] . "] " . $row[0]["user_name"] . " " . $row[0]["duty_name"];

// 읽음
if ($row[1]["uno"] == $row[0]["re_user"]) {
    $view_sql = "UPDATE {$_table_message} SET IS_READ= 'Y' WHERE mno = {$post["mno"]}";
    $db->query($view_sql);
    $db->commit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>쪽지</title>
    <!-- 합쳐지고 최소화된 최신 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

    <!-- 부가적인 테마 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

    <!-- 합쳐지고 최소화된 최신 자바스크립트 -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gowun+Dodum&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Gowun Dodum', sans-serif;
        }

        /* 쪽지 작성 영역 사이즈 및 가운데 정렬 */
        #message_box {
            width: 800px;
            margin: 0 auto;
        }

        /* 제목줄의 마진 및 아래 경계선 */
        #message_box h3 {
            margin-top: 30px;
            padding: 10px;
            border-bottom: solid 2px #516e7f;
            font-size: 20px;
        }

        /* 인풋영역들의 좌우 패딩 */
        #wirte_msg {
            padding: 0 20px;
        }

        /* 라벨 영역과 input영역들의 span요소의 사이즈 조절을 위해 inline-block으로  */
        #write_msg span {
            display: inline-block;
        }

        /* 라벨영역 가로사이즈 */
        #write_msg .col1 {
            width: 150px;
        }

        /* 각 줄마다 아래쪽에 경계선 그리기 */
        #write_msg li {
            border-bottom: solid 1px #dddddd;
            padding: 12px;
        }

        /* input요소의 사이즈 */
        #write_msg input {
            width: 500px;
            height: 25px;
        }

        /* textarea의 사이즈*/
        #write_msg textarea {
            width: 500px;
            height: 150px;
        }

        /* textarea의 라벨글씨가 아래쪽에 배치되기에 이를 수정하기 위해 absolute position 사용*/
        #write_msg #textarea {
            position: relative;
            height: 180px;
        }

        #write_msg #textarea .col1 {
            position: absolute;
            top: 10px;
        }

        #write_msg #textarea .col2 {
            position: absolute;
            left: 166px;
        }

        /* 서밋버튼 */
        #write_msg input[type="submit"] {
            margin: 15px 0 30px 165px;
            cursor: pointer;
        }

        /* 수신/송신 쪽지함 오른쪽 정렬 */
        #message_box .top_buttons {
            text-align: right;
            margin: 20px 0px;
        }

        #message_box .top_buttons li {
            display: inline;
            margin-right: 20px;
        }

        #message_box .top_buttons li a:link {
            text-decoration: underline;
            color: gray;
        }

        #message_box .top_buttons li a:visited {
            text-decoration: underline;
            color: gray;
        }

        input[type=button] {
            background-color: white;
            box-shadow: rgba(30, 22, 54, 0.4) 0 0px 0px 2px inset;
            color: rgba(30, 22, 54, 0.6);
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        @media(max-width:500px) {
            input[type=button] {
                background-color: white;
                box-shadow: rgba(30, 22, 54, 0.4) 0 0px 0px 2px inset;
                color: rgba(30, 22, 54, 0.6);
                padding: 5px 10px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }
        }

        /* 
        input[type=button]:hover {
            color: rgba(255, 255, 255, 0.85);
            background-color:rgba(255, 255, 255, 0.85);
            box-shadow: rgba(30, 22, 54, 0.7) 0 80px 0px 2px inset;

        } */
    </style>

</head>

<body>
    <header>
    </header>
    <section>
        <div id="main_content">
            <div id="message_box">
                <h3 id="write_title">쪽지함</h3>

                <!-- 쪽지함 이동 버튼 영역 -->
                <ul class="top_buttons">
                    <li><a href="message_list.php">수신 쪽지함</a></li>
                    <li><a href="send_list.php">발신 쪽지함</a></li>
                </ul>

                <!-- message_insert.php를 통해 DB의 message테이블에 저장 : 송신id는 get방식으로 -->
                <form action="message_action.php" method="post" name="message_form">
                    <input type="hidden" id="wr_user" name="wr_user" value="<?php echo $wr_uno ?>" />
                    <div id="write_msg">
                        <ul>
                            <li>
                                <span class="col1">받는 이 : </span>
                                <span class="col2"><?php echo $dept_user ?></span>
                            </li>
                            <!-- <li>
                                    <span class="col1">받는 이 : </span>
                                    <span class="col2">
                                        <select class="form-control" id="re_user" name="re_user" style="height:30px">
                                            <option value="">-- 직원 선택 --</option>
                                            <?php //echo $opt_user_name
                                            ?>
                                        </select>
                                    </span>
                                </li> -->
                            <li>
                                <span class="col1">제목 : </span>
                                <span class="col2"><?php echo $row[0]["m_title"] ?></span>
                            </li>
                            <li>
                                <span class="col1">발신일자 : </span>
                                <span class="col2"><?php echo $row[0]["m_datetime"] ?></span>
                            </li>
                            <li id="textarea">
                                <span class="col1">내용 : </span>
                                <span class="col2"><?php echo $row[0]["m_body"] ?></span>
                            </li>
                        </ul>
                        <!-- 서밋버튼 -->
                        <!-- <input type="submit" value="보내기"  style="margin-left:26%"> -->
                        <br />
                        <span><input type="button" class="btn btn-default btn-lg btn-block" value="목록" onclick="goMessage()" style="margin-left:400px; font-size:small; width:min-content"></span>
                        <!-- <span><input type="button" class="btn btn-default btn-lg btn-block" value="답장보내기"  onclick="goSend()" style="margin-left:260px; font-size:small; width:min-content"></span> -->
                    </div>
                </form>
            </div>

        </div>

    </section>
    <script>
        function goSubmit() {
            var frm = document.forms["message_form"];

            if (!frm.re_user.value) {
                alert("받는 이를 선택하세요");
                frm.re_user.focus();
            } else if (!frm.m_title.value) {
                alert("제목을 입력하세요");
                frm.m_title.focus();
            } else if (!frm.m_body.value) {
                alert("본문을 입력하세요.");
                frm.m_body.focus();
            } else {
                frm.submit();
            }
        }

        function goMessage() {
            location.href = 'send_list.php';
        }

        function goSend() {
            location.href = "message_send.php?mode=reply&wr_user=<?php echo $row[0]["wr_user"] ?>";
        }
    </script>
</body>

</html>
