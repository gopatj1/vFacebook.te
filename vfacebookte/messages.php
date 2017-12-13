<?php
  require_once 'header.php';  //шапка сайта

  if (!$loggedin) die();

  if (isset($_GET['view'])) $view = sanitizeString($_GET['view']);
  else                      $view = $user;

  if (isset($_POST['text']))
  {
    $text = sanitizeString($_POST['text']);

    if ($text != "")	//есть сообщение
    {
      $time = time();	//врем€ отправлени€
      queryMysql("INSERT INTO messages VALUES(NULL, '$user',
        '$view', '$pm', $time, '$text')");
    }
  }

  if ($view != "")
  {
    if ($view == $user) $name1 =  "¬ход€щие сообщени€";
    else
      $name1 = "ѕриватный чат с пользователем <a class='userslogin' href='members.php?view=$view'>$view</a>";

    echo "<div class='main'><h3>$name1</h3>";
    
	if ($view != $user)
	{
	  $check = TRUE;	//провер€ем чат или меню сообщений
		echo <<<_END
      <form method='post' action='messages.php?view=$view'>
      ¬ведите текст сообщени€:<br>
      <textarea name='text' cols='44' rows='3' style='resize: none;'></textarea><br>
      <input type='submit' value=' ќтправить сообщение '></form><br>
_END;
	}
	else $check = FALSE;

    if (isset($_GET['erase']))
    {
      $erase = sanitizeString($_GET['erase']);
      queryMysql("DELETE FROM messages WHERE id=$erase AND (recip='$user' OR auth='$user')");
    }
    
    $query  = "SELECT * FROM messages WHERE recip='$view' ORDER BY time DESC";
    $result = queryMysql($query);
    $num    = $result->num_rows;    
	$authors = array();	//массив, всех авторов, писавших пользователю
    if (!$check)	//информаци€ о сообщении в меню сообщени€
	{
	  $chatmess = "";	//все сообщение во вход€щих сообщени€х
	  for ($j = 0 ; $j < $num ; ++$j)
      {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        if ($row['auth'] != $user && $row['recip'] == $user)
        {
			//провер€ем отправл€л ли ранее, чтоб исключить дубликаты
		    for ($i = 0 ; $i < count($authors) ; $i++)
				if ($row['auth'] == $authors[$i])
				{
					$alreadysent = TRUE; //уже отправил
					break;
				}
				else 
					$alreadysent = FALSE; //не отправл€л
			
			if (!$alreadysent)	//если не отправл€л, то выводим информацию
			{
				$chatmess .= "<br>" . date('d.m.Y H:i', $row['time']); //дата
				
				if ($row['auth'] != $user)	//кнопка написать, перейти в чат
					$chatmess .= "  <a href='messages.php?view=" 		  .
						$row['auth'] . "' style='text-decoration:none;'>" .
						"<span class='letter'>&#x2709;</span></a>";

				if ($row['recip'] == $user)	//кнопка удаление сообщени€
					$chatmess .= "  <a class='taken' href='messages.php?view=$view" .
               "&erase=" . $row['id'] . "' >&#x2718;</a>";
			   
			    if ($row['pm'] == "") //если сообщение не прочитано
					$chatmess .= "  <span class='queryFriend'>Ќе прочитано</span>";

				$chatmess .= "<br>ѕользователь " . $row['auth'] .
							 " прислал сообщение!<br>";
			}
			$authors[] = $row['auth'];	//добавл€ем j-ого автора в массив
        }	  
      }
	  if($chatmess == "") //нет сообщений
		  echo "<br><span class='info'>Ќет сообщений</span><br><br>";
	  else
		  echo $chatmess; //все сообщени€ во вход€щих сообщени€х
	}

	//дл€ реализации двустороннего чата использвем запрос и дополнительные переменные с единичкой в конце
	$query1  = "SELECT * FROM messages WHERE (auth='$view' and recip='$user') or (auth='$user' and recip='$view') ORDER BY time DESC";
    $result1 = queryMysql($query1);
	$num1    = $result1->num_rows;
	if ($check)	//чат с пользователем
	{
	  $chatmess1 = "";	//все сообщение в чате
	  $idmess = ""; 	//айди сообщени€
	  for ($j = 0 ; $j < $num1 ; ++$j)
      {
        $row1 = $result1->fetch_array(MYSQLI_ASSOC);

        if ($row1['auth'] == $user)	//сообщение от пользовател€
        {
          $chatmess1 .= "<br>" . date('d.m.Y H:i - ', $row1['time']) .
						"ѕользователь " . $row1['auth'] . " "        .
						"отправил: <br><span class='whisper'>"       .
						$row1['message'] . "</span> ";

        if ($row1['recip'] == $user || $row1['auth'] == $user) //удаление сообщени€
          $chatmess1 .= "  <a class='taken' href='messages.php?view=$view" .
               "&erase=" . $row1['id'] . "'>&#x2718;</a><br>";
        }
       
        if ($row1['recip'] == $user) //сообщение пользователю
        {
          $chatmess1 .= "<br>" . date('d.m.Y H:i - ', $row1['time']) .
						"ѕользователь " . $row1['auth'] . " "        .
						"отправил: <br><span class='whisper'>"       .
						$row1['message'] . "</span> ";

        if ($row1['recip'] == $user)	//удаление сообщени€
          $chatmess1 .= "  <a class='taken' href='messages.php?view=$view" .
             "&erase=" . $row1['id'] . "'>&#x2718;</a><br>";
        }

		//измен€ем соощение на прочитанное
		queryMysql("UPDATE messages SET pm='1' WHERE recip='$user' and auth='$view'");
	  }
	  if($chatmess1 == "") //нет сообщений в чате
		  echo "<br><span class='info'>Ќет сообщений</span><br><br>";
	  else
		  echo $chatmess1; //все сообщени€ в чате
	}
  }

  echo "<br><a class='button' href='messages.php?view=$view'>ќбновить</a>";
  if (($view == $user))
	echo "<a class='button' href='members.php'>ќптравить сообщение</a>";
?>

    </div><br>
  </body>
</html>
