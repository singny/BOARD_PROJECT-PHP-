<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Summernote</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
</head>

<body>
  <form method="post" action="editorProc.php">
    <table boarder=1 width=100%>
      <!-- <tr>
        <td>제목 </td>
        <td><input type="text" name="subject" style="width:100%"></td>
      </tr> -->
      <tr>
        <td colspan="2"><textarea id="summernote" name="summernote"></textarea></td>

      </tr>
    </table>
    <input type="submit" value="전송" />
  </form>
  <script>
    $(document).ready(function() {
      $('#summernote').summernote({
        tabsize : 2,
        height : 500
      });
    });
  </script>
</body>

</html>
