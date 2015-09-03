<?php
include('../php/session.php');
$name = $_SESSION['user']->getName();
echo "Welcome $name!<br>";
echo "This is your first time logging in. An account has been created.<br>";
echo '<a href="view.php">Continue to website</a>';
?>
