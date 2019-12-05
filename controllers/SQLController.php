<?php

class mySQLController{

  function debug($txt){
    file_put_contents("php://stdout", "\033[1;35mMYSQL: $txt\033[0m\n");
  }

  function mySqlConnect(){
    include($_SERVER['DOCUMENT_ROOT']."/config/database.php");
    global $mysqli;
    //create connection to mysql
    $mysqli = new mysqli($database['host'], $database['user'], $database['password'], $database['database']);

    //check connection
    if ($mysqli->errno) {
      $this->debug("Connect failed: ".$mysqli->connect_error);
      return false;
    }
  }


  function query($txt){
    global $mysqli;
    $this->debug($txt);
    //establish connection to db
    $this->mySqlConnect();

    //do query
    if($query = $mysqli->query("$txt")){
      //$this->debug("ROWS: ".$query->num_rows);
      //successful query
      if(!is_bool($query)){
        $query = $query->fetch_all(MYSQLI_ASSOC);
      }
      $this->debug("RESULT: ".json_encode($query));
      //$query->close();
      //close connection to db
      $mysqli->close();
      return $query;
    } else {
      //failed query
      $this->debug($mysqli->error);
      //$query->close();
      //close connection to db
      $mysqli->close();
      return false;
    }
  }
}
$sc = new mySQLController;
?>
