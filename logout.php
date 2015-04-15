<?php
session_start();
if(session_destroy())
{
  header("Location: index.html"); // Redirecting To Home Page
}
?>
