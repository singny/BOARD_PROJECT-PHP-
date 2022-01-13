<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include_once "_inc.php";

$db = new DB;

$user_sql = "SELECT user_id, user_name, dept_id, duty_id, loc_id, uno, phone
            FROM ex_user_set
            WHERE user_id='{$_SESSION["user_id"]}'";
$db->query($user_sql);
while ($db->next_record()) {
  $row[3] = $db->Record;
  $user_id = $row[3]["user_id"];
  $user_name = $row[3]["user_name"];
  $phone = $row[3]["phone"];
}

$uno = $row[3]["uno"];

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

    if ($db->f("dept_no") == $row[3]["dept_id"]) {
      $_sel = " selected";
    }

    $txt = str_replace(" ", "&nbsp", $db->f("dept_name_lvl"));
    $opt_dept_id .= "<option value=\"{$db->f("dept_no")}\" {$_sel} title=\"{$db->f("dept_name_path")}\">{$txt}</option>\n";
  }
}

$duty_sql = "SELECT * FROM EX_DUTY_SET";
$db->query($duty_sql);

if ($db->nf() > 0) {
  while ($db->next_record()) {
    $row[1] = $db->Record;
    $_sel2 = "";

    if ($db->f("duty_no") == $row[3]["duty_id"]) {
      $_sel2 = " selected";
    }

    $txt2 = str_replace(" ", "&nbsp", $db->f("duty_name"));
    $opt_duty_id .= "<option value=\"{$db->f("duty_no")}\" {$_sel2} title=\"{$db->f("duty_name")}\">{$txt2}</option>\n";
  }
}

$loc_sql = "SELECT * FROM EX_LOC_CODE";
$db->query($loc_sql);

if ($db->nf() > 0) {
  while ($db->next_record()) {
    $row[2] = $db->Record;
    $_sel3 = "";

    if ($db->f("loc_no") == $row[3]["loc_id"]) {
      $_sel3 = " selected";
    }

    $txt3 = str_replace(" ", "&nbsp", $db->f("LOC_NAME"));
    $opt_loc_id .= "<option value=\"{$db->f("LOC_NO")}\" {$_sel3} title=\"{$db->f("LOC_NAME")}\">{$txt3}</option>\n";
  }
}

// 유저 사진
$file_sql = "SELECT UNO, FILE_PATH FROM {$_table_user} WHERE UNO = {$uno}";
$db->query($file_sql);
while ($db->next_record()) {
  $row[4] = $db->Record;
}

@$file_name = basename($row[4]["FILE_PATH"]);
$user_img = null;

if ($file_name) {
  $user_img = "<img src=\"./upload_file/user/{$uno}/$file_name\" id=\"image\" class=\"img-thumbnail\" width=\"125px\" height=\"150px\"  alt=\"\">";
  $yn_img = "yes";
} else {
  $user_img = "<img src=\"./image/noimg.png\" id=\"image\" class=\"img-thumbnail\" width=\"125px\" height=\"150px\" />";
  $yn_img = "no";
}

