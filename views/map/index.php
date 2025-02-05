<?php
$root = $_SERVER['DOCUMENT_ROOT'];
include("$root/controllers/SQLController.php");
?>

<head>
  <link rel="stylesheet" href="/src/js/style.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
  integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
  crossorigin=""/>
</head>
<body>
	<div id="map"></div>
	<div id="menu">
		<div id="menuCollapser" onclick="showMenu()"><<</div>
			<div id="menuOptions">
				<ul>
					<li><a onclick="openAllKeys()">All Keys</a></li>
					<li><a onclick="openAssignKey()">Assign key</a></li>
					<li><a onclick="openSearchBox()">Search Key</a></li>
          <li><a href="/logout">Logout</a></li>
				</ul>
				<div id="actionBox">
					<div id="allKeysBox">
						<?php
							echo '<table id="keyListing">';
							$keys = $sc->query("SELECT * FROM track_keys;");
							foreach($keys as $key) {
								if($key['holder'] == $_SESSION['user'] || $_SESSION['user'] == 'admin') {
                  echo '<tr>';
                  echo "<td>".$key['keyName']."</td>";
                  echo "<td><button style='font-weight: bold;' value='".$key['keyName']."' onclick='removeKey(this.value)'>X</button></td>";
                  echo "<td><button value='".$key['keyName']."' onclick=\"notifyKey('".$key['keyName']."')\">&#9200;</button></td>";
                  echo "</tr>";
                  //echo '<li>'.$key['keyName'].'<button>Remove</button><button>Change</button>';
								}
							}

							echo '</ul><input id="addKeyField" placeholder="key name"></input><button style="font-weight: bold;" onclick=\'addKey(document.getElementById("addKeyField").value);\'>+</button>';
              echo "</table>";
						?>
					</div>
					<div id="assignKeyBox">
            <table>
              <tr>
                <td>Key Name:</td>
                <td><input id="assignKeyName"></td>
              </tr>
              <tr>
                <td>User Name:</td>
                <td><input id="assignUserName"></td>
              </tr>
              <tr>
                <td><button onclick="assignKey(document.getElementById('assignKeyName').value, document.getElementById('assignUserName').value);">&#128273</button></td>
              </tr>
            </table>
					</div>
				</div>
			</div>
	</div>
	<div id="searchBox">
		<input id="searchQuerry" oninput="search(this.value)">
		<button onclick="closeSearchBox()">&#x2715</button>
		<span id="searchErrorMsg">No key matching the querry.</span>
	</div>
