<?php
  include('mail.php');
  include('session.php');

  function getFile($filename) {
    $myfile = fopen($filename, "r") or die("Unable to open file!");
    $text = fread($myfile,filesize($filename));
    fclose($myfile);
    return $text;
  }
?>
