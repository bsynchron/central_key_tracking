<?php
$request = substr($_SERVER['REQUEST_URI'], 4);
if(substr($request, 0, 1) != "/"){
  $request = "/$request";
}

$api_requests = explode("/", $request);
file_put_contents("php://stdout", json_encode($api_requests));

switch ($api_requests[1]) {
  case 'test':
    $response=["rs" => 200, "endpoint" => "$request" , "content" => ["test" => true]];
    print(json_encode($response));
    break;

  case 'status':
    //return status
    $response=["rs" => 200, "endpoint" => "$request" , "content" => ["requested" => $api_requests[2]]];
    print(json_encode($response));
    break;

  default:
    print("404");
    break;
}

?>
