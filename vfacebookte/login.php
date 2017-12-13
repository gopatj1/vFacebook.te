<?php
  require_once 'header.php';  //����� �����
  
  echo "<div class='main'><h3>������� ������ ��� �����������:</h3>";
  $error = $user = $pass = "";

  if (isset($_POST['user'])) //����� �������� �����
  {
    $user = sanitizeString($_POST['user']); //��������� �����
    $pass = sanitizeString($_POST['pass']); //��������� ������
    
    if ($user == "" || $pass == "")
        $error = "<span class='error'>�������� ����� ��� ������</span><br><br>";
    else //��� ���� ����� ����� ��������� - ���� � �� ����� � �������
    {
      $result = queryMySQL("SELECT user,pass FROM members
        WHERE user='$user' AND pass='$pass'");

      if ($result->num_rows == 0) //�� �����
      {
        $error = "<span class='error'>�������� ����� ��� ������</span><br><br>";
      }
      else //�����
      {
        $_SESSION['user'] = $user;	//��������� ��������
        $_SESSION['pass'] = $pass;	//� ������
		//������� �� ������� �����
		echo "<script>location.replace('members.php?view=$user');</script>";
      }
    }
  }

  //����� "����"
  echo <<<_END
    <form method='post' action='login.php'>$error
    <span class='fieldname'>�����</span><input type='text'
      maxlength='16' name='user' value='$user'><br>
    <span class='fieldname'>������</span><input type='password'
      maxlength='16' name='pass' id='joinpasswordbox' value='$pass'/> 
	<label onMouseUp="Show_HidePassword('joinpasswordbox'); return false;">
	<input type='checkbox' onMouseUp="Show_HidePassword('joinpasswordbox', this);
									  return false;" />�������� ������</label>
_END;
?>

    <br>
    <span class='fieldname'>&nbsp;</span>
    <input type='submit' value='�����'>
    </form><br></div>
  </body>
</html>
