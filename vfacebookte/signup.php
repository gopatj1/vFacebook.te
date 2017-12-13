<?php
  require_once 'header.php';  //����� �����

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
      request.open("POST", "checkuser.php", true) //�����, �����, �������������� ������������� ������
      request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
      request.setRequestHeader("Content-length", params.length)
      request.setRequestHeader("Connection", "close")

      request.onreadystatechange = function() //��������� �������
      {
        if (this.readyState == 4) //��������
          if (this.status == 200) //������ 200 - ����� ������ ������
            if (this.responseText != null) //������ �� ������� � ��������� �������
              O('info').innerHTML = this.responseText
      }
      request.send(params) //�������� ������ �������
    }

    function ajaxRequest()
    {
      try { var request = new XMLHttpRequest() } //�� Internet Explorer
      catch(e1) {
        try { request = new ActiveXObject("Msxml2.XMLHTTP") } //IE6+
        catch(e2) {
          try { request = new ActiveXObject("Microsoft.XMLHTTP") } //IE5
          catch(e3) {
            request = false //�� ������������ ajax
      } } }
      return request
    }
  </script>
  <div class='main'><h3>������� ������ ��� �����������:</h3>
_END;

  $fail = $field = $field1 = $user = $pass = $passrepeat = "";
  if (isset($_SESSION['user'])) destroySession();

  if (isset($_POST['user']))
  {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
	$passrepeat = $_POST['passrepeat'];

	//�� ������������� �����������
    $fail .= validate_user($user);
    $fail .= validate_pass($pass);
	$fail .= validate_passrepeat($pass, $passrepeat);

	if ($fail != "")	//�� ������������� �����������
		$fail .= "<br>";
    else	//������������� ����������� - �� ������� �������
    {
      queryMysql("INSERT INTO members VALUES('$user', '$pass')");
      die("<h4>������� ������!</h4>���������� �������������!<br><br>");
    }
  }

  //����� �����������
  echo <<<_END
    <form method='post' action='signup.php' onSumbit='return validate(this),checkUser(this)'>$fail
    <span class='fieldname'>�����</span>
    <input type='text' maxlength='16' name='user' value='$user'
      onBlur='checkUser(this)'><span id='info'></span><br>
    <span class='fieldname'>������</span>
    <input type='password' name='pass' id='joinpasswordbox' value='$pass'/> 
	<label onMouseUp="Show_HidePassword('joinpasswordbox', 'joinpasswordboxrepeat'); return false;"><input type='checkbox' />�������� ������</label><br>
	<span class='fieldname'>���������</span>
    <input type='password' name='passrepeat' id='joinpasswordboxrepeat' value='$passrepeat'/>

_END;

  function validate_user($field) //������������ ������ �����������
  {
    if ($field == "") return "<span class='error'>����� �� ������!</span><br>";
    else if (strlen($field) < 5)
      return "<span class='error'>����� ������ ���� �� ������ 5 ��������!</span><br>";
    else if (preg_match("/[^a-zA-Z0-9_-]/", $field))
      return "<span class='error'>����� ����� ��������� ������ ��������� �����, �����, - � _ !</span><br>";
	else if (isset($_POST['user']))	//���������� ��������� � sql �������
    {
      $user = $_POST['user'];
	  $result = queryMysql("SELECT * FROM members WHERE user='$user'");
      if ($result->num_rows)
         return "<span class='error'>��� ��� ��� ������!</span><br>";
	}
    return "";
  }

  function validate_pass($field) //������������ ������ �����������
  {
    if ($field == "") return "<span class='error'>������ �� ������!</span><br>";
    else if (strlen($field) < 6)
      return "<span class='error'>������ ������ ���� �� ������ 6 ��������!</span><br>";
    else if (preg_match("/[^a-zA-Z0-9]/", $field))
      return "<span class='error'>������ ����� ��������� ������ ��������� ����� � �����!</span><br>";
    return "";
  }

  function validate_passrepeat($field, $field1) //��������� ������������ ������
  {
    if ($field != $field1) return "<span class='error'>������ �� ���������!</span><br>";
    return "";
  }
?>

	<br>
    <span class='fieldname'>&nbsp;</span> <!--������ �����, ������� ��������-->
    <input type='submit' value='������������������'>
    </form></div><br>
  </body>
</html>
