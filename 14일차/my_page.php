<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
@error_reporting(E_ALL);

include_once "_inc.php";

$db = new DB;

$user_sql = "SELECT user_id, user_name, dept_id, duty_id, loc_id
            FROM ex_user_set
            WHERE user_id='{$_SESSION["user_id"]}'";
$db->query($user_sql);
while($db->next_record()){
    $row[3] = $db->Record;
    $user_id = $row[3]["user_id"];
    $user_name = $row[3]["user_name"];
}

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
?>
<!DOCTYPE html>
<html lang="ko">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Page</title>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <style>
    body {
      min-height: 100vh;

      /* background: -webkit-gradient(linear, left bottom, right top, from(#92b5db), to(#1d466c)); */
      /* background: -webkit-linear-gradient(bottom left, #eff6fd 0%, #296eaf 100%);
      background: -moz-linear-gradient(bottom left, #eff6fd 0%, #296eaf 100%);
      background: -o-linear-gradient(bottom left, #eff6fd 0%, #296eaf 100%);
      background: linear-gradient(to top right, #eff6fd 0%, #296eaf 100%); */
      background-image: url('../image/join.png');
      background-repeat : no-repeat;
      background-size:cover;
      
    }

    .input-form {
      max-width: 800px;

      margin-top: 80px;
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
            width : 740px;
            font-size: 20px;
        }

    input[type=button]:hover {
            background-color: #4682B4;

    }
    div[id=join_out]{
        text-align: right;
        font-size: 12pt;
        color: rgb(164, 164, 164);
        margin-left: 670px;
        cursor:pointer;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="input-form-backgroud row">
      <div class="input-form col-md-12 mx-auto">
        <h4 class="mb-3">My page</h4>
        <form class="validation-form" novalidate method="post" action="board_user_modify.php?mode=modify">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="user_id">아이디</label>
              <input type="text" class="form-control" id="user_id" name="user_id" value="<?php echo $user_id?>" disalbed readonly onfocus="this.blur()" required>
              <div class="invalid-feedback">
                아이디를 입력해주세요.
              </div>
            </div>
            <!-- <div class="col-md-6 mb-3">
              <div><label for="user_id">&nbsp;</label></div>
              <input type="button" id="id_check" value="아이디 중복확인" onclick="id_chk()"/>
            </div> -->
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
            <label for="user_name">이름</label>
            <input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo $user_name?>" disabled required>
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
            <label for="aggrement"><b>정보수정</b></label>
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

    function complete(){
            location.href = "board_list.php";
           }

    function modifyComplete(){
        var pwd = document.getElementById("user_pwd").value;
            var pwd_chk = document.getElementById("user_pwd_chk").value;
        
            if(!(pwd === pwd_chk)){
              alert("입력한 두 비밀번호가 일치하지 않습니다. 다시 입력해주세요.")
              document.getElementById("user_pwd").value = '';
              document.getElementById("user_pwd_chk").value = '';
              document.getElementById("user_pwd").focus();
              event.preventDefault();
              event.stopPropagation();
            }
    }

    function id_chk() {
      var user_id = document.getElementById("user_id").value;
      
        url = "board_id_chk.php?user_id=" + user_id;
        window.open(url, "chkid", "width=300,height=150,top=250,left=1000,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,resizable=no, status=no");

    }

    function modifyMode(){
        var check = document.getElementById("modify").checked;

        if(check){
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
            
        }
        else{
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
            
        }
    }

    function really(){
      
      if(confirm("정말 탈퇴하시겠습니까?")){
        location.href = "board_user_modify.php?mode=delete";
      }
    }
  </script>
</body>

</html>
