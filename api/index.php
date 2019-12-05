<?php
function array_push_assoc($array, $key, $value){
  $array[$key] = $value;
  return $array;
}

$root = $_SERVER['DOCUMENT_ROOT'];
include("$root/controllers/SQLController.php");

$request = substr($_SERVER['REQUEST_URI'], 4);
if(substr($request, 0, 1) != "/"){
  $request = "/$request";
}

$api_requests = explode("/", $request);
file_put_contents("php://stdout", json_encode($api_requests)."\n");

$response=["rs" => 200, "endpoint" => "$request"];

switch ($api_requests[1]) {
  case 'test':
    $response=array_push_assoc($response, "content", ["test" => true]);
    break;

  case 'status':
    //return status
    $response=array_push_assoc($response, "content", ["requested" => $api_requests[2]]);
    break;

  case 'sql':
    if($api_requests[2] == "query"){
      if(isset($api_requests[3])){
        //do query
        $response=array_push_assoc($response, "content", $sc->query("SELECT * FROM $api_requests[3]"));
      } else {
        $sc->debug("TABLE NOT GIVEN");
        $response['rc'] = 400;
        $response['error'] = 'Table not given';
        http_response_code(400);
      }
    } elseif($api_requests[2] == "insert"){
      if(isset($api_requests[3])){
        if(isset($_POST['fields']) and isset($_POST['values'])){
          $fields = $_POST['fields'];
          $values = $_POST['values'];
          $sql_result = $sc->query("INSERT INTO $api_requests[3] ($fields) VALUES ($values)");
          $response = array_push_assoc($response, "content", $sql_result);
        } else {
          //no post data
          $response['rc'] = 400;
          $response['error'] = 'No data given';
          http_response_code(400);
        }
      } else {
        $sc->debug("TABLE NOT GIVEN");
        $response['rc'] = 400;
        $response['error'] = 'Table not given';
        http_response_code(400);
      }
    } else {
      $sc->debug("ILLIGAL METHOD");
      $response['rc'] = 400;
      $response['error'] = 'Method not avaliable';
      http_response_code(400);
    }
    break;

  default:
    print("404");
    break;
}

print(json_encode($response));

?>
