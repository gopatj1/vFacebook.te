<?php
  require_once 'header.php';  //����� �����

  if (!$loggedin) die();

  if (isset($_GET['view'])) $view = sanitizeString($_GET['view']);
  else                      $view = $user;

  echo "<div class='main'>";

  $followers = array(); //������ �����������
  $following = array(); //������ ��������
	
  //����� �����������
  $result = queryMysql("SELECT * FROM friends WHERE user='$view'");
  $num    = $result->num_rows;

  for ($j = 0 ; $j < $num ; ++$j)
  {
    $row           = $result->fetch_array(MYSQLI_ASSOC);
    $followers[$j] = $row['friend'];
  }

  //����� ��������
  $result = queryMysql("SELECT * FROM friends WHERE friend='$view'");
  $num    = $result->num_rows;

  for ($j = 0 ; $j < $num ; ++$j)
  {
      $row           = $result->fetch_array(MYSQLI_ASSOC);
      $following[$j] = $row['user'];
  }
  
  /*��������� ���� ����������,
  ���������� ������ ��� ����� ��������, � ���������� ����� ������, 
  ������� �������� ������ ���� �����. �������� ������*/
  $mutual    = array_intersect($followers, $following);	
  $followers = array_diff($followers, $mutual); //������ ����������
  $following = array_diff($following, $mutual); //������ ��������
  $friends   = FALSE;

  if (sizeof($mutual)) //���� �� �������, �� ���� ���� ������
  {
    echo "<span class='subhead'>���� ������:</span><ul>";
    foreach($mutual as $friend)
      echo "<li><a class='userslogin' href='members.php?view=$friend'>$friend</a>";
    echo "</ul>";
    $friends = TRUE;
  }

  if (sizeof($followers))//����� �������
  {
    echo "<span class='subhead'>����� �������:</span><ul>";
    foreach($followers as $friend)
      echo "<li><a class='userslogin' href='members.php?view=$friend'>$friend</a>";
    echo "</ul>";
    $friends = TRUE;
  }

  if (sizeof($following)) //��������� ������
  {
    echo "<span class='subhead'>��������� ������:</span><ul>";
    foreach($following as $friend)
      echo "<li><a class='userslogin' href='members.php?view=$friend'>$friend</a>";
    echo "</ul>";
    $friends = TRUE;
  }

  if (!$friends) echo "<br>� ��� ��� ������, ����������� � ��������.<br><br>";
?>

    </div><br>
  </body>
</html>
