<?php
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include_once "_inc.php";

$db = new DB;

$dept_id = null;
$sql = "SELECT * FROM {$_table_dept} WHERE LEVEL_NO=2 OR LEVEL_NO=3 OR LEVEL_NO=4";
$opt_dept_id = null;
$opt_duty_id = null;
$opt_loc_id = null;
$db->query($sql);

if ($db->nf() > 0) {
  while ($db->next_record()) {
    $row[0] = $db->Record;
    $_sel = "";


    $txt = str_replace(" ", "&nbsp", $db->f("dept_name_lvl"));
    $opt_dept_id .= "<option value=\"{$db->f("dept_no")}\" title=\"{$db->f("dept_name_path")}\">{$txt}</option>\n";
  }
}

$duty_sql = "SELECT * FROM EX_DUTY_SET";
$db->query($duty_sql);

if ($db->nf() > 0) {
  while ($db->next_record()) {
    $row[1] = $db->Record;
    $_sel2 = "";

    $txt2 = str_replace(" ", "&nbsp", $db->f("duty_name"));
    $opt_duty_id .= "<option value=\"{$db->f("duty_no")}\" title=\"{$db->f("duty_name")}\">{$txt2}</option>\n";
  }
}

$loc_sql = "SELECT * FROM EX_LOC_CODE";
$db->query($loc_sql);

