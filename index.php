<?php
  @session_start();
  $root = $_SERVER['DOCUMENT_ROOT'];
  //load controller


  //do routing
  require("$root/config/routing.php");


?>
