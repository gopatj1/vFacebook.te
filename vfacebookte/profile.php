<?php
  require_once 'header.php';  //����� �����

  if (!$loggedin) die();

  echo "<div class='main'><h3>������������� ��� �������</h3>";

  $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");
    
  if (isset($_POST['text'])) //���� ����� �������� �����
  {
    $text = sanitizeString($_POST['text']); //��������������
    $text = preg_replace('/\s\s+/', ' ', $text);

    if ($result->num_rows) //��������� � ��
         queryMysql("UPDATE profiles SET text='$text' WHERE user='$user'");
    else queryMysql("INSERT INTO profiles VALUES('$user', '$text')");
  }
  else
  {
    if ($result->num_rows) //������� ��� ������� � �� 
    {
      $row  = $result->fetch_array(MYSQLI_ASSOC);
      $text = sanitizeString($row['text']);
    }
    else $text = "";
  }

  $text = stripslashes(preg_replace('/\s\s+/', ' ', $text));

  if (isset($_FILES['image']['name'])) //���� ����� �������� ��������
  {
    $saveto = "$user.jpg";
	//������������ ������ 4 �������
	if ($_FILES['image']['type'] == "image/jpeg" || $_FILES['image']['type'] == "image/gif" 
	|| $_FILES['image']['type'] == "image/pjpeg" || $_FILES['image']['type'] == "image/png")
	{
		//���������� ����������� ���� � ����������
		move_uploaded_file($_FILES['image']['tmp_name'], $saveto);
		$typeok = TRUE;
		
		switch($_FILES['image']['type']) //������� ����� �����������
		{
		case "image/gif":   $src = imagecreatefromgif($saveto); break;
		case "image/jpeg": 
		case "image/pjpeg": $src = imagecreatefromjpeg($saveto); break;
		case "image/png":   $src = imagecreatefrompng($saveto); break;
		default:            $typeok = FALSE; break;
		}

		if ($typeok) //��������� ������� ��� �������
		{
			list($w, $h) = getimagesize($saveto);

			$max = 250;
			$tw  = $w;
			$th  = $h;

		if ($w > $h && $max < $w)
		{
			$th = $max / $w * $h;
			$tw = $max;
		}
		elseif ($h > $w && $max < $h)
		{
			$tw = $max / $h * $w;
			$th = $max;
		}
		elseif ($max < $w)
		{
			$tw = $th = $max;
		}

		$tmp = imagecreatetruecolor($tw, $th);	//������ �������� � ���������� ���������
		//������� ����� ����������� � �������� � ������ ��������
		imagecopyresampled($tmp, $src, 0, 0, 0, 0, $tw, $th, $w, $h);
		imageconvolution($tmp, array(array(-1, -1, -1),
						array(-1, 16, -1), array(-1, -1, -1)), 8, 0); //�������� ��������
		imagejpeg($tmp, $saveto); //��������� ��� jpeg
		imagedestroy($tmp);		//������� �� ������
		imagedestroy($src);		//������� �� ������
		}
	}
	if ($typeok == FALSE && (!empty($_FILES['image']['type'])))
		echo "<span class='error'>���������� ������ ���� � ������� jpeg, png ��� gif!</span><br><br>";
  }

  if ($_GET['p'] == 'del') //������� ����� � ���� (p=del)
  {
    $file = "$user.jpg";   //������� ��� ����� � ����������
    unlink($file);         //������� ������
	echo "<span class='taken'>A����� ������� ������!</span><br>";
  }

  showProfile($user);
  
  if ($_GET['deleteprofile'] == TRUE) //������� �������
  {
	if (file_exists("$user.jpg"))
	{
		$file = "$user.jpg";           //������� ��� ����� � ����������
		unlink($file);                 //������� ������
	}
	queryMysql("DELETE FROM messages WHERE recip='$user' OR auth='$user'");//������� ��� ���������
	queryMysql("DELETE FROM friends WHERE user='$user' OR friend='$user'");//������� ������
	queryMysql("DELETE FROM profiles WHERE user='$user'");	//������� ���������� � �������
	queryMysql("DELETE FROM members WHERE user='$user'");	//������� �� �������������
	destroySession(); //���������� ������
	header ('Location: index.php?deleteinfo=TRUE');			//������� �� index.php
  }

  echo <<<_END
    <form method='post' action='profile.php' enctype='multipart/form-data'>
    <h3>������� ���������� � ���� �/��� ��������� ����:</h3>
    <textarea name='text' cols='50' rows='3' style='resize: none;'>$text</textarea><br>
_END;
?>

    ������: <input type='file' name='image' size='14'>
    <input type='submit' value='���������'><br><br>
    </form>	
	<input type='button' value='������� �������' 
		   onclick="if(confirm('�� ������������� ������ ������� �������?'))
						location.replace('profile.php?deleteprofile=TRUE');">
	</div><br>
  </body>
</html>
