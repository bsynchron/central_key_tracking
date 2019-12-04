<?php

$request = $_SERVER['REQUEST_URI'];

if(substr($request, 0, 4) == "/api"){
  file_put_contents("php://stdout", "API REQUEST\n");

  include "$root/api/index.php";







} else {
  file_put_contents("php://stdout", "NORMAL REQUEST\n");

  $paths = ["/","/views/index.php","CKT"];

  if(substr($request, -1) == "/" and strlen($request) > 1){
    $request = substr_replace($request, "", -1);
  }

  $pathindex = array_search($request, $paths);

  if(is_int($pathindex)){
    $title = $paths[$pathindex+2];
    if(in_array($request, $all_access)){
      include($root."/views/default.php");
      require "$root".$paths[$pathindex+1];
    } elseif($_SESSION['access_level'] >= $paths[$pathindex+3]) {
      include($root."/views/default.php");
      require "$root".$paths[$pathindex+1];
    } else {
      //403
      echo("403");
      die();
    }
  } else {
    //404
    echo("404");
    die();
  }
}
?>
