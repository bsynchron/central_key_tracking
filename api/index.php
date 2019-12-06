<?php
function debug($txt){
  file_put_contents("php://stdout", "\033[1;31mAPI: $txt\033[0m\n");
}

function array_push_assoc($array, $key, $value){
  $array[$key] = $value;
  return $array;
}
$root = $_SERVER['DOCUMENT_ROOT'];
include("$root/controllers/SQLController.php");
include("$root/config/tokens.php");

$request = explode("?",substr($_SERVER['REQUEST_URI'], 4))[0];
if(substr($request, 0, 1) != "/"){
  $request = "/$request";
}

$args = explode("?", $request);
$api_requests = explode("/", $args[0]);
unset($args[0]);

//set every element to lower case
$api_requests = array_map('strtolower', $api_requests);

file_put_contents("php://stdout", json_encode($api_requests)."\n");


debug("ARGS: ".json_encode($args));
debug("REQUEST: ".json_encode($api_requests));
if(!isset($_GET['token'])){
  $response=["rc" => 401, "requested" => $api_requests[2], "error" => "No token"];
  http_response_code(401);
  print(json_encode($response));
  die();
}
debug("token = ".$_GET['token']." / token index = ".array_search($_GET['token'], $tokens));
if(!is_numeric(array_search($_GET['token'], $tokens)) or !isset($_GET['token'])){
  $response=["rc" => 401, "requested" => $api_requests[2], "error" => "Invalid token"];
  http_response_code(401);
  print(json_encode($response));
  die();
}

$response=["rc" => 200, "endpoint" => "$request"];

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
    } elseif($api_requests[2] == "update") {
      $missing_post = [];

      if(isset($_POST['idfield'])){
        $idfield = $_POST['idfield'];
      } else {
        array_push($missing_post, "idfield");
      }

      if(isset($_POST['identifier'])){
        $identifier = $_POST['identifier'];
      } else {
        array_push($missing_post, "identifier");
      }

      if(isset($_POST['newval'])){
        $newval = $_POST['newval'];
      } else {
        array_push($missing_post, "newval");
      }

      if(isset($_POST['field'])){
        $field = $_POST['field'];
      } else {
        array_push($missing_post, "field");
      }

      if($missing_post == []){
        $key = $identifier;
        $sql_result = $sc->query("UPDATE $api_requests[3] SET $field = '$newval' WHERE $idfield = $identifier;");
        $response = array_push_assoc($response, "content", $sql_result);
        if($sc->query("SELECT triggered FROM track_keys WHERE keyName = $key;")[0]['triggered'] == true){
          $response['triggered'] = true;
          $sc->query("UPDATE track_keys SET triggered = false WHERE keyName = $key;");
        } else {
          $response['triggered'] = false;
        }
      } else {
        $response['rc'] = 400;
        $response['error'] = "Missing POST data!";
        $response['error_detail'] = $missing_post;
      }

    } else {
      debug("ILLIGAL METHOD [$api_requests[2]]");
      $response['rc'] = 400;
      $response['error'] = 'Method not avaliable';
      http_response_code(400);
    }
    break;

  case 'notify':
    if(isset($api_requests[2]) and $api_requests[2] != ""){
      $key = $sc->query("SELECT keyName FROM track_keys WHERE keyName = '$api_requests[2]';");
      if($key == []){
        //key not found
        $response['rc'] = 401;
        $response['error'] = "No key found!";
        break;
      }
      //key found
      $sc->query("UPDATE track_keys SET triggered = true;");
      $response['content'] = true;
      debug("SET UP TRIGGER [".$key[0]['keyName']."]");
    } else {
      $response['rc'] = 401;
      $response['error'] = "No key given!";
      break;
    }
    break;

  default:
    $response['rc'] = 404;
    $response['error'] = "Not found";
    http_response_code(404);
    break;
}

print(json_encode($response));
?>
