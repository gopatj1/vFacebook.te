<?php
  require_once 'header.php';  //шапка сайта

  if (!$loggedin) die();

  echo "<div class='main'><h3>Редактировать ваш профиль</h3>";

  $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");
    
  if (isset($_POST['text'])) //если форма прислала текст
  {
    $text = sanitizeString($_POST['text']); //обеззараживаем
    $text = preg_replace('/\s\s+/', ' ', $text);

    if ($result->num_rows) //вставляем в БД
         queryMysql("UPDATE profiles SET text='$text' WHERE user='$user'");
    else queryMysql("INSERT INTO profiles VALUES('$user', '$text')");
  }
  else
  {
    if ($result->num_rows) //выводим что имеется в БД 
    {
      $row  = $result->fetch_array(MYSQLI_ASSOC);
      $text = sanitizeString($row['text']);
    }
    else $text = "";
  }

  $text = stripslashes(preg_replace('/\s\s+/', ' ', $text));

  if (isset($_FILES['image']['name'])) //если форма прислала картинку
  {
    $saveto = "$user.jpg";
	//поддреживает только 4 формата
	if ($_FILES['image']['type'] == "image/jpeg" || $_FILES['image']['type'] == "image/gif" 
	|| $_FILES['image']['type'] == "image/pjpeg" || $_FILES['image']['type'] == "image/png")
	{
		//перемещаем загруженный файл в переменную
		move_uploaded_file($_FILES['image']['tmp_name'], $saveto);
		$typeok = TRUE;
		
		switch($_FILES['image']['type']) //создаем новое изображение
		{
		case "image/gif":   $src = imagecreatefromgif($saveto); break;
		case "image/jpeg": 
		case "image/pjpeg": $src = imagecreatefromjpeg($saveto); break;
		case "image/png":   $src = imagecreatefrompng($saveto); break;
		default:            $typeok = FALSE; break;
		}

		if ($typeok) //вычисляем размеры для аватара
		{
			list($w, $h) = getimagesize($saveto);

			$max = 250;
			$tw  = $w;
			$th  = $h;

		if ($w > $h && $max < $w)
		{
			$th = $max / $w * $h;
			$tw = $max;
		}
		elseif ($h > $w && $max < $h)
		{
			$tw = $max / $h * $w;
			$th = $max;
		}
		elseif ($max < $w)
		{
			$tw = $th = $max;
		}

		$tmp = imagecreatetruecolor($tw, $th);	//пустая картинка с найденными размерами
		//сжимаем новое изображение и копируем в пустую картинку
		imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
		imageconvolution($tmp, array(array(-1, -1, -1),
						array(-1, 16, -1), array(-1, -1, -1)), 8, 0); //повышаем резкость
		imagejpeg($tmp, $saveto); //сохраняем как jpeg
		imagedestroy($tmp);		//удаляем из памяти
		imagedestroy($src);		//удаляем из памяти
		}
	}
	if ($typeok == FALSE && (!empty($_FILES['image']['type'])))
		echo "<span class='error'>Фотография должна быть в формате jpeg, png или gif!</span><br><br>";
  }

  if ($_GET['p'] == 'del') //Создаем адрес в виде (p=del)
  {
    $file = "$user.jpg";   //Заносим имя файла в переменную
    unlink($file);         //Удаляем аватар
	echo "<span class='taken'>Aватар успешно удален!</span><br>";
  }

  showProfile($user);
  
  if ($_GET['deleteprofile'] == TRUE) //удаляем аккаунт
  {
	if (file_exists("$user.jpg"))
	{
		$file = "$user.jpg";           //Заносим имя файла в переменную
		unlink($file);                 //Удаляем аватар
	}
	queryMysql("DELETE FROM messages WHERE recip='$user' OR auth='$user'");//удаляем все сообщения
	queryMysql("DELETE FROM friends WHERE user='$user' OR friend='$user'");//удаляем друзей
	queryMysql("DELETE FROM profiles WHERE user='$user'");	//удаляем информацию в профиле
	queryMysql("DELETE FROM members WHERE user='$user'");	//удаляем из пользователей
	destroySession(); //уничтожаем сессию
	header ('Location: index.php?deleteinfo=TRUE');			//переход на index.php
  }

  echo <<<_END
    <form method='post' action='profile.php' enctype='multipart/form-data'>
    <h3>Введите информацию о себе и/или загрузите фото:</h3>
    <textarea name='text' cols='50' rows='3' style='resize: none;'>$text</textarea><br>
_END;
?>

    Аватар: <input type='file' name='image' size='14'>
    <input type='submit' value='Сохранить'><br><br>
    </form>	
	<input type='button' value='Удалить профиль' 
		   onclick="if(confirm('Вы действительно хотите удалить профиль?'))
						location.replace('profile.php?deleteprofile=TRUE');">
	</div><br>
  </body>
</html>
