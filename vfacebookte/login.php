<?php
  require_once 'header.php';  //шапка сайта
  
  echo "<div class='main'><h3>Введите данные для авторизации:</h3>";
  $error = $user = $pass = "";

  if (isset($_POST['user'])) //форма прислала логин
  {
    $user = sanitizeString($_POST['user']); //фиксируем логин
    $pass = sanitizeString($_POST['pass']); //фиксируем пароль
    
    if ($user == "" || $pass == "")
        $error = "<span class='error'>Неверный логин или пароль</span><br><br>";
    else //все поля формы входа заполнены - ищем в БД юзера с паролем
    {
      $result = queryMySQL("SELECT user,pass FROM members
        WHERE user='$user' AND pass='$pass'");

      if ($result->num_rows == 0) //не нашли
      {
        $error = "<span class='error'>Неверный логин или пароль</span><br><br>";
      }
      else //нашли
      {
        $_SESSION['user'] = $user;	//сохраняем значения
        $_SESSION['pass'] = $pass;	//в сессию
		//переход на вкладку домой
		echo "<script>location.replace('members.php?view=$user');</script>";
      }
    }
  }

  //форма "вход"
  echo <<<_END
    <form method='post' action='login.php'>$error
    <span class='fieldname'>Логин</span><input type='text'
      maxlength='16' name='user' value='$user'><br>
    <span class='fieldname'>Пароль</span><input type='password'
      maxlength='16' name='pass' id='joinpasswordbox' value='$pass'/> 
	<label onMouseUp="Show_HidePassword('joinpasswordbox'); return false;">
	<input type='checkbox' onMouseUp="Show_HidePassword('joinpasswordbox', this);
									  return false;" />Показать пароль</label>
_END;
?>

    <br>
    <span class='fieldname'>&nbsp;</span>
    <input type='submit' value='Войти'>
    </form><br></div>
  </body>
</html>
