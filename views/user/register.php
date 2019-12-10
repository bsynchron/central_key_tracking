<head>
  <script>
    function register(name, pass){
      var data = new FormData();
      data.append("user", btoa(name));
      data.append("pass", btoa(pass));


      const http = new XMLHttpRequest();
        let server_ip = '<?php print $_SERVER["SERVER_NAME"]; ?>';
  	    let port = 8080;
  	    let uri = server_ip + ':' + port + '/api/auth/register';
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

<?php






?>
<h1>REGISTER</h1>
<form action="#" onsubmit="register(this.name.value, this.pass.value);">
  <input id='name' type="text" value="" placeholder="Name"></input>
  <input id='pass' type="passoword" value="" placeholder="Password"></input>
  <input type="submit"></input>
</form>
