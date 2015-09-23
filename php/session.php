<?php
include('db.php');
include('Procedures.php'); //Generic database queries
include('Student.php'); // Database queries with student
session_start();
if (array_key_exists('user', $_SESSION))
{
	return;
}
$pid = $_SERVER['PID'];
$row = Procedures::login($db, $pid);

if($row)
{
	Procedures::logLogin($db, $row["pid"]);
	$_SESSION['user'] = new Student($row["pid"], $row["first_name"], $row["hash"]);
}else
{
	Procedures::register($db, $_SERVER);
	header('Location: /secure/newUser.php');
}
?>
