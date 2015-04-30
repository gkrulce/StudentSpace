<?php
  $db = new PDO('mysql:host=localhost;dbname=StudyTree;charset=utf8', 'webapp', '');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
