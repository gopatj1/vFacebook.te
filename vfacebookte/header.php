<?php
  session_start();	//старт сессии
  ob_start(); //указывает, что данные вначале надо накапливать в буфере и отправлять браузеру
  //только когда выполнение скрипта будет завершено. Для решения ошибки headers already sent by

  echo "<!DOCTYPE html>\n<html><head>";	//начало HTML-документа

  require_once 'functions.php'; //включение функций в документ

  $userstr = ' (Гость)'; //содержит имя пользователя

  if (isset($_SESSION['user'])) //заходил ли пользователь, есть ли сессия?
  {
    $user     = $_SESSION['user'];	 //фиксируем логин
    $loggedin = TRUE;				 //фиксируем, что вошел
    $userstr  = " (Аккаунт: $user)"; //показываем логин пользователя
  }
  else $loggedin = FALSE;			 //фиксируем, что не вошел

  //создаем холст-эмблему и div-контейнер, подгружаем css и javascript.js
  echo "<title>$appname$userstr</title><link rel='stylesheet' "       .
       "href='styles.css' type='text/css'>"                           .
       "</head><body><center><canvas id='logo' width='700' "          .
       "height='94'></canvas></center>"                               .
       "<div class='appname'>Социальная сеть: $appname$userstr</div>" .
       "<script src='javascript.js'></script>";

  if ($loggedin) //если пользователь вошел
  {
    echo "<ul class='menu'>" .									  //кнопки
         "<li><a href='members.php?view=$user'>Домой</a></li>" .  //юзер
         "<li><a href='members.php'>Общество</a></li>"     	   .  //юзеры
         "<li><a href='friends.php'>Друзья</a></li>"           .  //друзья
         "<li><a href='messages.php'>Сообщения</a></li>"       .  //сообщения
         "<li><a href='profile.php'>Профиль</a></li>"          .  //профиль
         "<li><a href='logout.php'>Выйти</a></li></ul>"; 	  	  //выход
  }
  else			 //пользователь не вошел
  {
    echo ("<br><ul class='menu'>" .									//кнопки
          "<li><a href='index.php'>Домой</a></li>"                .	//главная
          "<li><a href='signup.php'>Зарегистрироваться</a></li>"  .	//регистрация
          "<li><a href='login.php'>Войти</a></li></ul><br>"       .	//вход
          "<span class='info'>&#8658; Вы должны авторизоваться для " .	
          "дальнейшей работы</span><br><br>");	
  }
?>