</body>
<script>
	const pageHeight = document.documentElement.clientHeight;

	const menu = document.getElementById('menuOptions');
	const collapser = document.getElementById('menuCollapser');


	const searchBox = document.getElementById('searchBox');
	var markersInSearch = [];

	const allKeysBox = document.getElementById('allKeysBox');
	const assignKeyBox = document.getElementById('assignKeyBox');

	document.getElementById('map').style.height = pageHeight + 'px';
	menu.style.height = pageHeight + 'px';

	function hideMenu() {
		menu.style.display = 'none';
		collapser.setAttribute('onclick', 'showMenu()');
		collapser.innerHTML = '<<';

	}

	function showMenu() {
		menu.style.display = 'block';
		collapser.setAttribute('onclick', 'hideMenu()');
		collapser.innerHTML = '>>';
	}

	function openAllKeys() {
		assignKeyBox.style.display = 'none';
		allKeysBox.style.display = 'block';
	}

	function openAssignKey() {
		assignKeyBox.style.display = 'block';
		allKeysBox.style.display = 'none';
	}

	function openSearchBox() {
		searchBox.style.display = 'block';
    	document.getElementById("searchQuerry").focus();
	}

	function closeSearchBox() {
		searchBox.style.display = 'none';
	}

	function search(val) {
		markersInSearch = []
		markers.forEach((marker) => {
			if(marker.getPopup().getContent().includes(val) && val != "") {
				marker.setStyle({color: 'green'})
				markersInSearch.push(marker.getPopup().getContent());
			} else {
				marker.setStyle({color: 'red'})
			}
		})
		let searchErrorMsg = document.getElementById('searchErrorMsg');
		if(!markersInSearch.length) {
			document.getElementById('searchErrorMsg').style.display = 'block';
		} else {
			document.getElementById('searchErrorMsg').style.display = 'none';
		}
	}

	function isInSearch(name) {
		inSearch = false;
		markersInSearch.forEach((marker) => {
			if(marker == name){
				inSearch = true;
			}
		})
		return inSearch;
	}
  function removeKey(name){
    const http = new XMLHttpRequest();
    	let server_ip = '<?php print $_SERVER["SERVER_NAME"]; ?>';
    	let port = 8080;
    	let uri = server_ip + ':' + port + '/api/keys/remove/'+ name;
    	//console.log(uri);

  	http.open("GET", 'http://' + uri + '?token=x');
  	http.send();

    var table = document.getElementById("keyListing");

    for(var i = 0; i < table.rows.length; i++){
      if(table.rows[i].cells[0].innerHTML == name){
        table.deleteRow(i);
      }
    }
  }

  function addKey(name){
    if(name == ""){
      return false;
    }

    document.getElementById("addKeyField").value = "";

    const http = new XMLHttpRequest();
    	let server_ip = '<?php print $_SERVER["SERVER_NAME"]; ?>';
    	let port = 8080;
    	let uri = server_ip + ':' + port + '/api/keys/add/'+ name;
    	//console.log(uri);

    view_pos = map.getCenter();
    view_pos = view_pos.lat + ", " + view_pos.lng;

    var data = new FormData();
    data.append('pos', view_pos);

  	http.open("POST", 'http://' + uri + '?token=x');
  	http.send(data);

    var table = document.getElementById("keyListing");
    var row = table.insertRow(-1);
    var cell_kn = row.insertCell(0);
    var cell_rm = row.insertCell(1);
    var cell_ch = row.insertCell(2);

    cell_kn.innerHTML = name;
    cell_rm.innerHTML = "<button style='font-weight: bold;' value='" + name + "' onclick='removeKey(this.value)'>X</button>";
    cell_ch.innerHTML = "<button value='" + name + "' onclick='notifyKey(this.value)'>⏰</button>";
  }

  function notifyKey(name){
    const http = new XMLHttpRequest();
    	let server_ip = '<?php print $_SERVER["SERVER_NAME"]; ?>';
    	let port = 8080;
    	let uri = server_ip + ':' + port + '/api/notify/'+ name;
    	//console.log(uri);

  	http.open("GET", 'http://' + uri + '?token=x');
  	http.send();
  }

  function assignKey(key, user){
    const http = new XMLHttpRequest();
      let server_ip = '<?php print $_SERVER["SERVER_NAME"]; ?>';
      let port = 8080;
      let uri = server_ip + ':' + port + '/api/keys/lend/'+ key + "/" + user;
      //console.log(uri);

    http.open("GET", 'http://' + uri + '?token=x');
    http.send();

    document.getElementById("assignKeyName").value = "";
    document.getElementById("assignUserName").value = "";
  }
</script>
<script src="/src/js/leaflet/leaflet.js"></script>
<script>
	var map = L.map('map').setView([53.4996733, 10.0028465], 17.69);
	var markers = [];

	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(map);
  <?php
    $keys = $sc->query("SELECT * FROM track_keys;");
    foreach($keys as $key){
      $latlong = explode(",", $key['lastPos']);
      $lat = $latlong[0];
      $long = $latlong[1];
      print("markers.push(
        L.circle([$lat, $long], {radius: 10, color: 'red'})
        .addTo(map)
        .bindPopup('".$key['keyName']."'));");
    }
  ?>

	const server_ip = '<?php print $_SERVER["SERVER_NAME"]; ?>';
	const port = 8080;

  setInterval(() => {
	const http = new XMLHttpRequest();
  	let uri = server_ip + ':' + port + '/api/sql/query/track_keys';
  	//console.log(uri);

	http.open("GET", 'http://' + uri + '?token=x');
	http.send();

	http.onreadystatechange=(e)=>{
	// remove all markers
		markers.forEach((marker) => {
			marker.remove();
		})

		markers = [];

    if (http.status != 200) {
        console.log("ERROR: "+http.status+" / "+http.readyState);
    }
    if(http.status == 200 && http.readyState == 4){
      // add new markers
  		console.log(http.responseText);
  		let keys = JSON.parse(http.responseText);
  		keys.content.forEach((key) => {
        holder = key.holder;
  			latlong = key.lastPos.split(',');
  			lat = latlong[0];
  			long = latlong[1];

  			let color = 'red';
  			if(key.triggered == 1) {
  				color = 'blue';
  			}
			if(isInSearch(key.keyName)){
				color = 'green';
			}


        if(holder == "<?php print($_SESSION['user']); ?>" || "<?php print($_SESSION['role']); ?>" == "admin"){
          markers.push(
            L.circle([lat, long], {radius: 10, color: color})
            .addTo(map)
            .bindPopup(key.keyName)
          )
        }
  		})
    }

	}
}, 1900);
	// markers.push(
	// 	L.circle([53.4996733, 10.0028465], {radius: 10, color: 'red'})
	// 		.addTo(map)
	// 		.bindPopup('Key 1')
	// );
  //
	// markers.push(
	// 	L.circle([53.5000233, 10.0029665], {radius: 10, color: 'red'})
	// 		.addTo(map)
	// 		.bindPopup('Key 2')
	// );

</script>
