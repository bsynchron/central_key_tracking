<head>
  <script>
    function login(name, pass){
      var data = new FormData();
      data.append("user", btoa(name));
      data.append("pass", btoa(pass));


      const http = new XMLHttpRequest();
        let server_ip = '<?php print $_SERVER["SERVER_NAME"]; ?>';
  	    let port = 8080;
  	    let uri = server_ip + ':' + port + '/api/auth/login';
        let token = "x";

      http.open("POST", 'http://' + uri + '?token=' + token, true);
      http.onload = function () {
        // do something to response
        //console.log(this.responseText);
        let response = JSON.parse(this.responseText);
        console.log(response.auth);
        if(response.auth == true){
          window.location.replace("<?php print($_SESSION['wanted']); ?>");
        }
      };
      http.send(data);
    }
  </script>
  <style>
    body {font-family: sans-serif;}
  </style>
  <link rel="stylesheet" type="text/css" href="/src/css/login.css">
</head>

<div class="center">
  <p>LOGIN</p>
  <form action="#" onsubmit="login(this.name.value, this.pass.value);">
    <input class="textfield" id="name" type="text" placeholder="Name"></input><br>
    <input class="textfield" id="pass" type="password" placeholder="Password"></input>
    <br><br><input type="submit" value="Login"></input>
  </form>
  <a href="/register">Register Pages</a>
</div>
<?php






?>