if ($db->nf() > 0) {
  while ($db->next_record()) {
    $row[2] = $db->Record;
    $_sel3 = "";

    $txt3 = str_replace(" ", "&nbsp", $db->f("LOC_NAME"));
    $opt_loc_id .= "<option value=\"{$db->f("LOC_NO")}\" title=\"{$db->f("LOC_NAME")}\">{$txt3}</option>\n";
  }
}
?>
<!DOCTYPE html>
<html lang="ko">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>회원가입</title>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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
  <style>
    body {
      min-height: 100vh;

      /* background: -webkit-gradient(linear, left bottom, right top, from(#92b5db), to(#1d466c)); */
      /* background: -webkit-linear-gradient(bottom left, #eff6fd 0%, #296eaf 100%);
      background: -moz-linear-gradient(bottom left, #eff6fd 0%, #296eaf 100%);
      background: -o-linear-gradient(bottom left, #eff6fd 0%, #296eaf 100%);
      background: linear-gradient(to top right, #eff6fd 0%, #296eaf 100%); */
      background-image: url('../image/join.png');
      background-repeat: no-repeat;
      background-size: cover;
      font-family: 'Jua', sans-serif;

    }

    .input-form {
      max-width: 800px;

      margin-top: 40px;
      margin-bottom: 40px;
      padding: 32px;

      background: #fff;
      -webkit-border-radius: 10px;
      -moz-border-radius: 10px;
      border-radius: 10px;
      -webkit-box-shadow: 0 8px 20px 0 rgba(0, 0, 0, 0.15);
      -moz-box-shadow: 0 8px 20px 0 rgba(0, 0, 0, 0.15);
      box-shadow: 0 8px 20px 0 rgba(0, 0, 0, 0.15)
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="input-form-backgroud row">
      <div class="input-form col-md-12 mx-auto">
        <h4 class="mb-3">회원가입</h4>
        <form class="validation-form" novalidate method="post" action="board_join_insert.php?mode=join">
          <input type="hidden" id="user_id_check" name="user_id_check" value="false" />
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="user_id">아이디</label>
              <input type="text" class="form-control" id="user_id" name="user_id" placeholder="4자리이상의 아이디를 입력하세요" required>
              <div class="invalid-feedback">
                아이디를 입력해주세요.
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div><label for="user_id">&nbsp;</label></div>
              <!-- <input type="button" id="id_check" value="아이디 중복확인" onclick="id_chk()"/> -->
              <input type="button" id="btnUserIdCheck" name="btnUserIdCheck" value="아이디 중복확인" onclick="GoUserIdCheck(this);" />
            </div>
          </div>
          <div id="ajax_message"></div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="user_pwd">비밀번호</label>
              <input type="password" class="form-control" id="user_pwd" name="user_pwd"placeholder="4자리이상의 비밀번호를 입력하세요." value="" required>
              <div class="invalid-feedback">
                비밀번호를 입력해주세요.
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="user_pwd_chk">비밀번호 확인</label>
              <input type="password" class="form-control" id="user_pwd_chk"  value="" required>
              <div class="invalid-feedback">
                비밀번호 확인을 입력해주세요.
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="user_name">이름</label>
            <input type="text" class="form-control" id="user_name" name="user_name" required>
            <div class="invalid-feedback">
              이름을 입력해주세요.
            </div>
          </div>
          <div class="mb-3">
            <label for="company">회사명</label>
            <input type="text" class="form-control" id="company" name="company" value="(주)하이테크엔지니어링" readonly onfocus="this.blur()" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="dept_name">부서명</label>
              <select class="custom-select d-block w-100" id="dept_id" name="dept_id" required>
                <option value="" selected disalbed hidden></option>
                <?php echo $opt_dept_id; ?>
              </select>
              <div class="invalid-feedback">
                부서명을 선택해주세요.
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="mb-3">
                <label for="duty_name">직급</label>
                <select class="custom-select d-block w-100" id="duty_id" name="duty_id" required>
                  <option value="" selected disalbed hidden></option>
                  <?php echo $opt_duty_id ?>
                </select>
                <div class="invalid-feedback">
                  직급을 선택해주세요.
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-8 mb-3">
              <label for="loc_id">회사 위치</label>
              <select class="custom-select d-block w-100" id="loc_id" name="loc_id" required>
                <option value="" selected disalbed hidden></option>
                <?php echo $opt_loc_id ?>
              </select>
              <div class="invalid-feedback">
                회사 위치를 선택해주세요.
              </div>
            </div>
          </div>
          <hr class="mb-4">
          <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="aggrement" required>
            <label class="custom-control-label" for="aggrement">개인정보 수집 및 이용에 동의합니다.</label>
          </div>
          <div class="mb-4"></div>
          <button class="btn btn-primary btn-lg btn-block" type="submit" onclick="complete()">가입 완료</button>
          <br />
          <button type="button" onclick="goLogin()" class="btn btn-info" style="float:right">로그인 페이지</button>
        </form>
      </div>
    </div>
  </div>
  <script>
    window.addEventListener('load', () => {
      const forms = document.getElementsByClassName('validation-form');

      Array.prototype.filter.call(forms, (form) => {
        form.addEventListener('submit', function(event) {
          if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
    }, false);

    function complete() {
      var pwd = document.getElementById("user_pwd").value;
      var pwd_chk = document.getElementById("user_pwd_chk").value;
      var user_id_chk = document.getElementById("user_id_check").value;
      var user_id = document.getElementById("user_id").value;

      if(pwd.length < 4){
           alert("4자리이상의 비밀번호를 입력하세요");
           document.getElementById("user_pwd").focus();
      } else if (!(pwd === pwd_chk)) {
        alert("입력한 두 비밀번호가 일치하지 않습니다. 다시 입력해주세요.");
        document.getElementById("user_pwd_chk").value = '';
        document.getElementById("user_pwd").focus();
        event.preventDefault();
        event.stopPropagation();
      } else if (user_id.length < 4) {
        //   alert("4자리이상의 아이디를 입력하세요");
        //  document.getElementById("user_id").focus();
      } else if (user_id_chk == "false") {
        alert("아이디 중복확인을 해주세요.")
        event.preventDefault();
        event.stopPropagation();
      }
    }

    function GoUserIdCheck(obj) {
      document.getElementById("user_id_check").value = "false";
      document.getElementById("ajax_message").innerHTML = "";
      //console.log(obj);
      //alert(obj);
      // Create an XMLHttpRequest object
      var xhttp = new XMLHttpRequest();

      // Define a callback function
      xhttp.onload = function() {
        // Here you can use the Data
        if (this.responseText != "") {
          alert(this.responseText);
          if (this.responseText == "중복된 아이디입니다.") {
            // document.getElementById("user_id").value = "";
            document.getElementById("user_id").focus();
          } else if (this.responseText == "아이디를 입력하세요.") {
            document.getElementById("user_id").focus();
          } else if(this.responseText == "4자리 이상의 아이디를 입력하세요."){
            document.getElementById("user_id").focus();
          } else {
            document.getElementById("user_id_check").value = "true";
          }
        } else {
          document.getElementById("user_id_check").value = "false";
        }
      }

      // Send a request
      xhttp.open("GET", "board_join_insert.php?mode=userid_check&user_id=" + document.getElementById("user_id").value);
      xhttp.send();
    }

    function goLogin(){
      location.href = "board_login.php"
    }
  </script>
</body>

</html>