?>
<!DOCTYPE html>
<html lang="ko">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Page</title>

  <!-- Bootstrap CSS -->
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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

      margin-top: 45px;
      margin-bottom: 45px;
      padding: 32px;

      background: #fff;
      -webkit-border-radius: 10px;
      -moz-border-radius: 10px;
      border-radius: 10px;
      -webkit-box-shadow: 0 8px 20px 0 rgba(0, 0, 0, 0.15);
      -moz-box-shadow: 0 8px 20px 0 rgba(0, 0, 0, 0.15);
      box-shadow: 0 8px 20px 0 rgba(0, 0, 0, 0.15)
    }

    input[type=button] {
      background-color: #6495ED;
      color: white;
      padding: 8px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      width: 740px;
      font-size: 20px;
    }

    @media(max-width:500px) {
      input[type=button] {
        background-color: #6495ED;
        color: white;
        padding: 8px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 300px;
        font-size: 20px;
      }
    }

    input[type=button]:hover {
      background-color: #4682B4;

    }

    div[id=join_out] {
      text-align: right;
      font-size: 12pt;
      color: rgb(164, 164, 164);
      margin-left: 670px;
      cursor: pointer;
    }

    @media(max-width:500px) {
      div[id=join_out] {
        text-align: right;
        font-size: 12pt;
        color: rgb(164, 164, 164);
        margin-left: 0px;
        cursor: pointer;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="input-form-backgroud row">
      <div class="input-form col-md-12 mx-auto">
        <h4 class="mb-3">My page</h4>
        <br />
        <form class="validation-form" novalidate method="post" action="board_user_modify.php?mode=modify" enctype="multipart/form-data">
          <input type="hidden" id="uno" name="uno" value="<?php echo $row[3]["uno"] ?>">
          <input type="hidden" id="yn_img" name="yn_img" value="<?php echo $yn_img?>" />
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="user_id" style="vertical-align:bottom">아이디</label>
              <input type="text" class="form-control" id="user_id" name="user_id" value="<?php echo $user_id ?>" disalbed readonly onfocus="this.blur()" required>
              <div class="invalid-feedback">
                아이디를 입력해주세요.
              </div>
              <br />
              <label for="user_name">이름</label>
              <input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo $user_name ?>" disabled required>
              <div class="invalid-feedback">
                이름을 입력해주세요.
              </div>
            </div>
            <div class="col-md-6 mb-3" style="text-align:right; padding-right:7%">
              <label for="img">사진</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <!-- <img src="./image/noimg.png" id="image" width="125px" height="150px" /> -->
              <?php echo $user_img ?>
              <div id="change_img" hidden>
                <input type="file" name="uploadfile" id="img" style="display:none;" accept="image/gif,image/jpeg,image/png" onchange="previewFile()" />
                <label for="img" style="background-color:cadetblue;border-radius: 5px; margin-top:2px; padding:2px; cursor:pointer">이미지 변경하기</label>
                <img src="image/remove.png" width="20px" height="20px" onclick="deleteImg()" style="cursor:pointer" />
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="user_pwd" id="la_user_pwd" hidden>비밀번호</label>
              <input type="password" class="form-control" id="user_pwd" name="user_pwd" disabled value="" required hidden>
              <div class="invalid-feedback">
                비밀번호를 입력해주세요.
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="user_pwd_chk" id="la_user_pwd_chk" hidden>비밀번호 확인</label>
              <input type="password" class="form-control" id="user_pwd_chk" value="" disabled required hidden>
              <div class="invalid-feedback">
                비밀번호 확인을 입력해주세요.
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="phone">핸드폰</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone ?>" placeholder="ex) 01012345678" disabled required>
            <div class="invalid-feedback">
              핸드폰번호를 입력해주세요.
            </div>
          </div>
          <div class="mb-3">
            <label for="company">회사명</label>
            <input type="text" class="form-control" id="company" name="company" value="(주)하이테크엔지니어링" readonly onfocus="this.blur()" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="dept_name">부서명</label>
              <select class="custom-select d-block w-100" id="dept_id" name="dept_id" disabled required>
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
                <select class="custom-select d-block w-100" id="duty_id" name="duty_id" disabled required>
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
              <label for="loc_name">회사 위치</label>
              <select class="custom-select d-block w-100" id="loc_id" name="loc_id" disabled required>
                <option value="" selected disalbed hidden></option>
                <?php echo $opt_loc_id ?>
              </select>
              <div class="invalid-feedback">
                회사 위치를 선택해주세요.
              </div>
            </div>
          </div>
          <div id="join_out">
            <div onclick="really()">회원탈퇴</div>
          </div>
          <hr class="mb-4">
          <div class="custom-control custom-checkbox" style="text-align:center">
            <!-- <input type="checkbox" class="custom-control-input" id="aggrement" name="aggrement" onclick="modifyMode()"> -->
            <input type="checkbox" id="modify" name="modify" onclick="modifyMode()">
            <label for="aggrement"><b>정보수정 mode</b></label>
          </div>
          <div class="mb-4"></div>
          <input type="button" id="ok" name="ok" onclick="complete()" value="확인" />
          <button class="btn btn-primary btn-lg btn-block" id="modify_btn" name="modify_btn" type="submit" onclick="modifyComplete()" hidden>수정 완료</button>
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
      location.href = "board_list.php";
    }

    function modifyComplete() {
      var pwd = document.getElementById("user_pwd").value;
      var pwd_chk = document.getElementById("user_pwd_chk").value;
      var phone = document.getElementById("phone").value;

      if (pwd.length == 0) {
        alert("비밀번호를 입력하세요");
        document.getElementById("user_pwd").focus();
      } else if (pwd.length < 4) {
        alert("4자리이상의 비밀번호를 입력하세요");
        document.getElementById("user_pwd").focus();
      } else if (!(pwd === pwd_chk)) {
        alert("입력한 두 비밀번호가 일치하지 않습니다. 다시 입력해주세요.")
        document.getElementById("user_pwd").value = '';
        document.getElementById("user_pwd_chk").value = '';
        document.getElementById("user_pwd").focus();
        event.preventDefault();
        event.stopPropagation();
      } else if (isNaN(phone)) {
        alert("핸드폰번호를 숫자로만 표기해주세요.");
        document.getElementById("phone").focus();
        event.preventDefault();
        event.stopPropagation();
      }
    }

    function id_chk() {
      var user_id = document.getElementById("user_id").value;

      url = "board_id_chk.php?user_id=" + user_id;
      window.open(url, "chkid", "width=300,height=150,top=250,left=1000,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,resizable=no, status=no");

    }

    function modifyMode() {
      var check = document.getElementById("modify").checked;

      if (check) {
        document.getElementById("user_pwd").disabled = false;
        document.getElementById("user_pwd_chk").disabled = false;
        document.getElementById("user_name").disabled = false;
        document.getElementById("dept_id").disabled = false;
        document.getElementById("duty_id").disabled = false;
        document.getElementById("loc_id").disabled = false;
        document.getElementById("modify_btn").hidden = false;
        document.getElementById("ok").hidden = true;
        document.getElementById("la_user_pwd").hidden = false;
        document.getElementById("user_pwd").hidden = false;
        document.getElementById("la_user_pwd_chk").hidden = false;
        document.getElementById("user_pwd_chk").hidden = false;
        document.getElementById("change_img").hidden = false;
        document.getElementById("phone").disabled = false;

      } else {
        document.getElementById("user_pwd").disabled = true;
        document.getElementById("user_pwd_chk").disabled = true;
        document.getElementById("user_name").disabled = true;
        document.getElementById("dept_id").disabled = true;
        document.getElementById("duty_id").disabled = true;
        document.getElementById("loc_id").disabled = true;
        document.getElementById("ok").hidden = false;
        document.getElementById("modify_btn").hidden = true;
        document.getElementById("la_user_pwd").hidden = true;
        document.getElementById("user_pwd").hidden = true;
        document.getElementById("la_user_pwd_chk").hidden = true;
        document.getElementById("user_pwd_chk").hidden = true;
        document.getElementById("change_img").hidden = true;
        document.getElementById("phone").disabled = true;

      }
    }

    function really() {

      if (confirm("정말 탈퇴하시겠습니까?")) {
        location.href = "board_user_modify.php?mode=delete";
      }
    }

    function previewFile() {
      var preview = document.querySelector('img');
      var file = document.querySelector('input[type=file]').files[0];
      var reader = new FileReader();

      reader.addEventListener("load", function() {
        preview.src = reader.result;
      }, false);
      document.getElementById("yn_img").value = "yes";

      if (file) {
        reader.readAsDataURL(file);
        var reader_image = document.getElementById("image").style.visibility = "visible";
        document.getElementById("yn_img").value = "yes";
      }
      var attach = document.getElementById("attach").hidden = true;

    }

    function deleteImg(){
      document.getElementById("image").src="image/noimg.png";
      document.getElementById("yn_img").value = "no";
    }
  </script>
</body>

</html>
