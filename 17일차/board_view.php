<?php
session_start();
@ini_set("display_errors", "On");
//@error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
if (!defined("_INCLUDE_")) require_once $_SERVER["DOCUMENT_ROOT"] . "/lib/include.php";
include "_inc.php";
@error_reporting(E_ALL);
if ($_REQUEST["con_no"]) {
  $con_no = $_REQUEST["con_no"];
  $db = new DB;
  //$db->Debug = true;
  $main_sql = "SELECT b.con_datetime, b.con_title, u.user_name, de.dept_name, b.con_vc , du.duty_name, b.con_body, u.user_id, b.con_good
            FROM ex_user_set u, ex_dept_set de, board_contents b, ex_duty_set du
            WHERE b.wr_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no and b.con_no={$con_no}";
  $sql = "WITH A as (
    {$main_sql}
)
SELECT COUNT(*) AS CNT FROM A";
  $sql = $main_sql;
  $db->query($sql);
  $db->next_record();
  $row[0] = $db->Record;

  $date = substr($row[0]["con_datetime"], 0, 10);
  $dept_user = "[" . $row[0]["dept_name"] . "] " . $row[0]["user_name"] . " " . $row[0]["duty_name"];
  $con_title = $row[0]["con_title"];
  $con_good = $row[0]["con_good"];

  $re_sql = "SELECT u.user_name, de.dept_name, du.duty_name, u.uno, de.dept_no
FROM ex_user_set u, ex_dept_set de, board_contents b, ex_duty_set du
WHERE b.re_user = u.uno and u.dept_id = de.dept_no and u.duty_id = du.duty_no and b.con_no={$con_no}";

  $db->query($re_sql);
  $db->next_record();
  $row[1] = $db->Record;

  $re_user = "[" . $row[1]["dept_name"] . "] " . $row[1]["user_name"] . " " . $row[1]["duty_name"];

  $re_uno = $row[1]["uno"];
  $re_dept = $row[1]["dept_no"];
  $query_string = Fun::getParamUrl("con_no");
  $list_uri = "board_list.php?" . $query_string;
  $modify_uri = "board_write.php?" . $query_string . "&con_no={$con_no}&mode=modify&re_user={$re_uno}";
  $delete_uri = "board_action.php?mode=delete" . $query_string . "&con_no={$con_no}";

  $modify_btn = null;
  $delete_btn = null;
  if ($_SESSION["user_id"] == $row[0]["user_id"] || $_SESSION["user_id"] == "admin") {
    $modify_btn = "<input type=\"button\" value=\"수정\" onclick=\"goModify()\">";
    $delete_btn = "<input type=\"button\" value=\"삭제\" onclick=\"goDelete()\">";
  };

  //조회수
  $vc_no = $db->nextid("SEQ_" . "VIEW_COUNT");
  $view_date = date("Y/m/d");


  $yn_sql = "SELECT user_id FROM VIEW_COUNT WHERE user_id = '{$_SESSION["user_id"]}' and con_no={$con_no}";
  $db->query($yn_sql);
  $db->next_record();
  @$row[3] = $db->Record;


  if (!$row[3]) {
    $view_sql = "INSERT INTO VIEW_COUNT(vc_no, con_no, user_id) VALUES($vc_no, $con_no,'{$_SESSION["user_id"]}')";
    $db->query($view_sql);
    $vc_sql = "UPDATE {$_table_board} SET con_vc= con_vc + 1 WHERE con_no = {$con_no}";
    $db->query($vc_sql);
    $db->commit();
  }

  // 파일 업로드
  $file_sql = "SELECT CON_NO, FILE_PATH FROM {$_table_board} WHERE CON_NO = {$con_no}";
  $db->query($file_sql);
  while ($db->next_record()) {
    $row[2] = $db->Record;
  }


  @$file_name = basename($row[2]["FILE_PATH"]);
  $attach = null;
  if ($file_name) {

    $attach = "<div class=\"row\">
  <div class=\"col-75\">
    <h4>첨부파일  &nbsp;&nbsp;<img src=\"./image/folder.png\" style=\"width:15px;height:15px;\">&nbsp;$file_name&nbsp;&nbsp;&nbsp;<a href=\"download.php?con_no={$con_no}\">다운로드</a></h4>
    </div>
  </div>";
  }

  // 좋아요
  $db->query("SELECT COUNT(*) AS CNT FROM GOOD_COUNT WHERE USER_ID = '{$_SESSION["user_id"]}' AND CON_NO = {$con_no}");
  $db->next_record();
  $row[4] = $db->Record;
  $n = $row[4]["CNT"];

  if ($n == 0) {
    $good_no = $db->nextid("SEQ_" . "GOOD_COUNT");
    $good_sql = "INSERT INTO GOOD_COUNT(GOOD_NO,CON_NO,USER_ID,IS_GOOD) VALUES({$good_no},{$con_no},'{$_SESSION["user_id"]}','N')";
    $db->query($good_sql);
    $db->commit();
  }

  $good_sql = "SELECT IS_GOOD FROM GOOD_COUNT WHERE USER_ID='{$_SESSION["user_id"]}' AND CON_NO = {$con_no}";
  $db->query($good_sql);
  $db->next_record();
  $row[4] = $db->Record;

  if ($row[4]["is_good"] == 'Y') {
    $goodsave = "<div style=\"margin-left:42%; cursor:pointer\" onclick=\"good(this)\">
              <img src=\"./image/no_good.png\" id=\"no_good\" style=\"width:60px; height:60px\" hidden/>
              <img src=\"./image/good.png\" id=\"good\" style=\"width:60px; height:60px\" />
              </div>";
  } else {
    $goodsave = "<div style=\"margin-left:42%; cursor:pointer\" onclick=\"good(this)\">
  <img src=\"./image/no_good.png\" id=\"no_good\" style=\"width:60px; height:60px\" />
  <img src=\"./image/good.png\" id=\"good\" style=\"width:60px; height:60px\" hidden/>
  </div>";
  }

// 댓글
$user_sql = "SELECT u.user_name, de.dept_name, du.duty_name
FROM ex_user_set u, ex_dept_set de, board_contents b, ex_duty_set du
WHERE u.dept_id = de.dept_no and u.duty_id = du.duty_no and u.user_id = '{$_SESSION["user_id"]}'";
$db->query($user_sql);
$db->next_record();
$row[5] = $db->Record;
$comment_user = "[".$row[5]["dept_name"]."] ".$row[5]["user_name"]." ".$row[5]["duty_name"];
}
else{
  FUN::alert("잘못된 접근입니다.","board_login.php");
}

