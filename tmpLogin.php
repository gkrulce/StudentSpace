<?php
session_start();
  if(isset($_SESSION['user']))
  {
    header('Location: view.php');
  }

?>

<form name="login" method="post" action="php/login.php">
	<p> Username: </p>
	<input name="user_name" type="text" id="user_name">
</form>
