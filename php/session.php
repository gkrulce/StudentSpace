<?php
include('Student.php'); //auto includes db.php
session_start();
// Storing Session
if(!isset($_SESSION['user']))
{
	header('Location: index.html');
}

?>
