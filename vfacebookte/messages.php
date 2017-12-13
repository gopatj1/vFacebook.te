<?php
  require_once 'header.php';  //����� �����

  if (!$loggedin) die();

  if (isset($_GET['view'])) $view = sanitizeString($_GET['view']);
  else                      $view = $user;

  if (isset($_POST['text']))
  {
    $text = sanitizeString($_POST['text']);

    if ($text != "")	//���� ���������
    {
      $time = time();	//����� �����������
      queryMysql("INSERT INTO messages VALUES(NULL, '$user',
        '$view', '$pm', $time, '$text')");
    }
  }

  if ($view != "")
  {
    if ($view == $user) $name1 =  "�������� ���������";
    else
      $name1 = "��������� ��� � ������������� <a class='userslogin' href='members.php?view=$view'>$view</a>";

    echo "<div class='main'><h3>$name1</h3>";
    
	if ($view != $user)
	{
	  $check = TRUE;	//��������� ��� ��� ���� ���������
		echo <<<_END
      <form method='post' action='messages.php?view=$view'>
      ������� ����� ���������:<br>
      <textarea name='text' cols='44' rows='3' style='resize: none;'></textarea><br>
      <input type='submit' value=' ��������� ��������� '></form><br>
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
	$authors = array();	//������, ���� �������, �������� ������������
    if (!$check)	//���������� � ��������� � ���� ���������
	{
	  $chatmess = "";	//��� ��������� �� �������� ����������
	  for ($j = 0 ; $j < $num ; ++$j)
      {
        $row = $result->fetch_array(MYSQLI_ASSOC);
        if ($row['auth'] != $user && $row['recip'] == $user)
        {
			//��������� ��������� �� �����, ���� ��������� ���������
		    for ($i = 0 ; $i < count($authors) ; $i++)
				if ($row['auth'] == $authors[$i])
				{
					$alreadysent = TRUE; //��� ��������
					break;
				}
				else 
					$alreadysent = FALSE; //�� ���������
			
			if (!$alreadysent)	//���� �� ���������, �� ������� ����������
			{
				$chatmess .= "<br>" . date('d.m.Y H:i', $row['time']); //����
				
				if ($row['auth'] != $user)	//������ ��������, ������� � ���
					$chatmess .= "  <a href='messages.php?view=" 		  .
						$row['auth'] . "' style='text-decoration:none;'>" .
						"<span class='letter'>&#x2709;</span></a>";

				if ($row['recip'] == $user)	//������ �������� ���������
					$chatmess .= "  <a class='taken' href='messages.php?view=$view" .
               "&erase=" . $row['id'] . "' >&#x2718;</a>";
			   
			    if ($row['pm'] == "") //���� ��������� �� ���������
					$chatmess .= "  <span class='queryFriend'>�� ���������</span>";

				$chatmess .= "<br>������������ " . $row['auth'] .
							 " ������� ���������!<br>";
			}
			$authors[] = $row['auth'];	//��������� j-��� ������ � ������
        }	  
      }
	  if($chatmess == "") //��� ���������
		  echo "<br><span class='info'>��� ���������</span><br><br>";
	  else
		  echo $chatmess; //��� ��������� �� �������� ����������
	}

	//��� ���������� ������������� ���� ���������� ������ � �������������� ���������� � ��������� � �����
	$query1  = "SELECT * FROM messages WHERE (auth='$view' and recip='$user') or (auth='$user' and recip='$view') ORDER BY time DESC";
    $result1 = queryMysql($query1);
	$num1    = $result1->num_rows;
	if ($check)	//��� � �������������
	{
	  $chatmess1 = "";	//��� ��������� � ����
	  $idmess = ""; 	//���� ���������
	  for ($j = 0 ; $j < $num1 ; ++$j)
      {
        $row1 = $result1->fetch_array(MYSQLI_ASSOC);

        if ($row1['auth'] == $user)	//��������� �� ������������
        {
          $chatmess1 .= "<br>" . date('d.m.Y H:i - ', $row1['time']) .
						"������������ " . $row1['auth'] . " "        .
						"��������: <br><span class='whisper'>"       .
						$row1['message'] . "</span> ";

        if ($row1['recip'] == $user || $row1['auth'] == $user) //�������� ���������
          $chatmess1 .= "  <a class='taken' href='messages.php?view=$view" .
               "&erase=" . $row1['id'] . "'>&#x2718;</a><br>";
        }
       
        if ($row1['recip'] == $user) //��������� ������������
        {
          $chatmess1 .= "<br>" . date('d.m.Y H:i - ', $row1['time']) .
						"������������ " . $row1['auth'] . " "        .
						"��������: <br><span class='whisper'>"       .
						$row1['message'] . "</span> ";

        if ($row1['recip'] == $user)	//�������� ���������
          $chatmess1 .= "  <a class='taken' href='messages.php?view=$view" .
             "&erase=" . $row1['id'] . "'>&#x2718;</a><br>";
        }

		//�������� �������� �� �����������
		queryMysql("UPDATE messages SET pm='1' WHERE recip='$user' and auth='$view'");
	  }
	  if($chatmess1 == "") //��� ��������� � ����
		  echo "<br><span class='info'>��� ���������</span><br><br>";
	  else
		  echo $chatmess1; //��� ��������� � ����
	}
  }

  echo "<br><a class='button' href='messages.php?view=$view'>��������</a>";
  if (($view == $user))
	echo "<a class='button' href='members.php'>��������� ���������</a>";
?>

    </div><br>
  </body>
</html>
