<?php
  require_once 'header.php';  //шапка сайта

  if (!$loggedin) die();

  echo "<div class='main'>";

  if (isset($_GET['view']))	//просмотр профиля пользователя
  {
    $view = sanitizeString($_GET['view']);
    
    if ($view == $user) $name = "вашего аккаунта";
    else                $name = "$view";
    
    echo "<h3>Профиль $name</h3>";
    showProfile($view);		//отображение профиля
	if ($view != $user)		//кнопки в профиле других юзеров
	{
		//кнопка отправить сообщение
		echo "<a class='button' href='messages.php?view=$view'>" .
			"Отправить сообщение</a>";

		//кнопка принять/запросить/удалить дружбу
		$result = queryMysql("SELECT * FROM friends WHERE (user='$view' AND friend='$user')");
		$result1 = queryMysql("SELECT * FROM friends WHERE (friend='$view' AND user='$user')");
		if (!$result->num_rows && !$result1->num_rows)
		{
			echo "<a class='button' href='members.php?add=$view'>" .
				"Запросить дружбу</a><br><br>";	
		}
		elseif ($result->num_rows && !$result1->num_rows)
			echo "<a class='button' href='members.php?remove=$view'>" .
				"Отменить запрос на дружбу</a><br><br>";
		elseif (!$result->num_rows && $result1->num_rows)
			echo "<a class='button' href='members.php?add=$view'>" .
				"Принять дружбу</a><br><br>";
		else 
			echo "<a class='button' href='members.php?remove=$view'>" .
				"Удалить из друзей</a><br><br>";
	}
	else 	//кнопки в своем профиле
	{
		echo "<a class='button' href='messages.php'>" .
				"Входящие сообщения</a>";
		echo "<a class='button' href='profile.php'>" .
				"Редактировать профиль</a><br><br>";		
	}
    die("</div></body></html>");
  }

  if (isset($_GET['add']))	//добавление в друзья
  {
    $add = sanitizeString($_GET['add']);

    $result = queryMysql("SELECT * FROM friends WHERE user='$add' AND friend='$user'");
    if (!$result->num_rows)
      queryMysql("INSERT INTO friends VALUES ('$add', '$user')");
  }
  elseif (isset($_GET['remove']))	//удаление
  {
    $remove = sanitizeString($_GET['remove']);
    queryMysql("DELETE FROM friends WHERE user='$remove' AND friend='$user'");
  }	

  //извлекаем из БД всех пользователей
  $result = queryMysql("SELECT user FROM members ORDER BY user");
  $num    = $result->num_rows;

  echo "<h3>Другие пользователи:</h3><ul>";

  for ($j = 0 ; $j < $num ; ++$j)
  {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if ($row['user'] == $user) continue; //юзер не видит сам себя

    echo "<li><a class='userslogin' href='members.php?view=" .
      $row['user'] . "'>" . $row['user'] . "</a>";
    $follow = "<span class='queryFriend'>&#9787;</span>";	//запрос в друзья

	//проверка на подписку
    $result1 = queryMysql("SELECT * FROM friends WHERE
      user='" . $row['user'] . "' AND friend='$user'");
    $t1      = $result1->num_rows;
    $result1 = queryMysql("SELECT * FROM friends WHERE
      user='$user' AND friend='" . $row['user'] . "'");
    $t2      = $result1->num_rows;

	//если подписаны, то друзья
    if (($t1 + $t2) > 1) echo " &harr; ";	//друг
    elseif ($t1)         echo " &larr; ";	//запрошена дружба
    elseif ($t2)       { echo " &rarr; ";	//хочет дружить
      $follow = "<span class='available'>&#x2714;"; } //принять дружбу
    
    if (!$t1) echo " <a href='members.php?add="   .$row['user'] . "'>$follow</a>";
    else      echo " <a href='members.php?remove=".$row['user'] . "'><span class='taken'>&#x2718;</span></a>"; //удалить запрос в друзья
	//написать
	echo " <a href='messages.php?view=" . $row['user'] . "'><span class='letter'>&#x2709;</span></a>";
  }
?>

    </ul></div>
  </body>
</html>
