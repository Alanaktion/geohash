<?php

// Get location from IP address
$ip = $_SERVER["REMOTE_ADDR"];
$coords = "";
if(!in_array($ip, array("::1", "127.0.0.1"))) {
	$result = @file_get_contents("http://freegeoip.net/json/" . $_SERVER["REMOTE_ADDR"]);
	if($result) {
		$json = json_decode($result);
		$coords = $json->latitude . ", " . $json->longitude;
	}
}

// Get DJI opening for today
$dji = "";
$result = @file_get_contents("http://betawebapi.dowjones.com/fintech/data/api/v1/quotes/dji");
if($result) {
	$json = json_decode($result);
	$dji = number_format($json->CompositeTrading->Open->Value, 2, ".", "");
}

?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Geohash</title>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootswatch/3.2.0/flatly/bootstrap.min.css" media="all">
</head>
<body>
	<div class="container">
		<br>
		<nav class="navbar navbar-default">
			<div class="navbar-header">
				<a class="navbar-brand" href="#">Geohash</a>
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="http://xkcd.com/426/" target="_blank">xkcd.com/426</a></li>
				</ul>
			</div>
		</nav>

		<div class="row">
			<div class="col-md-6">
				<div class="well">
					<form class="form-horizontal" id="setup">
						<fieldset>
							<legend>Setup</legend>
							<div class="form-group">
								<label class="col-lg-3 control-label">Location</label>
								<div class="col-lg-9">
									<input class="form-control" type="text" name="location" id="location" value="<?php echo $coords; ?>">
									<span class="help-block">Coordinates, zip codes, or most other address formats should work. We've given you an estimate of your coordinates above, but they may not be accurate.</span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 control-label">Date</label>
								<div class="col-lg-9">
									<input class="form-control" type="text" name="date" id="date" value="<?php echo date("Y-m-d"); ?>" placeholder="YYYY-MM-DD">
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-3 control-label"><abbr title="Dow Jones Index">^DJI</abbr> Open</label>
								<div class="col-lg-9">
									<input class="form-control" type="text" name="dji" id="dji" value="<?php echo $dji; ?>">
									<span class="help-block">For this to work correctly, this <abbr title="Dow Jones Index">DJI</abbr> open value must match the date entered above. We automatically fill these for the current date.</span>
								</div>
							</div>
							<div class="form-group">
								<div class="col-lg-9 col-lg-offset-3">
									<button class="btn btn-primary" type="submit"></button>
								</div>
							</div>
						</fieldset>
					</form>
				</div>
			</div>
			<div class="col-md-6">
				<h3 class="page-title">Result</h3>
				<p><b>Graticule:</b> <span id="result-graticule"></span></p>
				<p><b>Composite:</b> <span id="result-composite"></span></p>
				<p><b>MD5 Hash:</b> <span id="result-md5"></span></p>
				<p><b>Coordinates:</b> <span id="result-coords"></span></p>
				<br>
				<div id="map"></div>
			</div>
		</div>
	</div>

	<script src="https://maps.googleapis.com/maps/api/js?v=3"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="md5.min.js"></script>
	<script>
		var geocoder = new google.maps.Geocoder(),
			graticule = {},
			resulthash = "",
			coords = {};

		$("#setup").submit(function(e) {
			geocoder.geocode({address: $("#location").val()}, function(results_array, status) {

				// Check geocode response
				if(status != "OK") {
					alert("Error getting graticule. Verify location and try again.");
					console.log(status);
					return;
				}

				// Update graticule
				graticule.lat = ~~(results_array[0].geometry.location.lat());
				graticule.lng = ~~(results_array[0].geometry.location.lng());
				$("#result-graticule").text(graticule.lat + ", " + graticule.lng);

				// Combine date and DJI
				var result = $("#date").val() + "-" + $("#dji").val();
				$("#result-composite").text(result);

				// Generate MD5
				resulthash = md5(result);
				var hashparts = {};
				hashparts.lat = resulthash.substr(0, resulthash.length / 2);
				hashparts.lng = resulthash.substr(resulthash.length / 2);
				$("#result-md5").text(hashparts.lat + " " + hashparts.lng);

				// Convert to decimal and combine with graticule
				coords.lat = parseFloat(graticule.lat + "." + parseInt(hashparts.lat, 16));
				coords.lng = parseFloat(graticule.lng + "." + parseInt(hashparts.lng, 16));
				$("#result-coords").text(Math.round(coords.lat * 1000000) / 1000000 + ", " + Math.round(coords.lng * 1000000) / 1000000);

				// Show map
				$("#map").css("height", "400px");
				var center = new google.maps.LatLng(coords.lat, coords.lng),
					map = new google.maps.Map(document.getElementById("map"), {
						zoom: 11,
						center: center,
						mapTypeId: google.maps.MapTypeId.TERRAIN
					}),
					marker = new google.maps.Marker({
						position: center,
						map: map,
						title: $("#result-coords").text()
					});

			});
			e.preventDefault();
			return false;
		});

		// Run automatically
		$(document).ready(function(e) {
			if($("#location").val()) {
				$("#setup").submit();
			}
		});
	</script>
</body>
</html>
