<?php
  session_start();	//����� ������
  ob_start(); //���������, ��� ������ ������� ���� ����������� � ������ � ���������� ��������
  //������ ����� ���������� ������� ����� ���������. ��� ������� ������ headers already sent by

  echo "<!DOCTYPE html>\n<html><head>";	//������ HTML-���������

  require_once 'functions.php'; //��������� ������� � ��������

  $userstr = ' (�����)'; //�������� ��� ������������

  if (isset($_SESSION['user'])) //������� �� ������������, ���� �� ������?
  {
    $user     = $_SESSION['user'];	 //��������� �����
    $loggedin = TRUE;				 //���������, ��� �����
    $userstr  = " (�������: $user)"; //���������� ����� ������������
  }
  else $loggedin = FALSE;			 //���������, ��� �� �����

  //������� �����-������� � div-���������, ���������� css � javascript.js
  echo "<title>$appname$userstr</title><link rel='stylesheet' "       .
       "href='styles.css' type='text/css'>"                           .
       "</head><body><center><canvas id='logo' width='700' "          .
       "height='94'></canvas></center>"                               .
       "<div class='appname'>���������� ����: $appname$userstr</div>" .
       "<script src='javascript.js'></script>";

  if ($loggedin) //���� ������������ �����
  {
    echo "<ul class='menu'>" .									  //������
         "<li><a href='members.php?view=$user'>�����</a></li>" .  //����
         "<li><a href='members.php'>��������</a></li>"     	   .  //�����
         "<li><a href='friends.php'>������</a></li>"           .  //������
         "<li><a href='messages.php'>���������</a></li>"       .  //���������
         "<li><a href='profile.php'>�������</a></li>"          .  //�������
         "<li><a href='logout.php'>�����</a></li></ul>"; 	  	  //�����
  }
  else			 //������������ �� �����
  {
    echo ("<br><ul class='menu'>" .									//������
          "<li><a href='index.php'>�����</a></li>"                .	//�������
          "<li><a href='signup.php'>������������������</a></li>"  .	//�����������
          "<li><a href='login.php'>�����</a></li></ul><br>"       .	//����
          "<span class='info'>&#8658; �� ������ �������������� ��� " .	
          "���������� ������</span><br><br>");	
  }
?>
