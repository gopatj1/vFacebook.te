<?php
  require_once 'header.php';  //шапка сайта

  if (isset($_SESSION['user'])) //если есть сессия
  {
    destroySession();  //удаляем сессию
	echo "<script>location.replace('index.php');</script>";
  }
  else echo "<div class='main'><br>" .
            "Вы не можете выполнить выход из аккаунта, так как не был выполнен вход!";
?>

    <br><br></div>
  </body>
</html>
