<?php 
  $dbhost  = 'localhost';       // сервер
  $dbname  = 'soc_set';   		// БД
  $dbuser  = 'igor';  		    // пользователь
  $dbpass  = 'qwerty';   	 	// пароль
  $appname = "Вfacebook.те"; 	// имя приложения

  $connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);    //соединение
  if ($connection->connect_error) die($connection->connect_error); //ошибка
  
  function createTable($name, $query)	//функция создания таблицы
  {
    queryMysql("CREATE TABLE IF NOT EXISTS $name($query)"); //выполнение запроса
    echo "Таблица '$name' создана или уже имелась в БД!<br>";
  }

  function queryMysql($query)				//функция шаблон-запроса
  {
    global $connection;						//соединяемся в БД
    $result = $connection->query($query);	//выполение запроса-параметра
    if (!$result) die($connection->error);	//вывод ошибки
    return $result;							//или результата
  }
  //устанавливаем кодировку у  БД для поддержки русского при отправке данных в БД
  queryMysql("SET NAMES 'cp1251'"); 

  function destroySession()	//функция завершение сессии
  {
    $_SESSION=array();	//обнуляем массив _session
	// есть сессия или есть куки с названием сессии
    if (session_id() != "" || isset($_COOKIE[session_name()])) 
      setcookie(session_name(), '', time()-2592000, '/'); //удаление куки
    session_destroy();									  //уничтожаем сессию
  }

  function sanitizeString($var) //функция обезвреживания
  {
    global $connection;
    $var = strip_tags($var);	//полностью удаляет HTML-код
    //$var = htmlentities($var, ENT_QUOTES, 'UTF-8');	
	//заменяет HTML-код и теги, но конфликтует с русским языком, т.к. все в CP-1251
    $var = stripslashes($var);	//от слешей
    return $connection->real_escape_string($var); //обезвреживание специальных символов
  }  

  function showProfile($user) //функция показать профиль
  {
    if (file_exists("$user.jpg"))	//если есть картинка
	{
      echo "<div class='photo' style='float:left;'><img src='$user.jpg'>";
	  
	  //кнопка удалить фото только в разделе редактирования
	  if (isset($_GET['view']))
			$view = sanitizeString($_GET['view']);
	  if ($view != $user)
			echo "<div class='butUnderPhoto'><input type='button' value='Удалить фото' 
				onClick=\"if(confirm('Вы действительно хотите удалить аватар?'))
					    location.href='profile.php?p=del';\"></div><br><br>";
	  echo "</div>";
	}
	
    $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");

    if ($result->num_rows)
    {
	  //извлечение ассоциативного массива с данными таблицы profiles
      $row = $result->fetch_array(MYSQLI_ASSOC);
	  //вывод значения столбца text для пользователя $user
      echo stripslashes($row['text']) . "<br style='clear:left;'><br>";
    }
  }
?>
