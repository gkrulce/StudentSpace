<?php
  include('session.php');
  $username=$_POST['user_name'];
  $row = Procedures::login($db, $username);

  if($row)
  {
    session_start();
    $_SESSION['user'] = new Student($row["pid"], $row["name"], $row["hash"]);
    header('Location: ../view.php');
  }else
  {
    echo "Try username: demo.\n";
  }
?>
