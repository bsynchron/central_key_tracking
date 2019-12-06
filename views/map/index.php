<?php
$root = $_SERVER['DOCUMENT_ROOT'];
include("$root/controllers/SQLController.php");
?>

<head>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
  integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
  crossorigin=""/>
  <style>
  	body { margin: 0}
  </style>
</head>
<body>
	<div id="map"></div>
	<div id="menu">
		<div id="menuCollapser" onclick="showMenu()"><<</div>
			<div id="menuOptions">
				<ul>
					<li>Heatmap</li>
					<li>All Keys</li>
					<li>All Users</li>
				</ul>
			</div>
	</div>
</body>
<script>
	const pageHeight = document.documentElement.clientHeight;

	const menu = document.getElementById('menuOptions');
	const collapser = document.getElementById('menuCollapser');

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
