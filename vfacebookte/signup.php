<?php
  require_once 'header.php';  //шапка сайта

  echo <<<_END
  <script>
    function checkUser(user)
    {
      if (user.value == '')
      {
        O('info').innerHTML = ''
        return
      }

      params  = "user=" + user.value
      request = new ajaxRequest()
      request.open("POST", "checkuser.php", true) //метод, адрес, обязательность ассинхронного режима
      request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
      request.setRequestHeader("Content-length", params.length)
      request.setRequestHeader("Connection", "close")

      request.onreadystatechange = function() //состояние запроса
      {
        if (this.readyState == 4) //завершен
          if (this.status == 200) //статус 200 - вызов прошел удачно
            if (this.responseText != null) //данные от сервера в текстовом формате
              O('info').innerHTML = this.responseText
      }
      request.send(params) //отправка данных серверу
    }

    function ajaxRequest()
    {
      try { var request = new XMLHttpRequest() } //не Internet Explorer
      catch(e1) {
        try { request = new ActiveXObject("Msxml2.XMLHTTP") } //IE6+
        catch(e2) {
          try { request = new ActiveXObject("Microsoft.XMLHTTP") } //IE5
          catch(e3) {
            request = false //не поддерживает ajax
      } } }
      return request
    }
  </script>
  <div class='main'><h3>Введите данные для регистрации:</h3>
_END;

  $fail = $field = $field1 = $user = $pass = $passrepeat = "";
  if (isset($_SESSION['user'])) destroySession();

  if (isset($_POST['user']))
  {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
	$passrepeat = $_POST['passrepeat'];

	//не удовлетворяет требованиям
    $fail .= validate_user($user);
    $fail .= validate_pass($pass);
	$fail .= validate_passrepeat($pass, $passrepeat);

	if ($fail != "")	//не удовлетворяет требованиям
		$fail .= "<br>";
    else	//удовлетворяет требованиям - то создать аккаунт
    {
      queryMysql("INSERT INTO members VALUES('$user', '$pass')");
      die("<h4>Аккаунт создан!</h4>Пожалуйста авторизуйтесь!<br><br>");
    }
  }

  //форма регистрации
  echo <<<_END
    <form method='post' action='signup.php' onSumbit='return validate(this),checkUser(this)'>$fail
    <span class='fieldname'>Логин</span>
    <input type='text' maxlength='16' name='user' value='$user'
      onBlur='checkUser(this)'><span id='info'></span><br>
    <span class='fieldname'>Пароль</span>
    <input type='password' name='pass' id='joinpasswordbox' value='$pass'/> 
	<label onMouseUp="Show_HidePassword('joinpasswordbox', 'joinpasswordboxrepeat'); return false;"><input type='checkbox' />Показать пароль</label><br>
	<span class='fieldname'>Повторите</span>
    <input type='password' name='passrepeat' id='joinpasswordboxrepeat' value='$passrepeat'/>

_END;

  function validate_user($field) //соответствие логина требованиям
  {
    if ($field == "") return "<span class='error'>Логин не введен!</span><br>";
    else if (strlen($field) < 5)
      return "<span class='error'>Логин должен быть не короче 5 символов!</span><br>";
    else if (preg_match("/[^a-zA-Z0-9_-]/", $field))
      return "<span class='error'>Логин может содержать только латинские буквы, цифры, - и _ !</span><br>";
	else if (isset($_POST['user']))	//сравниваем введенное с sql данными
    {
      $user = $_POST['user'];
	  $result = queryMysql("SELECT * FROM members WHERE user='$user'");
      if ($result->num_rows)
         return "<span class='error'>Это имя уже занято!</span><br>";
	}
    return "";
  }

  function validate_pass($field) //соответствие пароля требованиям
  {
    if ($field == "") return "<span class='error'>Пароль не введен!</span><br>";
    else if (strlen($field) < 6)
      return "<span class='error'>Пароль должен быть не короче 6 символов!</span><br>";
    else if (preg_match("/[^a-zA-Z0-9]/", $field))
      return "<span class='error'>Пароль может содержать только латинские буквы и цифры!</span><br>";
    return "";
  }

  function validate_passrepeat($field, $field1) //сравнения повторенного пароля
  {
    if ($field != $field1) return "<span class='error'>Пароли не совпадают!</span><br>";
    return "";
  }
?>

	<br>
    <span class='fieldname'>&nbsp;</span> <!--Пустое место, имеющее значение-->
    <input type='submit' value='Зарегистрироваться'>
    </form></div><br>
  </body>
</html>
