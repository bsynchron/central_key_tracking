<?php

if(isset($_SESSION['user'])){
  session_destroy();
}


?>
<body onload="window.location.href = '/map';">
<a href="/map">Login</a>


</body>
