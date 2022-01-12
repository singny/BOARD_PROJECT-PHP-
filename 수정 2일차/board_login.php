<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include_once "_inc.php";

$db = new DB;

?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="ko"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="ko"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="ko">
<!--<![endif]--><html lang="ko">
<html>

<head>
    <meta property="og:url" content="http://separk2111.htenc.com/board_login.php">
  <meta property="og:title" content="(주) 하이테크엔지니어링">
  <meta property="og:type" content="website">
  <meta property="og:image" content="http://separk2111.htenc.com/image/thumbnail.png">
  <meta property="og:description" content="로그인하세요">

    <!-- 합쳐지고 최소화된 최신 CSS -->
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css"> -->

    <!-- 부가적인 테마 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

    <!-- 합쳐지고 최소화된 최신 자바스크립트 -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script> -->
    <title>로그인</title>
    <meta name="viewport" content="width=device-width, height=device-height, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&family=Nanum+Pen+Script&family=Poor+Story&display=swap" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="57x57" href="/image/icon/apple-icon-57x57.png">
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</head>
<style>
    body {
        font-family: 'Jua', sans-serif;
    }

    header {
        display: flex;
        justify-content: center;
    }

    form {
        padding: 10px;
    }

    .input-box {
        position: relative;
        margin: 65px 530px;
    }

    @media(min-width:1900px) {
        .input-box {
            position: relative;
            margin: 65px 700px;
        }
    }

    @media(max-width:500px) {
        .input-box {
            position: relative;
            margin: 40px 60px
        }
    }

    .input-box>input {
        background: transparent;
        border: none;
        border-bottom: solid 1px #ccc;
        padding: 20px 0px 5px 0px;
        font-size: 14pt;
        width: 100%;
    }

    input::placeholder {
        color: transparent;
    }



    input:placeholder-shown+label {
        color: #aaa;
        font-size: 14pt;
        top: 15px;

    }

    input:focus+label,
    label {
        color: #4169E1;
        font-size: 10pt;
        pointer-events: none;
        position: absolute;
        left: 0px;
        top: 0px;
        transition: all 0.2s ease;
        -webkit-transition: all 0.2s ease;
        -moz-transition: all 0.2s ease;
        -o-transition: all 0.2s ease;
    }

    input:focus,
    input:not(:placeholder-shown) {
        border-bottom: solid 1px #4169E1;
        outline: none;
    }

    input[type=button] {
        background-color: #4169E1;
        border: none;
        color: white;
        border-radius: 5px;
        width: 35%;
        height: 35px;
        font-size: 14pt;
        margin-top: 100px;
        margin: 0 490px;
        cursor: pointer;
        font-family: 'Jua', sans-serif;
    }

    @media(min-width:1900px) {
        input[type=button] {
            background-color: #4169E1;
            border: none;
            color: white;
            border-radius: 5px;
            width: 30%;
            height: 35px;
            font-size: 14pt;
            margin-top: 100px;
            margin: 0 650px;
            cursor: pointer;
            font-family: 'Jua', sans-serif;
        }
    }

    @media(max-width:500px) {
        input[type=button] {
            background-color: #4169E1;
            border: none;
            color: white;
            border-radius: 5px;
            width: 70%;
            height: 35px;
            font-size: 14pt;
            margin-top: 100px;
            margin: 0 47.5px;
            cursor: pointer;
            font-family: 'Jua', sans-serif;
        }
    }


    #forgot {
        text-align: right;
        font-size: 12pt;
        color: rgb(164, 164, 164);
        margin: 10px 0px;
    }

    h1 {
        text-align: center;
        margin-top: 175px;
        color: #4169E1;
    }

    a {
        text-align: center;
        font-size: 12pt;
        color: rgb(164, 164, 164);
        margin-left: 965px;
    }

    @media(min-width:1900px) {
        a {
            text-align: center;
            font-size: 12pt;
            color: rgb(164, 164, 164);
            margin-left: 1155px;
        }
    }

    @media(max-width:500px) {
        a {
            text-align: center;
            font-size: 12pt;
            color: rgb(164, 164, 164);
            margin-left: 230px;
        }
    }

    @media(max-width:500px) {
        .join {
            font-size: x-small;
        }
    }
    .findid{
        float: left; 
        width: 10%; 
        margin-left:38%;
        color:rgb(164, 164, 164); 
        cursor:pointer;
    }
    @media(max-width:500px) {
        .findid{
        float: left; 
        width: 30%; 
        margin-left:10%;
        color:rgb(164, 164, 164); 
        cursor:pointer;
    }
    }
    .findpwd{
        float: left; 
        width: 10%;
        color:rgb(164, 164, 164);
        cursor:pointer;
    }
    @media(max-width:500px) {
        .findpwd{
        float: left; 
        width: 30%;
        color:rgb(164, 164, 164);
        cursor:pointer;
    }
    }
</style>

<body>
    <form id="login_frm" name="login_frm" action="board_login_process.php" method="POST">
        <h1>HI-TECH ENG</h1>
        <div class="input-box">
            <input id="user_id" type="text" name="user_id" placeholder="아이디">
            <label for="username">아이디</label>
        </div>

        <div class="input-box">
            <input id="user_pwd" type="password" name="user_pwd" placeholder="비밀번호">
            <label for="password">비밀번호</label>
        </div>

        <input type="button" value="로그인" onclick="null_chk()">
    </form>
    <div class="join">
        <br /> <br /><br />
        <div class="findid" onclick="goFindid()">아이디 찾기&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</div>
        <div class ="findpwd" onclick="goFindpwd()" >비밀번호 찾기&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</div>
        <div class ="findpwd" onclick="goJoin()">&nbsp;&nbsp;회원가입</div>
    </div>

</body>
<script>
    function null_chk() {
        var id = document.getElementById("user_id").value;
        var password = document.getElementById("user_pwd").value;

        if (id == '') {
            alert("아이디를 입력하세요");
            document.getElementById("user_id").focus();
        } else if (password == '') {
            alert("비밀번호를 입력하세요");
            document.getElementById("user_pwd").focus();
        } else {
            document.getElementById("login_frm").submit();
        }
    }

    function goJoin() {
        location.href = "board_join.php";
    }
    function goFindid(){
        var url = "find_id.php";
        var name = "popup test";
        var option = "width = 500, height = 650, top = 100, left = 200, location = no, status = no, titlebar = no"
        window.open(url, name, option);
    }
    function goFindpwd(){
        var url = "find_pwd.php";
        var name = "popup test";
        var option = "width = 500, height = 650, top = 100, left = 200, location = no, status = no, titlebar = no"
        window.open(url, name, option);
    }
</script>

</html>
