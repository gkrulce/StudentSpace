<?php
  include('php/db.php');
  include('php/Procedures.php');
  include('php/Student.php');
  session_start();
  if(isset($_SESSION['user']))
  {
    header('Location: secure/view.php');
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST')
  {
    $pid = $_POST['pid'];
    $row = Procedures::login($db, $pid);
    
    if($row)
    {
        $_SESSION['user'] = new Student($row["pid"], $row["first_name"], $row["hash"]);
        header('Location: secure/view.php'); 
    }else
    {
      echo 'Wrong PID dummy!';
    }
    
    
  }
?>

<form name="login" method="post" action="">
	<p> PID: </p>
	<input name="pid" type="text" id="pid">
</form>
