<?php
  require_once 'header.php';  //шапка сайта

  if (!$loggedin) die();

  if (isset($_GET['view'])) $view = sanitizeString($_GET['view']);
  else                      $view = $user;

  echo "<div class='main'>";

  $followers = array(); //массив подписчиков
  $following = array(); //массив подписок
	
  //поиск подписчиков
  $result = queryMysql("SELECT * FROM friends WHERE user='$view'");
  $num    = $result->num_rows;

  for ($j = 0 ; $j < $num ; ++$j)
  {
    $row           = $result->fetch_array(MYSQLI_ASSOC);
    $followers[$j] = $row['friend'];
  }

  //поиск подписок
  $result = queryMysql("SELECT * FROM friends WHERE friend='$view'");
  $num    = $result->num_rows;

  for ($j = 0 ; $j < $num ; ++$j)
  {
      $row           = $result->fetch_array(MYSQLI_ASSOC);
      $following[$j] = $row['user'];
  }
  
  /*извлекает всех участников,
  являющихся общими для обоих массивов, и возвращает новый массив, 
  который содержит только этих людей. Взаимных друзей*/
  $mutual    = array_intersect($followers, $following);	
  $followers = array_diff($followers, $mutual); //только подписчики
  $following = array_diff($following, $mutual); //только подписки
  $friends   = FALSE;

  if (sizeof($mutual)) //если не нулевой, то есть есть друзья
  {
    echo "<span class='subhead'>Ваши друзья:</span><ul>";
    foreach($mutual as $friend)
      echo "<li><a class='userslogin' href='members.php?view=$friend'>$friend</a>";
    echo "</ul>";
    $friends = TRUE;
  }

  if (sizeof($followers))//хотят дружить
  {
    echo "<span class='subhead'>Хотят дружить:</span><ul>";
    foreach($followers as $friend)
      echo "<li><a class='userslogin' href='members.php?view=$friend'>$friend</a>";
    echo "</ul>";
    $friends = TRUE;
  }

  if (sizeof($following)) //запрошена дружба
  {
    echo "<span class='subhead'>Запрошена дружба:</span><ul>";
    foreach($following as $friend)
      echo "<li><a class='userslogin' href='members.php?view=$friend'>$friend</a>";
    echo "</ul>";
    $friends = TRUE;
  }

  if (!$friends) echo "<br>У вас нет друзей, подписчиков и подписок.<br><br>";
?>

    </div><br>
  </body>
</html>
