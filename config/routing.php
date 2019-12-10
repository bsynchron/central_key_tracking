<?php
$request = $_SERVER['REQUEST_URI'];

if(substr($request, 0, 4) == "/api"){
  file_put_contents("php://stdout", "API REQUEST\n");
  include "$root/api/index.php";

} else {
  file_put_contents("php://stdout", "NORMAL REQUEST\n");

$allowed = ['/register'];

  if(!isset($_SESSION['user']) and !in_array($request, $allowed)){
    file_put_contents("php://stdout", "REDIRECTED TO LOGIN - WANTED $request\n");
    $_SESSION['wanted'] = $request;
    require "$root/views/user/login.php";
    die();
  }

  $paths = ["/","/views/index.php","CKT",
            "/map", "/views/map/index.php", "MAP",
            "/login", "/views/user/login.php", "LOGIN",
            "/logout", "/views/user/logout.php", "Logout",
            "/register", "/views/user/register.php", "Register"];

  if(substr($request, -1) == "/" and strlen($request) > 1){
    $request = substr_replace($request, "", -1);
  }

  $args = explode("?", $request)[1];
  $request = explode("?", $request)[0];

  file_put_contents("php://stdout", "ARGS: $args\n");
  $pathindex = array_search($request, $paths);

  if(is_int($pathindex)){
    $title = $paths[$pathindex+2];
    include($root."/views/default.php");
    require "$root".$paths[$pathindex+1];
  } else {
    //404
    echo("404");
    die();
  }
}
?>
