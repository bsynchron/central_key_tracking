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
          window.location.replace("/");
        }
      };
      http.send(data);
    }
  </script>
</head>

<p>LOGIN</p>


<form action="#" onsubmit="login(this.name.value, this.pass.value);">
  <input id="name" type="text" placeholder="Name"></input>
  <input id="pass" type="password" placeholder="Password"></input>
  <input type="submit" value="Login"></input>
</form>
<?php






?>
