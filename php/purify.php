<?php
  require_once(__DIR__ . '/../lib/htmlpurifier/library/HTMLPurifier.auto.php');
  $config = HTMLPurifier_Config::createDefault();
  $purifier = new HTMLPurifier($config);
?>
