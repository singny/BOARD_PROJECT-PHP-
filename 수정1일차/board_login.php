<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include_once "_inc.php";

$db = new DB;

?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="imagetoolbar" content="no">
  <meta name="content-language" content="kr">
  <meta name="google-site-verification" content="8_SyZg2Wg3LNnCmFXzETp7ld4yjZB8ny17m8QsYsLwk">
  <meta name="author" content="ㅇㅇ">


  <meta property="og:type" content="website">

  <meta property="og:image" content="https://write.dcinside.com/viewimage.php?id=w_entertainer&amp;no=24b0d769e1d32ca73deb82fa11d028316c04369a2e349766dc7643c7579d2183f97f3347c41aea5f5fade152498450abaf9e9a754a60c4d5d5e11007671dd92d3cc51163b890de8bc016b72013db563171f2d7b99ca584">
  <meta property="og:url" content="https://gall.dcinside.com/board/view/?id=w_entertainer&amp;no=16082807&amp;exception_mode=recommend&amp;page=1">
  <meta property="og:updated_time" content="2021-12-25 22:01:38">
  <meta property="og:locale" content="ko_KR">
  <meta name="referrer" content="unsafe-url">
  <title>우수사원 추천 게시판</title>
  <link rel="stylesheet" type="text/css" href="//nstatic.dcinside.com/dc/w/css/reset.css?v=2">
  <link rel="stylesheet" type="text/css" href="https://nstatic.dcinside.com/dc/w/css/common_210913.css?101"><!-- 211025 파일명 수정-->
  <link rel="stylesheet" type="text/css" href="https://nstatic.dcinside.com/dc/w/css/contents.css">
  <link rel="stylesheet" type="text/css" href="https://nstatic.dcinside.com/dc/w/css/minor_210913.css?1026-20"><!-- 211025 파일명 수정-->
  <link rel="stylesheet" type="text/css" href="https://nstatic.dcinside.com/dc/w/css/popup_210913.css"><!-- 211025 파일명 수정-->
  
  <script type="text/javascript" async="" src="https://ssl.google-analytics.com/ga.js"></script>
  <script type="text/javascript" async="" src="https://ssl.google-analytics.com/ga.js"></script>
  <script async="" src="https://sb.scorecardresearch.com/beacon.js"></script>
  <script async="" src="//cdn.taboola.com/libtrc/dcinside/loader.js" id="tb_loader_script"></script>
  
  
  <!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="//nstatic.dcinside.com/dc/w/css/ie7.css"/>
	<![endif]-->
  <!--[if lt IE 9]>
	<script src="/_js/jquery/jquery-1.7.2.min.js"></script>
	<![endif]-->
  <!--[if gte IE 9]>
	<script src="/_js/jquery/jquery-3.2.1.min.js"></script>
	<![endif]-->
  <!--[if !IE]> -->
  
  <!-- <![endif]-->
  
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
  <!-- 합쳐지고 최소화된 최신 CSS -->
  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css"> -->

  <!-- 부가적인 테마 -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

  <!-- 합쳐지고 최소화된 최신 자바스크립트 -->
  <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script> -->

  <!-- Taboola -->
  <script type="text/javascript">
    window._taboola = window._taboola || [];
    _taboola.push({
      category: 'auto'
    });
    ! function(e, f, u, i) {
      if (!document.getElementById(i)) {
        e.async = 1; 
        e.src = u;
        e.id = i;
        f.parentNode.insertBefore(e, f);
      }
    }(document.createElement('script'),
      document.getElementsByTagName('script')[0],
      '//cdn.taboola.com/libtrc/dcinside/loader.js',
      'tb_loader_script');
    if (window.performance && typeof window.performance.mark == 'function') {
      window.performance.mark('tbl_ic');
    }
  </script>
    <title>로그인</title>
    <meta name="viewport" content="width=device-width, height=device-height, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jua&family=Nanum+Pen+Script&family=Poor+Story&display=swap" rel="stylesheet">
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
    <div id="join">
        <br />   <br /><br />
        <div style="float: left; width: 10%; margin-left:38%;color:rgb(164, 164, 164); cursor:pointer">아이디찾기&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</div>
        <div style="float: left; width: 10%;color:rgb(164, 164, 164);cursor:pointer">비밀번호찾기&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|</div>
        <div style="float: left; width: 10%;color:rgb(164, 164, 164);cursor:pointer" onclick="goJoin()">&nbsp;&nbsp;회원가입</div>
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
    function goJoin(){
        location.href = "board_join.php";
    }
</script>

</html>
