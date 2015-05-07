<?php
  $GLOBALS['dbn'] = 'mysql:host=localhost;dbname=StudyTree;charset=utf8';
  $GLOBALS['dbusr'] = 'webapp';
  $GLOBALS['dbpass'] = '';
  $GLOBALS['db'] = new PDO($GLOBALS['dbn'], $GLOBALS['dbusr'], $GLOBALS['dbpass']);
  $GLOBALS['db']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
