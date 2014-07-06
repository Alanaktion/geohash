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
	$dji = round($json->CompositeTrading->Open->Value, 2);
}

?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Geohashing</title>
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootswatch/3.2.0/flatly/bootstrap.min.css" media="all">
</head>
<body>

	<div class="container">
		<br>
		<nav class="navbar navbar-default">
			<div class="navbar-header">
				<a class="navbar-brand" href="#">Geohashing</a>
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
									<button class="btn btn-primary" type="submit">Go for it</button>
								</div>
							</div>
						</fieldset>
					</form>
				</div>
			</div>
			<div class="col-md-6">
				<h3 class="page-title">Result</h3>
				<p><b>Graticule:</b> <span id="result-graticule"></span></p>
				<p><b>MD5:</b> <span id="result-md5"></span></p>
				<p><b>Coordinates:</b> <span id="result-coords"></span></p>
				<br>
				<div id="map" style="height: 400px;"></div>
			</div>
		</div>
	</div>

	<script src="https://maps.googleapis.com/maps/api/js?v=3"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script>!function(a){"use strict";function b(a,b){var c=(65535&a)+(65535&b),d=(a>>16)+(b>>16)+(c>>16);return d<<16|65535&c}function c(a,b){return a<<b|a>>>32-b}function d(a,d,e,f,g,h){return b(c(b(b(d,a),b(f,h)),g),e)}function e(a,b,c,e,f,g,h){return d(b&c|~b&e,a,b,f,g,h)}function f(a,b,c,e,f,g,h){return d(b&e|c&~e,a,b,f,g,h)}function g(a,b,c,e,f,g,h){return d(b^c^e,a,b,f,g,h)}function h(a,b,c,e,f,g,h){return d(c^(b|~e),a,b,f,g,h)}function i(a,c){a[c>>5]|=128<<c%32,a[(c+64>>>9<<4)+14]=c;var d,i,j,k,l,m=1732584193,n=-271733879,o=-1732584194,p=271733878;for(d=0;d<a.length;d+=16)i=m,j=n,k=o,l=p,m=e(m,n,o,p,a[d],7,-680876936),p=e(p,m,n,o,a[d+1],12,-389564586),o=e(o,p,m,n,a[d+2],17,606105819),n=e(n,o,p,m,a[d+3],22,-1044525330),m=e(m,n,o,p,a[d+4],7,-176418897),p=e(p,m,n,o,a[d+5],12,1200080426),o=e(o,p,m,n,a[d+6],17,-1473231341),n=e(n,o,p,m,a[d+7],22,-45705983),m=e(m,n,o,p,a[d+8],7,1770035416),p=e(p,m,n,o,a[d+9],12,-1958414417),o=e(o,p,m,n,a[d+10],17,-42063),n=e(n,o,p,m,a[d+11],22,-1990404162),m=e(m,n,o,p,a[d+12],7,1804603682),p=e(p,m,n,o,a[d+13],12,-40341101),o=e(o,p,m,n,a[d+14],17,-1502002290),n=e(n,o,p,m,a[d+15],22,1236535329),m=f(m,n,o,p,a[d+1],5,-165796510),p=f(p,m,n,o,a[d+6],9,-1069501632),o=f(o,p,m,n,a[d+11],14,643717713),n=f(n,o,p,m,a[d],20,-373897302),m=f(m,n,o,p,a[d+5],5,-701558691),p=f(p,m,n,o,a[d+10],9,38016083),o=f(o,p,m,n,a[d+15],14,-660478335),n=f(n,o,p,m,a[d+4],20,-405537848),m=f(m,n,o,p,a[d+9],5,568446438),p=f(p,m,n,o,a[d+14],9,-1019803690),o=f(o,p,m,n,a[d+3],14,-187363961),n=f(n,o,p,m,a[d+8],20,1163531501),m=f(m,n,o,p,a[d+13],5,-1444681467),p=f(p,m,n,o,a[d+2],9,-51403784),o=f(o,p,m,n,a[d+7],14,1735328473),n=f(n,o,p,m,a[d+12],20,-1926607734),m=g(m,n,o,p,a[d+5],4,-378558),p=g(p,m,n,o,a[d+8],11,-2022574463),o=g(o,p,m,n,a[d+11],16,1839030562),n=g(n,o,p,m,a[d+14],23,-35309556),m=g(m,n,o,p,a[d+1],4,-1530992060),p=g(p,m,n,o,a[d+4],11,1272893353),o=g(o,p,m,n,a[d+7],16,-155497632),n=g(n,o,p,m,a[d+10],23,-1094730640),m=g(m,n,o,p,a[d+13],4,681279174),p=g(p,m,n,o,a[d],11,-358537222),o=g(o,p,m,n,a[d+3],16,-722521979),n=g(n,o,p,m,a[d+6],23,76029189),m=g(m,n,o,p,a[d+9],4,-640364487),p=g(p,m,n,o,a[d+12],11,-421815835),o=g(o,p,m,n,a[d+15],16,530742520),n=g(n,o,p,m,a[d+2],23,-995338651),m=h(m,n,o,p,a[d],6,-198630844),p=h(p,m,n,o,a[d+7],10,1126891415),o=h(o,p,m,n,a[d+14],15,-1416354905),n=h(n,o,p,m,a[d+5],21,-57434055),m=h(m,n,o,p,a[d+12],6,1700485571),p=h(p,m,n,o,a[d+3],10,-1894986606),o=h(o,p,m,n,a[d+10],15,-1051523),n=h(n,o,p,m,a[d+1],21,-2054922799),m=h(m,n,o,p,a[d+8],6,1873313359),p=h(p,m,n,o,a[d+15],10,-30611744),o=h(o,p,m,n,a[d+6],15,-1560198380),n=h(n,o,p,m,a[d+13],21,1309151649),m=h(m,n,o,p,a[d+4],6,-145523070),p=h(p,m,n,o,a[d+11],10,-1120210379),o=h(o,p,m,n,a[d+2],15,718787259),n=h(n,o,p,m,a[d+9],21,-343485551),m=b(m,i),n=b(n,j),o=b(o,k),p=b(p,l);return[m,n,o,p]}function j(a){var b,c="";for(b=0;b<32*a.length;b+=8)c+=String.fromCharCode(a[b>>5]>>>b%32&255);return c}function k(a){var b,c=[];for(c[(a.length>>2)-1]=void 0,b=0;b<c.length;b+=1)c[b]=0;for(b=0;b<8*a.length;b+=8)c[b>>5]|=(255&a.charCodeAt(b/8))<<b%32;return c}function l(a){return j(i(k(a),8*a.length))}function m(a,b){var c,d,e=k(a),f=[],g=[];for(f[15]=g[15]=void 0,e.length>16&&(e=i(e,8*a.length)),c=0;16>c;c+=1)f[c]=909522486^e[c],g[c]=1549556828^e[c];return d=i(f.concat(k(b)),512+8*b.length),j(i(g.concat(d),640))}function n(a){var b,c,d="0123456789abcdef",e="";for(c=0;c<a.length;c+=1)b=a.charCodeAt(c),e+=d.charAt(b>>>4&15)+d.charAt(15&b);return e}function o(a){return unescape(encodeURIComponent(a))}function p(a){return l(o(a))}function q(a){return n(p(a))}function r(a,b){return m(o(a),o(b))}function s(a,b){return n(r(a,b))}function t(a,b,c){return b?c?r(b,a):s(b,a):c?p(a):q(a)}"function"==typeof define&&define.amd?define(function(){return t}):a.md5=t}(this);</script>
	<script>
		var geocoder = new google.maps.Geocoder(),
			graticule = {},
			resulthash = "",
			coords = {};
		$("#setup").submit(function(e) {
			geocoder.geocode({address: $("#location").val()}, function(results_array, status) {

				if(status != "OK") {
					alert("Error getting graticule. Verify location and try again.");
					console.log(status);
					return;
				}

				// Update graticule
				graticule.lat = Math.floor(results_array[0].geometry.location.lat());
				graticule.lng = Math.floor(results_array[0].geometry.location.lng());
				$("#result-graticule").text(graticule.lat + ", " + graticule.lng);

				// Generate MD5
				resulthash = window.md5($("#date") + "-" + $("#dji"));
				$("#result-md5").text(resulthash);

				// Split MD5
				var hashparts = {};
				hashparts.lat = resulthash.substr(0, resulthash.length / 2);
				hashparts.lng = resulthash.substr(resulthash.length / 2);

				// Convert to decimal and combine with graticule
				coords.lat = parseFloat(graticule.lat + "." + parseInt(hashparts.lat, 16));
				coords.lng = parseFloat(graticule.lng + "." + parseInt(hashparts.lng, 16));
				$("#result-coords").text(Math.round(coords.lat * 1000000) / 1000000 + ", " + Math.round(coords.lng * 1000000) / 1000000);

				// Show map
				var center = new google.maps.LatLng(coords.lat, coords.lng),
					map = new google.maps.Map(document.getElementById("map"), {zoom: 11, center: center}),
					marker = new google.maps.Marker({
						position: center,
						map: map,
						title: $("#result-coords").text()
					});

			});
			e.preventDefault();
			return false;
		});
	</script>

</body>
</html>
