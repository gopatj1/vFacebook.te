<?php
  require_once 'header.php';  //����� �����

  if (!$loggedin) die();

  echo "<div class='main'>";

  if (isset($_GET['view']))	//�������� ������� ������������
  {
    $view = sanitizeString($_GET['view']);
    
    if ($view == $user) $name = "������ ��������";
    else                $name = "$view";
    
    echo "<h3>������� $name</h3>";
    showProfile($view);		//����������� �������
	if ($view != $user)		//������ � ������� ������ ������
	{
		//������ ��������� ���������
		echo "<a class='button' href='messages.php?view=$view'>" .
			"��������� ���������</a>";

		//������ �������/���������/������� ������
		$result = queryMysql("SELECT * FROM friends WHERE (user='$view' AND friend='$user')");
		$result1 = queryMysql("SELECT * FROM friends WHERE (friend='$view' AND user='$user')");
		if (!$result->num_rows && !$result1->num_rows)
		{
			echo "<a class='button' href='members.php?add=$view'>" .
				"��������� ������</a><br><br>";	
		}
		elseif ($result->num_rows && !$result1->num_rows)
			echo "<a class='button' href='members.php?remove=$view'>" .
				"�������� ������ �� ������</a><br><br>";
		elseif (!$result->num_rows && $result1->num_rows)
			echo "<a class='button' href='members.php?add=$view'>" .
				"������� ������</a><br><br>";
		else 
			echo "<a class='button' href='members.php?remove=$view'>" .
				"������� �� ������</a><br><br>";
	}
	else 	//������ � ����� �������
	{
		echo "<a class='button' href='messages.php'>" .
				"�������� ���������</a>";
		echo "<a class='button' href='profile.php'>" .
				"������������� �������</a><br><br>";		
	}
    die("</div></body></html>");
  }

  if (isset($_GET['add']))	//���������� � ������
  {
    $add = sanitizeString($_GET['add']);

    $result = queryMysql("SELECT * FROM friends WHERE user='$add' AND friend='$user'");
    if (!$result->num_rows)
      queryMysql("INSERT INTO friends VALUES ('$add', '$user')");
  }
  elseif (isset($_GET['remove']))	//��������
  {
    $remove = sanitizeString($_GET['remove']);
    queryMysql("DELETE FROM friends WHERE user='$remove' AND friend='$user'");
  }	

  //��������� �� �� ���� �������������
  $result = queryMysql("SELECT user FROM members ORDER BY user");
  $num    = $result->num_rows;

  echo "<h3>������ ������������:</h3><ul>";

  for ($j = 0 ; $j < $num ; ++$j)
  {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    if ($row['user'] == $user) continue; //���� �� ����� ��� ����

    echo "<li><a class='userslogin' href='members.php?view=" .
      $row['user'] . "'>" . $row['user'] . "</a>";
    $follow = "<span class='queryFriend'>&#9787;</span>";	//������ � ������

	//�������� �� ��������
    $result1 = queryMysql("SELECT * FROM friends WHERE
      user='" . $row['user'] . "' AND friend='$user'");
    $t1      = $result1->num_rows;
    $result1 = queryMysql("SELECT * FROM friends WHERE
      user='$user' AND friend='" . $row['user'] . "'");
    $t2      = $result1->num_rows;

	//���� ���������, �� ������
    if (($t1 + $t2) > 1) echo " &harr; ";	//����
    elseif ($t1)         echo " &larr; ";	//��������� ������
    elseif ($t2)       { echo " &rarr; ";	//����� �������
      $follow = "<span class='available'>&#x2714;"; } //������� ������
    
    if (!$t1) echo " <a href='members.php?add="   .$row['user'] . "'>$follow</a>";
    else      echo " <a href='members.php?remove=".$row['user'] . "'><span class='taken'>&#x2718;</span></a>"; //������� ������ � ������
	//��������
	echo " <a href='messages.php?view=" . $row['user'] . "'><span class='letter'>&#x2709;</span></a>";
  }
?>

    </ul></div>
  </body>
</html>
