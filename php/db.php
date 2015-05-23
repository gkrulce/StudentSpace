<?php
  $db = new PDO('mysql:host=localhost;dbname=ucsdspace;charset=utf8', 'webapp', '');
  $GLOBALS['db_errmode'] = PDO::ERRMODE_EXCEPTION;
?>
