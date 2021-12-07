<?php
$id = "separk2111";
$user_id =  $id."님 반갑습니다.";
?>
<!DOCTYPE html>
<html>
<title>리뷰게시판</title>
<style>
  td, th {border:solid 1px}
  table {border-collapse: collapse;}
  body { background-image: url('./assets/8.PNG');}
    body { background-size : cover;}  
    header { background-color:lightsalmon;}
    table { background-color: oldlace; }

  ul { background-color: #FFDAB9; width: 300px; list-style-type: none; margin: 0; padding: 0; }
  
  li a { display: block; color: #000000; padding: 8px; text-decoration: none; font-weight: bold; }

  li a:hover { background-color: #CD853F; color: white; }
  li a.current { background-color: #FF6347; color: white; }

  li a:hover:not(.current) { background-color: #CD853F; color: white;}
  li { border-bottom: solid 1px black; }
  
  li:last-child { border-bottom: none; }
</style>
<head>
  <meta charset="utf-8">  
</head>
<body>
<header>
  <center>
  <br>
<h1>신입사원 게시판</h1>

</center>
<h3 align="right"><?=$user_id?></h3>
</header>
<br>

<nav>
  <center>
  
  <ul>

    <!-- <li style="text-align:center" ><a href="main.html">메인페이지</a></li> -->



</ul>
<br>
</center>
</nav>

<table style="width:100%">

  <tr>
 
    <th>제목</th>
    <th>작성자</th>
    <th>작성일</th>
    <!-- <th>별점</th>
    <th>사진</th> -->
  </tr>
  <tbody id="rows">
    
    <!-- <tr>
 
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr> -->
      
</tbody>
</table>
<form method="GET" action="./board_write.html">
<div style="width:100%; text-align: right;">
  <br>
 <input type="submit" value="글작성"/>
</div>
</form>
</body>
</html>
