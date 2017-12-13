<?php 
  $dbhost  = 'localhost';       // ������
  $dbname  = 'soc_set';   		// ��
  $dbuser  = 'igor';  		    // ������������
  $dbpass  = 'qwerty';   	 	// ������
  $appname = "�facebook.��"; 	// ��� ����������

  $connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);    //����������
  if ($connection->connect_error) die($connection->connect_error); //������
  
  function createTable($name, $query)	//������� �������� �������
  {
    queryMysql("CREATE TABLE IF NOT EXISTS $name($query)"); //���������� �������
    echo "������� '$name' ������� ��� ��� ������� � ��!<br>";
  }

  function queryMysql($query)				//������� ������-�������
  {
    global $connection;						//����������� � ��
    $result = $connection->query($query);	//��������� �������-���������
    if (!$result) die($connection->error);	//����� ������
    return $result;							//��� ����������
  }
  //������������� ��������� �  �� ��� ��������� �������� ��� �������� ������ � ��
  queryMysql("SET NAMES 'cp1251'"); 

  function destroySession()	//������� ���������� ������
  {
    $_SESSION=array();	//�������� ������ _session
	// ���� ������ ��� ���� ���� � ��������� ������
    if (session_id() != "" || isset($_COOKIE[session_name()])) 
      setcookie(session_name(), '', time()-2592000, '/'); //�������� ����
    session_destroy();									  //���������� ������
  }

  function sanitizeString($var) //������� ��������������
  {
    global $connection;
    $var = strip_tags($var);	//��������� ������� HTML-���
    //$var = htmlentities($var, ENT_QUOTES, 'UTF-8');	
	//�������� HTML-��� � ����, �� ����������� � ������� ������, �.�. ��� � CP-1251
    $var = stripslashes($var);	//�� ������
    return $connection->real_escape_string($var); //�������������� ����������� ��������
  }  

  function showProfile($user) //������� �������� �������
  {
    if (file_exists("$user.jpg"))	//���� ���� ��������
	{
      echo "<div class='photo' style='float:left;'><img src='$user.jpg'>";
	  
	  //������ ������� ���� ������ � ������� ��������������
	  if (isset($_GET['view']))
			$view = sanitizeString($_GET['view']);
	  if ($view != $user)
			echo "<div class='butUnderPhoto'><input type='button' value='������� ����' 
				onClick=\"if(confirm('�� ������������� ������ ������� ������?'))
					    location.href='profile.php?p=del';\"></div><br><br>";
	  echo "</div>";
	}
	
    $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");

    if ($result->num_rows)
    {
	  //���������� �������������� ������� � ������� ������� profiles
      $row = $result->fetch_array(MYSQLI_ASSOC);
	  //����� �������� ������� text ��� ������������ $user
      echo stripslashes($row['text']) . "<br style='clear:left;'><br>";
    }
  }
?>
