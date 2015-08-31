<?php
include('db.php');
include('Procedures.php'); //Generic database queries
include('Student.php'); // Database queries with student
session_start();
// Storing Session
if(!isset($_SESSION['user']))
{
	header('Location: ../index.html');
}

?>
