<?php
  require_once 'header.php';  //����� �����

  if (isset($_SESSION['user'])) //���� ���� ������
  {
    destroySession();  //������� ������
	echo "<script>location.replace('index.php');</script>";
  }
  else echo "<div class='main'><br>" .
            "�� �� ������ ��������� ����� �� ��������, ��� ��� �� ��� �������� ����!";
?>

    <br><br></div>
  </body>
</html>