?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="ko"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="ko"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="ko"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="ko">
<!--<![endif]-->

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
  <!-- <link rel="shortcut icon" href="//nstatic.dcinside.com/dc/w/images/logo_icon.ico"> -->
  <link rel="apple-touch-icon" href="//nstatic.dcinside.com/dc/m/img/dcinside_icon.png">
  <link rel="apple-touch-icon-precomposed" href="//nstatic.dcinside.com/dc/m/img/dcinside_icon.png">
  <link rel="stylesheet" type="text/css" href="//nstatic.dcinside.com/dc/w/css/reset.css?v=2">
  <link rel="stylesheet" type="text/css" href="https://nstatic.dcinside.com/dc/w/css/common_210913.css?101"><!-- 211025 파일명 수정-->
  <link rel="stylesheet" type="text/css" href="https://nstatic.dcinside.com/dc/w/css/contents.css">
  <link rel="stylesheet" type="text/css" href="https://nstatic.dcinside.com/dc/w/css/minor_210913.css?1026-20"><!-- 211025 파일명 수정-->
  <link rel="stylesheet" type="text/css" href="https://nstatic.dcinside.com/dc/w/css/popup_210913.css"><!-- 211025 파일명 수정-->

  <link rel="manifest" href="/manifest.json">
  <script type="text/javascript" async="" src="https://ssl.google-analytics.com/ga.js"></script>
  <script type="text/javascript" async="" src="https://ssl.google-analytics.com/ga.js"></script>
  <script type="text/javascript" src="//gum.criteo.com/sync?c=72&amp;r=2&amp;j=TRC.getRTUS" charset="UTF-8" async="async"></script>
  <script type="text/javascript" src="//gum.criteo.com/sync?c=72&amp;r=2&amp;j=TRC.getRTUS" charset="UTF-8" async="async"></script>
  <script async="" src="https://sb.scorecardresearch.com/beacon.js"></script>
  <script charset="UTF-8" type="text/javascript" src="https://cdn.taboola.com/libtrc/impl.20211227-3-RELEASE.js"></script>
  <script async="" src="//cdn.taboola.com/libtrc/dcinside/loader.js" id="tb_loader_script"></script>
  <script type="text/javascript" src="/_js/html5shiv.min.js"></script>
  <script type="text/javascript" src="/_js/ctr_cookie.min.js"></script>
  <!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="//nstatic.dcinside.com/dc/w/css/ie7.css"/>
	<![endif]-->
  <script type="text/javascript" src="/_js/cross_domain.js"></script>
  <!--[if lt IE 9]>
	<script src="/_js/jquery/jquery-1.7.2.min.js"></script>
	<![endif]-->
  <!--[if gte IE 9]>
	<script src="/_js/jquery/jquery-3.2.1.min.js"></script>
	<![endif]-->
  <!--[if !IE]> -->
  <script src="/_js/jquery/jquery-3.2.1.min.js"></script>
  <!-- <![endif]-->
  <script type="text/javascript" src="/_js/jquery/jquery.tmpl.min.js"></script>
  <script type="text/javascript" src="/_js/jquery/jquery-ui.min.js"></script>
  <script type="text/javascript" src="/_js/jquery/jquery.matchHeight.js"></script>
  <script type="text/javascript" src="/_js/common.js?v=210913"></script>
  <script type="text/javascript" src="/_js/favorite.js?v=180106"></script>
  <script type="text/javascript" src="/_js/gallery_top.js?190513"></script>
  <script type="text/javascript" src="/_js/user_block.js?201224"></script>
  <script type="text/javascript" src="/_js/crossDomainStorage.js?1"></script>
  <script type="text/javascript" src="/_js/globalSearch.js?190513"></script>
  <script type="text/javascript" src="/_js/lately_visit.js?2103"></script>
  <script type="text/javascript" src="/_js/fcm/app.js?2"></script>
  <script type="text/javascript" src="/_js/navigation.js?v=6"></script>
  <script type="text/javascript" src="/_js/jquery/jquery.iframetracker.js"></script>

  <script src="/_js/view.js?v=211115" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/recommend_box.js?v=210113" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/search.js?v=190213" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/relate_keyword.js?v=190213" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/comment.js?v=210819" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/total_singo.js?v=190213" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/voice_reple.js?v=2101" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/ZeroClipboard.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/vr_clipboard.js?v=190304" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/ad.js" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/dccon/dccon.js?v=210819" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/zzbang.js?v=211201" type="text/javascript" charset="utf-8"></script>
  <script src="//dccon.dcinside.com/_js/dc_common2.js?1" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/zip/jszip.min.js" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/FileSaver.js?2" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/gallery_bottom.js" type="text/javascript" charset="utf-8"></script>
  <script src="/_js/kakao.min.js" type="text/javascript" charset="utf-8"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Gowun+Dodum&display=swap" rel="stylesheet">
  <!-- 합쳐지고 최소화된 최신 CSS -->
  <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css"> -->
  
  <!-- 부가적인 테마 -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">

  <!-- 합쳐지고 최소화된 최신 자바스크립트 -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

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
  <!-- Taboola -->
  <style>
    .adfit__tag-button {
      letter-spacing: 0;
      -webkit-tap-highlight-color: transparent;
      -webkit-box-direction: normal;
      -webkit-box-sizing: border-box;
      box-sizing: border-box;
      font-family: sans-serif;
      -webkit-font-feature-settings: "tnum";
      font-feature-settings: "tnum";
      font-variant: tabular-nums;
      list-style: none;
      display: inline-block;
      height: auto;
      margin: 4px 4px 0 0;
      padding: 0 7px;
      font-size: 10px;
      line-height: 18px;
      white-space: nowrap;
      border: 1px solid transparent;
      border-radius: 2px;
      opacity: 0.9;
      color: #fff;
      background-color: #fa8c16;
    }

    .adfit__tag-button--yellow {
      background-color: #fa8c16;
    }

    .adfit__tag-button--purple {
      background-color: #722ed1;
    }

    .adfit__tag-button--blue {
      background-color: #108ee9;
    }

    .adfit__tag-button--orange {
      background-color: #ff5500;
    }

    .adfit__tag-button--green {
      background-color: #8bc34a;
    }

    /*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8uL3NyYy9jb21wb25lbnRzL0RlYnVnVG9vbGJhci9UYWdCdXR0b24uY3NzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7QUFDQTtFQUNFLGlCQUFpQjtFQUNqQix3Q0FBd0M7RUFDeEMsNkJBQTZCO0VBQzdCLDhCQUFzQjtVQUF0QixzQkFBc0I7RUFDdEIsdUJBQXVCO0VBQ3ZCLHFDQUEwQjtVQUExQiw2QkFBMEI7RUFBMUIsMEJBQTBCO0VBQzFCLGdCQUFnQjtFQUNoQixxQkFBcUI7RUFDckIsWUFBWTtFQUNaLG1CQUFtQjtFQUNuQixjQUFjO0VBQ2QsZUFBZTtFQUNmLGlCQUFpQjtFQUNqQixtQkFBbUI7RUFDbkIsNkJBQTZCO0VBQzdCLGtCQUFrQjtFQUNsQixZQUFZO0VBQ1osV0FBVztFQUNYLHlCQUF5QjtBQUMzQjs7QUFFQTtFQUNFLHlCQUF5QjtBQUMzQjs7QUFFQTtFQUNFLHlCQUF5QjtBQUMzQjs7QUFFQTtFQUNFLHlCQUF5QjtBQUMzQjs7QUFFQTtFQUNFLHlCQUF5QjtBQUMzQjs7QUFFQTtFQUNFLHlCQUF5QjtBQUMzQiIsInNvdXJjZXNDb250ZW50IjpbIlxuLmFkZml0X190YWctYnV0dG9uIHtcbiAgbGV0dGVyLXNwYWNpbmc6IDA7XG4gIC13ZWJraXQtdGFwLWhpZ2hsaWdodC1jb2xvcjogdHJhbnNwYXJlbnQ7XG4gIC13ZWJraXQtYm94LWRpcmVjdGlvbjogbm9ybWFsO1xuICBib3gtc2l6aW5nOiBib3JkZXItYm94O1xuICBmb250LWZhbWlseTogc2Fucy1zZXJpZjtcbiAgZm9udC12YXJpYW50OiB0YWJ1bGFyLW51bXM7XG4gIGxpc3Qtc3R5bGU6IG5vbmU7XG4gIGRpc3BsYXk6IGlubGluZS1ibG9jaztcbiAgaGVpZ2h0OiBhdXRvO1xuICBtYXJnaW46IDRweCA0cHggMCAwO1xuICBwYWRkaW5nOiAwIDdweDtcbiAgZm9udC1zaXplOiAxMHB4O1xuICBsaW5lLWhlaWdodDogMThweDtcbiAgd2hpdGUtc3BhY2U6IG5vd3JhcDtcbiAgYm9yZGVyOiAxcHggc29saWQgdHJhbnNwYXJlbnQ7XG4gIGJvcmRlci1yYWRpdXM6IDJweDtcbiAgb3BhY2l0eTogMC45O1xuICBjb2xvcjogI2ZmZjtcbiAgYmFja2dyb3VuZC1jb2xvcjogI2ZhOGMxNjtcbn1cblxuLmFkZml0X190YWctYnV0dG9uLS15ZWxsb3cge1xuICBiYWNrZ3JvdW5kLWNvbG9yOiAjZmE4YzE2O1xufVxuXG4uYWRmaXRfX3RhZy1idXR0b24tLXB1cnBsZSB7XG4gIGJhY2tncm91bmQtY29sb3I6ICM3MjJlZDE7XG59XG5cbi5hZGZpdF9fdGFnLWJ1dHRvbi0tYmx1ZSB7XG4gIGJhY2tncm91bmQtY29sb3I6ICMxMDhlZTk7XG59XG5cbi5hZGZpdF9fdGFnLWJ1dHRvbi0tb3JhbmdlIHtcbiAgYmFja2dyb3VuZC1jb2xvcjogI2ZmNTUwMDtcbn1cblxuLmFkZml0X190YWctYnV0dG9uLS1ncmVlbiB7XG4gIGJhY2tncm91bmQtY29sb3I6ICM4YmMzNGE7XG59XG4iXSwic291cmNlUm9vdCI6IiJ9 */
  </style>
  <style>
    .adfit__debug-toolbar {
      position: absolute;
      z-index: 9999999;
    }

    .adfit__debug-toolbar--top {
      top: 0px;
      right: 0px;
    }

    .adfit__debug-toolbar--bottom {
      right: 0px;
      bottom: 4px;
    }

    /*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8uL3NyYy9jb21wb25lbnRzL0RlYnVnVG9vbGJhci9EZWJ1Z1Rvb2xiYXIuY3NzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7QUFDQTtFQUNFLGtCQUFrQjtFQUNsQixnQkFBZ0I7QUFDbEI7O0FBRUE7RUFDRSxRQUFRO0VBQ1IsVUFBVTtBQUNaOztBQUVBO0VBQ0UsVUFBVTtFQUNWLFdBQVc7QUFDYiIsInNvdXJjZXNDb250ZW50IjpbIlxuLmFkZml0X19kZWJ1Zy10b29sYmFyIHtcbiAgcG9zaXRpb246IGFic29sdXRlO1xuICB6LWluZGV4OiA5OTk5OTk5O1xufVxuXG4uYWRmaXRfX2RlYnVnLXRvb2xiYXItLXRvcCB7XG4gIHRvcDogMHB4O1xuICByaWdodDogMHB4O1xufVxuXG4uYWRmaXRfX2RlYnVnLXRvb2xiYXItLWJvdHRvbSB7XG4gIHJpZ2h0OiAwcHg7XG4gIGJvdHRvbTogNHB4O1xufVxuIl0sInNvdXJjZVJvb3QiOiIifQ== */
  </style>
  <style>
    .adfit__border {
      position: absolute;
    }

    .adfit__border--left {
      left: 0px;
      top: 0px;
      width: 1px;
      height: 100%;
    }

    .adfit__border--right {
      right: 0px;
      top: 0px;
      width: 1px;
      height: 100%;
    }

    .adfit__border--top {
      left: 0px;
      top: 0px;
      width: 100%;
      height: 1px;
    }

    .adfit__border--bottom {
      left: 0px;
      bottom: 0px;
      width: 100%;
      height: 1px;
    }

    /*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8uL3NyYy9jb21wb25lbnRzL0JvcmRlci5jc3MiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IjtBQUNBO0VBQ0Usa0JBQWtCO0FBQ3BCOztBQUVBO0VBQ0UsU0FBUztFQUNULFFBQVE7RUFDUixVQUFVO0VBQ1YsWUFBWTtBQUNkOztBQUVBO0VBQ0UsVUFBVTtFQUNWLFFBQVE7RUFDUixVQUFVO0VBQ1YsWUFBWTtBQUNkOztBQUVBO0VBQ0UsU0FBUztFQUNULFFBQVE7RUFDUixXQUFXO0VBQ1gsV0FBVztBQUNiOztBQUVBO0VBQ0UsU0FBUztFQUNULFdBQVc7RUFDWCxXQUFXO0VBQ1gsV0FBVztBQUNiIiwic291cmNlc0NvbnRlbnQiOlsiXG4uYWRmaXRfX2JvcmRlciB7XG4gIHBvc2l0aW9uOiBhYnNvbHV0ZTtcbn1cblxuLmFkZml0X19ib3JkZXItLWxlZnQge1xuICBsZWZ0OiAwcHg7XG4gIHRvcDogMHB4O1xuICB3aWR0aDogMXB4O1xuICBoZWlnaHQ6IDEwMCU7XG59XG5cbi5hZGZpdF9fYm9yZGVyLS1yaWdodCB7XG4gIHJpZ2h0OiAwcHg7XG4gIHRvcDogMHB4O1xuICB3aWR0aDogMXB4O1xuICBoZWlnaHQ6IDEwMCU7XG59XG5cbi5hZGZpdF9fYm9yZGVyLS10b3Age1xuICBsZWZ0OiAwcHg7XG4gIHRvcDogMHB4O1xuICB3aWR0aDogMTAwJTtcbiAgaGVpZ2h0OiAxcHg7XG59XG5cbi5hZGZpdF9fYm9yZGVyLS1ib3R0b20ge1xuICBsZWZ0OiAwcHg7XG4gIGJvdHRvbTogMHB4O1xuICB3aWR0aDogMTAwJTtcbiAgaGVpZ2h0OiAxcHg7XG59XG4iXSwic291cmNlUm9vdCI6IiJ9 */
  </style>
  <style>
    .adfit__swipeable {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      z-index: 999999;
    }

    /*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8uL3NyYy9jb21wb25lbnRzL1N3aXBlYWJsZS5jc3MiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IjtBQUNBO0VBQ0Usa0JBQWtCO0VBQ2xCLE9BQU87RUFDUCxNQUFNO0VBQ04sV0FBVztFQUNYLFlBQVk7RUFDWixlQUFlO0FBQ2pCIiwic291cmNlc0NvbnRlbnQiOlsiXG4uYWRmaXRfX3N3aXBlYWJsZSB7XG4gIHBvc2l0aW9uOiBhYnNvbHV0ZTtcbiAgbGVmdDogMDtcbiAgdG9wOiAwO1xuICB3aWR0aDogMTAwJTtcbiAgaGVpZ2h0OiAxMDAlO1xuICB6LWluZGV4OiA5OTk5OTk7XG59XG4iXSwic291cmNlUm9vdCI6IiJ9 */
  </style>
  <style>
    .adfit__a11y-layer {
      pointer-events: none;
    }

    .adfit__a11y-layer__hint {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-position: center;
      background-repeat: no-repeat;
      background-size: 158px 58px;
      background-image: url('data:image/png;base64,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');
    }

    /*# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8uL3NyYy9jb21wb25lbnRzL0ExMXlMYXllci5jc3MiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IjtBQUNBO0VBQ0Usb0JBQW9CO0FBQ3RCOztBQUVBO0VBQ0Usa0JBQWtCO0VBQ2xCLE1BQU07RUFDTixPQUFPO0VBQ1AsV0FBVztFQUNYLFlBQVk7RUFDWiwyQkFBMkI7RUFDM0IsNEJBQTRCO0VBQzVCLDJCQUEyQjtFQUMzQixtNklBQW02STtBQUNyNkkiLCJzb3VyY2VzQ29udGVudCI6WyJcbi5hZGZpdF9fYTExeS1sYXllciB7XG4gIHBvaW50ZXItZXZlbnRzOiBub25lO1xufVxuXG4uYWRmaXRfX2ExMXktbGF5ZXJfX2hpbnQge1xuICBwb3NpdGlvbjogYWJzb2x1dGU7XG4gIHRvcDogMDtcbiAgbGVmdDogMDtcbiAgd2lkdGg6IDEwMCU7XG4gIGhlaWdodDogMTAwJTtcbiAgYmFja2dyb3VuZC1wb3NpdGlvbjogY2VudGVyO1xuICBiYWNrZ3JvdW5kLXJlcGVhdDogbm8tcmVwZWF0O1xuICBiYWNrZ3JvdW5kLXNpemU6IDE1OHB4IDU4cHg7XG4gIGJhY2tncm91bmQtaW1hZ2U6IHVybCgnZGF0YTppbWFnZS9wbmc7YmFzZTY0LGlWQk9SdzBLR2dvQUFBQU5TVWhFVWdBQUFKNEFBQUE2Q0FZQUFBQkJDT0NEQUFBTTNVbEVRVlI0QWV5YUE1UWp5eDZIeDFyYjFzWGFHcTl0MjdadDI3WnQyN1kzZGpKS0pzTm9YdjltVDgzcjdlMU1NcXVIVzMzT3Q1dXVyblRWZEg4cC9LdGN1SWRSWGY5dmc2TE9jb015OUsxZUdXTFVLMEtTMGdxRkFuZmdFRnlDVXd3dWJGSStKQ1cxOVdJeXJtR3cvc3dLVUNod0NtN0JNUVlYZ0grQUY1UGg2cGRNSWJaTnEvL1cxNnFlUlpZeGc2ZUFhUVEvcHhVS0JlN0FJYmdFcCtBV0hJTnJLZUxCUmx3UXZQUTMxd3ZOcnZpWkZhQlE0QlRjZ21Od2plSExtQTVOSWF5azB2MUtxSHh3REs3Qk9SY00vbUFpbXNSZldUQ0ZBc2ZnR3B4RE4vc1dKK2lQZjJXaEZBb2NnMnR3em9XRVRPaEVndktyZ1dNazFPS0NEK0IzRkV5aEVOK29lQlFxSG9XS1I2RlE4U2hVUEFxRmlrZWg0djNqcUZ3aGsxUXRDTFJXcTVKWlJ0SWFOR2lnT0hMa1NEUWZNMmJNQ0xOM3I3WnQyeXBidEdpaGRGUm1nUUlGUlA3Ky9qSStLbFNvSUdYbksxeTQ4RmU0dWJrbFgvdjQ4V1BDb2tXTEl2N3g0cFV2azFHSzh2a0k4czhxZC9ZK1k0Y1gwUTNzVzFEN3MrdDM3MnExZU9XbklBdWJBdmw5UkZVclo1WnhWM3p3OGlkT25LampncGQ5OCtiTldIdGw0TnE1YytlTWp1b3lldlJvclZxdE5uT0ppNHV6dm5yMUtwN2tReHB6Mk9MajQ2MG1rOG1XeEJ4VnExYVY5dWpSUXgwUkVXRmVzV0lGRVkrSzkvcEJ6Y1JaazR1SHN5bFN5RmZzN0gwK3YvQTMzNzlXUGVGbjF3K0NCUWRrbFlNRk0wcEdSTXFDYlc1dXJwLzV4T3Zmdjc4R0wvbk5temZ4RUlITjFxMWJvMzVNUFB2czJyVkx6eFZ2OGVMRnlYSjE3dHhaaFRyMTZkTkgvZmJ0MndTSXlCS1BpbmZtVU1VWWUzbWFOc3loZkhpOWVrS2Y3dmswK0YveE1kQ3llVTFwdmJ1N2EvTDFLNmNxeDBFSW5TallpdXNaMHJzTGtKNHBvNmRnMjdxL0RkSjNBWmJuZDJzazl1dFpRTU85WitkMmVkUjNMMWVMMzdHaHRJRmJibm8vZDBIWnZ6TkkwUEsyYXBaTGRYQm5PU04rSUxpV21uam9XZzhlUEdoZ0F6bFNFKy9UcDA4SmMrYk1DZWRTdjM3OWxGYS9XN2R1YXJTZVhQUjZ2Y1dSZU5telp4ZmlYQ3FWbWxqaVVmRStQZmMzNzJSZVBtSEp2RktSS1ErOFkxNDE4cWcrQjFwM2JTeHRRRjZjTjY2ZlE0bnJJNGNVMWlrL0JWclI2azBhWFRUTTIvdkxlT2JZdnZKR2pNUFFVbDArVlRrVzMybFVMNGVDZlUvNSswQUx2amQzZW9sdlhrYkR1dGtWeUJNdUNiWjlmRjdiaE0renBpRGZ0K0s1dXJwK0xsdTJyR1RNbURGYVBvWVBINjVGSHVUbGdqSFhwVXVYakh6MDY5ZFBUZktOSERsU2EyT09uajE3cXJrMGE5Wk13Ulp2N2RxMWtibHo1eFlPR2pSSUEvR0dEQm1pWVE2ejFXcWxMVjVxWTd3M2oycVp1T0l0WDFBcUN1ZTl1dWJUNEh6VTBNSTZlMTF0c1NKK1l1UlpQTGRVWk81YzNxSVN4ZnpFWWVKZzYrSGQ1WXpzZTE0NFVTbVd0SnhjSUhDdW5ONUNDQU41STZUQk5venYrTVJESzVma3hPSGw1ZlZWR1JqME93TGxzOFZESzhhSHI2K3ZnSWpITFJjL2lvWU5HeXAwT2gwZDQzRzdXb3lkQ0hqWVhQSFExZUljNHkyY1R4cFROTXllZUdqWitJUit4SFN2N0hzTzZ1ZDRRdEt5YVU0bE5qR09HRXhFLzFhODlPblRDMnJYcmkxREM4TkhodzRkVkZtelpoVnk3MDBHLzZrZEZ5NWNNQ0l2SmdjaWtTalJIbm56NWhVaFg0WU1HUVNaTW1YNkNxVFRyamFOWXp3aUNmN0hlWkEvdjNnWXM1SHpLaFV6SmQ5MzdiSy9vb29XOWhNRHRIcVlzUERka3c4Zkg3ZlBDMmVYaW95U2g5aldMLzlUanpSNzRoRXhidCsrSGN0RnE5V2FuejE3RnM5WFJ1blNwU1ZvalFob3JmYnUzYXRucHlFY2t0Ym5paTc2OE9IREJtNjZRQ0JJWExKa0NSV1BpQWR4ZG04dUU4Mm1aclhNTW1mRmUvKzBsZ21oam81dDg2ZzlQZDJTdTBsMDEyOGYxektoOWV2WUpyY0trNCtoQXdwcG5SVVBZenlNSy92M0lwT1MxTVZETE83OCtmTkdMZ3FGd2tURWM0Uk1Kak90V3JVcWtwdmVwVXNYRlNZTWppRHhPbnhtSkRZNUtJK0t4MGVITnJuVnpvcTNjR2JKQ0xSTVNNK2M2Y3VtVm9qN2lwbUZJazNMU0xkclU1bG9kT0hPaWdleVovY2kzYU5EOFRDSllIcEcyNVFwVThLNERCMDZWSU95djFjODV2dmFmZnYyR1J6aDd1NmVJcDdSYUxSTW16WXRqRXYzN3QzVmRPWGlKK0xsNVpZY0F1R21ZM0tCRnZCbnIxeWdOV1N2WEdCMmlmQ0dQWHg4ZkJ6ZWx3bXJKQzVkdXZTSHUwS0VYazZlUEJuTngvejU4OE9wZUh4UTZGb3RoVUxGbzFEeEtCUXFIb1dLbHlkUEhsR2xTcFdrSUVlT0hNTE1tVE1Mbmp4NUVsZTVjbVVwWm16WUpvUjgxYXRYbDlhc1dWTkdLRmV1bkFUcHZYdjNWc2ZHeGxvUmZiZFhCbWFIQ0txeWw1K09IVHNXemQzaDRlbnBtVndPaWVnRHZqMXB4WW9WRTJOTEV4KzVjdVVTa254Ly9mV1hwR1BIamlvK3lHSytQVXFVS0NFdVg3NjhoQjFTUVNEMy9mdjMvMTV0YWRSSWNldldyVmcrdG0vZnJtZmZEK2YzNzkrUEE2MWF0Vklpcld2WHJpbzhPenh6Uis4SlpUTzdhQkxZenhSTGJteElVSnk4ai85cThhWk9uUm9XRlJWbEFZaWVRejRzKzRTR2hzb1JTRVVjQ2Ztd1Y4eG9ORnF4ZGdpd1E0Tzl3eU8xUHhRYkxwRUh3cEkwQkdxdlhic1dRODd6NTg4dmd1ak1ZVVZleE5Mc0xSdmR1WE1uRmt0WHFET1hTWk1tNmRpckFVK2ZQbzNuZ2oxdTJCMlMycllseFBlWXV0anUzcjBiaStVMXBDTmVKNUZJRWtrK2JGMUNYUWNQSHF6cDFhdVhtazN6NXMyVDVjS1BLQ1FrUkk1NkxWaXdJQndnZElKbmdYQU92ay9FWStxcmdUUjg4VU9VTFJRS1U4ckdPMGhpSGRoTXdLN1RmN1Y0aU54UG1EQkJ4Nlp4NDhZS2UrTGhPdmNlUkx4Um8wWnBzYlBXM281ZDVFRUxZVSs4YmR1MlJURXYxWVNXY2ZMa3lUcUx4V0lyVXFTSTJKNTRKMDZjaVA0cCsrRTRvQ1ZFWGR1MWE2ZEVMMkF3R0N6NHUxTVREOHRrcUN1YmdnVUxpa2dMSkpmTFRWd2VQWG9VUjhRclZLaVFDQzBZZHFUZzNGbngva1c5SFlQRWRrUmhIQy90TE8wcjBSNHJRU3V0dExPdkxhMjB0Qk5zRkZGUXhVSlFVQVN4c1JFVmJlMnNSRERoSmI0WFNjeTY2dVltV1lPQjdDOHdNZ3h6ZFRjSWNSY096NzF2ZHUvc3pQK2M4ODNjTXlwYVZDOHpqdHMyNEtrZk00bXhUVXhNL1BKZndKT0NSTSt5TktITnpNeE1wUXc4a3dFK2YzdElMdUxZL1M4RHIxYXJ2VnhlWHRaVEl4RkN1L0h4OFo5elVWRWtLd052YVdtcDJvaUlMK0c5UG9ydTVlRGxYMHFhWWpuanMrNnBkTXJuWEEvZ2VhMnNyRHkwQ3A3b21iWnJDL0RZMk5qWVQ0M0JLSlFKRFF3TTNJUlVlM2g0K0p1d0g0UG5JYmJLRFkrYWdBQzBabEt0QisvYW5KMmRGVEY0VXJmcVgybEg2dlM5TVlnR3RndzhPbFRLU3EycnErdjd0QjVPcWt1dHI2L3Z4MXhmOS9iMmFnMGQ5bnNNSW8yWkE0OG05YnNaY0R4ckRlOURlbWFlUG9qbXNvbnhOQmF5UXdDUHJxVk5QeEk4aFE1emMzUDNueEk4UDlhRXo4N09WbFJZM043ZS91V2FqaFArUlZHOGdzZGJsV0tiY0NDcDBoV1IzZ09Qd0FmdDBkRlJVYS9YLys3djc3OEo0SmxRM3lGTlZDcVYxN296RTByclRVMU4zWldCSjdwTlQwLy9tdHJJeU1pM0dEeVJjM0p5OGk1bnVUNHJlWS9oMnQvZnI1MmZuLytSZ3JlMnR2WXZhTUdNRlowVlgyTitpMGdlOU9mT3pzNFRHYUhLSmRWNEh3a2VuY201UGlWNEtpaWtyQ0R1ZFZnMHlLWGFZTjNkM1YrSWRvTklESXVZRHJVWTROd3BLdy9ScjYrdm4vMi9pbzlxdGZwaTBOTlVlM3g4WEpoZzFScnVyUStEZzRNM09mQThVRmZ6bGpOU0liU2owMFF2QmdJd2gvY3NYZ0hINlJtc2RKZVZvdjR2THk5WFUvRG9QMVhFd1l5bFNhZWI0K3NnOGozR2JIdDcrMmwrZnY0K09KOVZNN2xEMzhYZ2FhLzB2YmUzOXdjcllEc0l6WURuZTlvaTFkSlNUanh0Ykd3OFNxMSttRlJWQnQ3Q3drTFZwSmhFMndJaXBQZXJxNnZaZ3pGQTRPbFNhZGdHYWJ5ZXRVL0JBenp0ZFhGeDhhYytuWjZlRmg5WkRMbTR1RmdGMFh2dFREcEhFWUd0c3ZYZjFrb3UxYW9za2M2MERWck5hYkdUazVPaXA2Zm5TMXJGc3I2Ky9zQnhnOUhNdVlnWHY4Z1J6cFFEei94WXBjdEVIRnBXYWh1TlIxL1lZekl4MG1MWmRvcHFYSkRSUFBIbkRiejI2ZDViOEdpVzdodlNQeWw0VENRQUdLMEhnQng0b3AybzhaWVpmSG92dm1hQ1JQZTByUWlia3lEU3RyMURVY2UxSEhoS3A0d0pTV0RzWkEyclluck8vZUx2TkpabEM1RUFuczhQRFExOXBiVkZPVUdnTE5VNkV3SmNVSkk3bzZPajM2eW0yd2E4MU1yQTQ5MnFiZWt5YVVPRUNrZndlSHhIUjBkTDkwbkFLN1VVUEU2eXRiWDFwZ0czeFhxNHBpd0Z6MWFKc1FLd3ZVcU9RMXNSOW5Sb0N0N3U3dTdUOFBEdzEyQ2NLQWF2M0ZMd3lxMXR3Uk5wd0Viak9NTEhxMko5dDdtNStRZytvZDYvRmlVaEtyUmlWbzhIQndkTm5VMjl1cnFxV3dENSsvODBvRmpVeE5la1I5clVlTmp6azZhbDFGUS9hc09CY3RiWjJmbGRNL2UyQS9CZU84NWdvZE4yNFAzVDNoMWdPZ3lFWVJRTnlBN2Zjck8wSi8wdkRTMmdVaDA0T0lNcTRHbzZnUTl1aHdmQ1EzZ2dQSVFId2tONENNK2sxQkltcFl6b0xXRkViOGxzS0daRDF3d2xZeWg1anQ5UHcyTWF2dU04Ly9iNThCaG5WZmFUMlBQNHpvVURGNGthcXFXYXFxMXgxTnJZaXU2eVAydjg3MHZ3TFRWVlcxZDA2WGpUODdjL2Y5MDhybGN0OEtuYXFhRmFxcW14dlhvQVREZDlpVlVzay93QUFBQUFTVVZPUks1Q1lJST0nKTtcbn1cbiJdLCJzb3VyY2VSb290IjoiIn0= */
  </style>
  <style>
    .adfit__html-banner {
      position: relative;
      overflow: hidden;
      max-height: inherit;
    }

    .adfit__html-banner__inner {
      overflow: hidden;
    }

    .adfit__html-banner__inner {
      display: block;
      border: 0;
      margin: 0 auto;
    }

    .adfit__html-banner--responsive>.adfit__html-banner__inner {
      height: 0;
    }

    .adfit__html-banner--responsive>.adfit__html-banner__inner iframe {
      width: 100%;
      height: 100%;
      max-height: inherit;
      left: 0;
      top: 0;
      position: absolute;
    }

    .gallview_head clear ub-content {
      font-family: -apple-system, BlinkMacSystemFont, "Apple SD Gothic Neo", "Malgun Gothic", "맑은 고딕", arial, 굴림, Gulim, sans-serif;
      font-size: 13px;
      color: #333;
      padding: 0;
      border: 0;
      vertical-align: baseline;
      background: transparent;
      font-style: normal;
      margin: 16px 0 29px;
      padding-bottom: 11px;
      border-bottom: 1px solid #eee;
    }

    body {
      font-family: 'Gowun Dodum', sans-serif;
    }

    * {
      box-sizing: border-box;
    }

    input[type=text],
    select,
    textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 4px;
      resize: vertical;
    }

    label {
      padding: 12px 12px 12px 0;
      display: inline-block;
    }

    input[type=submit] {
      background-color: #4CAF50;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-left: 40%;
    }

    input[type=submit]:hover {
      background-color: #45a049;

    }

    input[type=button] {
      background-color: #6495ED;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 4px;
      cursor: pointer;

    }

    input[type=button]:hover {
      background-color: #4682B4;

    }

    .container {
      border-radius: 10px;
      background-color: snow;
      padding: 20px;
      max-width: 900px;
      margin-left: 27%;
      margin-top: 1%;
      margin-bottom: 3%;
    }

    .col-25 {
      float: left;
      width: 1%;
      margin-top: 6px;
    }

    .col-75 {
      float: left;
      width: 90%;
      margin-top: 6px;
    }

    .col-50 {
      float: left;
      width: 50%;
      margin-top: 6px;
    }


    /* Clear floats after the columns */
    .row:after {
      content: "";
      display: table;
      clear: both;
    }

    /* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
    @media screen and (max-width: 600px) {

      .col-25,
      .col-75,
      input[type=submit] {
        width: 100%;
        margin-top: 100px;
      }
    }

    .CommentWriter {
      --themeColorTC1: #000;
      --themeColorTC2: #323232;
      --themeColorTC3: #676767;
      --themeColorTC4: #878787;
      --themeColorTC5: #949494;
      --themeColorTC6: #03c75a;
      --themeColorTC7: #009f47;
      --themeColorTC8: #fb6400;
      --themeColorTC9: #f53535;
      --themeColorTC10: #0076ff;
      --themeColorTC11: #0076ff;
      --themeColorTC12: #009f47;
      --themeColorTC13: #f53535;
      --themeColorTC14: #fff;
      --themeColorTC15: #676767;
      --themeColorTC16: #a6a6a6;
      --themeColorTC17: #ff7921;
      --themeColorTC18: #222;
      --themeColorTC19: #a6a6a6;
      --themeColorTC20: #2761c2;
      --themeColorLN1: #eee;
      --themeColorLN2: #e6e6e6;
      --themeColorLN3: rgba(0, 0, 0, 0.06);
      --themeColorLN4: #e6e6e6;
      --themeColorLN5: #eee;
      --themeColorLN6: #eee;
      --themeColorBG1: #fff;
      --themeColorBG2: #fff;
      --themeColorBG3: #fff;
      --themeColorBG4: #f5fdf5;
      --themeColorBG5: #f5f6f8;
      --themeColorBG6: #fff;
      --themeColorBG7: #fbfefb;
      --themeColorBT1: rgba(3, 199, 90, 0.12);
      --themeColorBT2: #009f47;
      --themeColorBT3: rgba(0, 118, 255, 0.1);
      --themeColorBT4: #f5f6f8;
      --themeColorBT5: #ebecef;
      --themeColorBT6: #e5e7ea;
      --themeColorBT7: #f5f6f8;
      --themeColorBT8: rgba(245, 53, 53, 0.06);
      --themeColorBT9: #a6a6a6;
      --themeColorBT10: rgba(0, 159, 71, 0.4);
      --themeColorBT11: #323232;
      --themeColorHC1: #ffff9f;
      --skinSpecialMenuWidth: 1px;
      --skinSpecialMenuBorder: #74bbbe;
      --skinSpecialMenuBg: #74bbbe;
      --skinSpecialMenuColor: #fff;
      --skinSpecialMenuDot: #fff;
      --skinTextColor: #000000;
      --skinText323232: #323232;
      --skinText676767: #676767;
      --skinText979797: #979797;
      --skinTextb7b7b7: #b7b7b7;
      --GnbLink: #333;
      --GnbLinkBar: #e5e5e5;
      --GnbLinkArrow: #777;
      --LayoutHeaderTitleBg: url(https://ssl.pstatic.net/static/cafe/cafe_pc/bg_default_title_white_180724.png);
      --LayoutHeaderTitleName: #212121;
      --LayoutHeaderTitleUrl: #666;
      --skinListBorder: #f2f2f2;
      --skinThumbBorder: rgba(0, 0, 0, 0.06);
      --skinBar: rgba(0, 0, 0, 0.15);
      --skinArticleLink: -webkit-link;
      --skinSourceBadge: #323232;
      --skinTagLinkColor: #323232;
      --skinToggleSwitchBg: #a7a7a7;
      --skinTempSaveBorder: rgba(0, 0, 0, 0.2);
      --skinCommentWriterBorder: rgba(0, 0, 0, 0.1);
      --skinCommentWriterBg: #ffffff;
      --skinCommentWriterText: #b7b7b7;
      --skinCommentWriterFocus: #eeeeee;
      --skinBox: #f9f9fa;
      --skinSvgIconPostBtnArrowUp: #323232;
      --skinSvgIconPostTop: #323232;
      --skinLayoutBorder: #ebecef;
      --skinBaseButtonDefaultBg: #eff0f2;
      --skinBaseButtonDefaultColor: #000000;
      --skinBaseButtonPointBg: rgba(3, 199, 90, 0.12);
      --skinBaseButtonPointColor: #009f47;
      --skinBgPostRefresh: url(https://ca-fe.pstatic.net/web-pc/static/img/ico-post-refresh.svg?f332c2e8d26157445fa480d35d5b0ef4);
      --skinSvgIconNpay: url(https://ca-fe.pstatic.net/web-pc/static/img/pc-ico-npay.svg?d0a9d54243d73135189ed084c99de534);
      --skinSvgIconSolidWriting: #009F47;
      --skinColor: #ffffff;
      --skinCommentMineBg: #f9f9fa;
      --skinListSelectedBg: #f9f9fa;
      --skinBaseButtonDefaultBorder: transparent;
      --skinCommentRefreshButtonBorder: transparent;
      --skinNoticeBadgeRequiredBg: #ffe3e4;
      --skinNoticeBadgeMenuBg: #ffffff;
      --skinNoticeBadgeMenuBorder: #ffe3e4;
      --skinNoticeBadgeColor: #f53535;
      -webkit-text-size-adjust: none;
      font-weight: 400;
      color: #333;
      font-size: 12px;
      margin: 12px 0 29px;
      padding: 16px 10px 10px 18px;
      border: 2px solid var(--skinCommentWriterBorder);
      border-radius: 6px;
      box-sizing: border-box;
      background: var(--skinCommentWriterBg);
    }
    textarea {
    padding: 10px;
    max-width: 100%;
    line-height: 1.5;
    border-radius: 5px;
    border: 1px solid #ccc;
    box-shadow: 1px 1px 1px #999;
}
  </style>
</head>

<body>
  <div style="margin-top:5%; margin-left:26%; margin-right:25%">
    <form>
      <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION["user_id"] ?>" />
      <input type="hidden" id="con_no" name="con_no" value="<?php echo $con_no ?>" />
      <input type="hidden" id="goodchk" name="goodchk" value="false" />
      <div class="gallview_head clear ub-content">
        <!-- 모바일에서 작성 icon_write_mbl -->
        <h3 class="title ub-word">

          <span class="title_subject" style="font-size:x-large;"><?php echo $con_title ?></span>

          <!-- <em class="blind">앱에서 작성</em>
                                <em class="sp_img icon_write_app"></em> -->
          </span>
          </span>
        </h3>
        <br />
        <div class="gall_writer ub-writer" data-loc="view">
          <div class="fl">
            <span class="nickname" title="<?php echo $dept_user ?>" style="font-size:medium;"><em><?php echo $dept_user ?></em></span>
            <span class="gall_date" style="font-size:medium;"><?php echo $row[0]["con_datetime"] ?></span>
          </div>
          <div class="fr">
            <span class="gall_count" style="font-size:small;">조회 <?php echo $row[0]["con_vc"] ?></span>
            <span class="gall_reply_num" style="font-size:small;">좋아요 <?php echo $row[0]["con_good"] ?></span>
            <span class="gall_comment" style="font-size:small;"><a href="#focus_cmt">댓글 48</a></span>
          </div>
        </div>
      </div>
  </div>
  <div class="container">
    <div style="font-size:large">
      <h4><b>추천사원 : <?php echo $re_user ?></b>
        <h4>
    </div>
    <br />
    <textarea class="form-control" rows="10" id="comment" name="text" style="font-size:medium" disabled readonly><?php echo $row[0]['con_body'] ?></textarea>
    <br /><br />
    <div style="font-size:medium"><?php echo $attach ?></div>
    <br /><br /><br /><br /> <br /> 
    <div class="row">
      <!-- <div style="margin-left:42%; cursor:pointer" onclick="good(this)">
          <img src="./image/no_good.png" id="no_good" style="width:60px; height:60px"/>
          <img src="./image/good.png" id="good" style="width:60px; height:60px" hidden/>
        </div> -->
      <?php echo $goodsave; ?>
      <div id="ajax_message" style="margin-left:44%; padding:5px"><b><?php echo $con_good ?></b></div>
    </div>
    <br /> <br /> 
    <div class="CommentWriter">
      <div class="comment_inbox">
        <strong><?php echo $comment_user?></strong>

        <em class="comment_inbox_name"></em>
        <br />
        <textarea placeholder="댓글을 남겨보세요" class="comment_inbox_text" style="overflow: hidden; overflow-wrap: break-word; height: 70px;"></textarea>
      </div>
      <br />
      <div class="register_box" style="text-align:right">
        <a href="#" role="button" class="button btn_register" >등록</a>
      </div>
    </div>
    <br /><br /><br />
    <div class="row" style="text-align:center; padding-right:80px;">
    <span class="btn2" style="width:min-content">
      <?php echo $modify_btn ?>
    </span>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    <span class="btn1" style="width:min-content">
      <input type="button" value="목록" onclick="goList()">
    </span>
    <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    <span class="btn3" style="width:min-content">
      <?php echo $delete_btn ?>
      </form>
  </div>
  <br /><br />

  </div>

  <script type="text/javascript">
    function goList() {
      location.href = "<?php echo $list_uri ?>";
    }

    function goModify() { //== EDIT == UPDATE == MODIFY
      location.href = "<?php echo $modify_uri; ?>";
    }

    function goDelete() {
      var a = confirm("정말 삭제하시겠습니까?");
      if (a == true) {
        location.href = "<?php echo $delete_uri; ?>";
      }
    }

    function good(obj) {

      var xhttp = new XMLHttpRequest();

      // Define a callback function
      xhttp.onload = function() {
        // Here you can use the Data
        document.getElementById("ajax_message").innerHTML = "<b>" + this.responseText + "</b>";
      }

      var no_good = document.getElementById("no_good").hidden;
      var good = document.getElementById("good").hidden;
      if (no_good == false) {
        document.getElementById("no_good").hidden = true;
        document.getElementById("good").hidden = false;
        document.getElementById("goodchk").value = "false";
      } else {
        document.getElementById("no_good").hidden = false;
        document.getElementById("good").hidden = true;
        document.getElementById("goodchk").value = "true";
      }

      xhttp.open("GET", "board_action.php?mode=good&user_id=" + document.getElementById("user_id").value + "&con_no=" + document.getElementById("con_no").value + "&goodchk=" + document.getElementById("goodchk").value);
      xhttp.send();
    }
  </script>
</body>

</html>
