<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Project Hanzai :: Police Reports</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
	<?php echo Asset::css('bootstrap.min.css'); ?>
	<?php echo Asset::css('bootstrap-glyphicons.css'); ?>
	<?php echo Asset::css('jumbotron.css'); ?>
	<?php echo Asset::css('hanzai.css'); ?>
	<style>
		#map_canvas img{max-width: inherit;}
	</style>

	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBUfqZOv6eQPzxy55sIrOt_emW-llv3AKY&sensor=false"></script>
</head>
<body>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<a class="navbar-brand" href="#">Project Hanzai</a>
		</div>
	</div>

	<div class="container">
		<div class="row">
			<h2>Police Incident Reports</h2>
		</div>
		
		<div id="map_canvas" style="height:400px; width:100%;"></div>
		
		<br>
		<div class="row">
			<?php echo Pagination::instance('mypagination')->render(); ?>
		</div>
		<br>
		<div class="row">
			<?php foreach($result as $row): ?>
			<div class="col-lg-4">
				<h4><?php echo Inflector::humanize(strtolower($row['summarized_offense_description'])); ?></h4>
				<p>
					<span class="label label-info">Reported <?php echo Date::forge($row['date_reported'])->format("%B %d, %Y %H:%M"); ?></span>
				</p>
				<p><b>Offense Code:</b> <?php echo $row['offense_code']; ?></p>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="row">
			<?php echo Pagination::instance('mypagination')->render(); ?>
		</div>
		<hr/>
		<footer>
			<p class="pull-right">Page rendered in {exec_time}s using {mem_usage}mb of memory.</p>
			<p>
				<a href="http://fuelphp.com">FuelPHP</a> is released under the MIT license.<br>
				<small>Version: <?php echo Fuel::VERSION; ?></small>
			</p>
		</footer>
	</div>
</body>
</html>

<!-- JavaScript plugins (requires jQuery) -->
<script src="http://code.jquery.com/jquery.js"></script>

<!-- Include all compiled plugins (below), or include individual files as needed -->
<?php echo Asset::js('bootstrap.min.js'); ?>

<script type="text/javascript">
function initialize() {
	var mapStyle = [{
		"stylers": [
			{ "visibility": "on" },
			{ "hue": "#0091ff" },
			{ "invert_lightness": true },
			{ "lightness": -22 }
		]
	}];
	var mapOptions = {
		center: new google.maps.LatLng(47.6097, -122.3331),
		zoom: 12,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		styles: mapStyle
	};
	var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

	var markers = <?php echo json_encode($markers); ?>;

	createMarkers(map, markers);
	infowindow = new google.maps.InfoWindow({
		content: "loading..."
	});
}

function createMarkers(map, markers) {
	for (i = 0; i < markers.length; i++) {
		var location = markers[i];
		
		var locationLatLng = new google.maps.LatLng(location[0], location[1]);
		var marker = new google.maps.Marker({
			position: locationLatLng,
			map: map,
			title: location[2],
			icon: '<?php echo Asset::get_file("map_marker.png", "img"); ?>'
		});

		var contentString = "<h4>" + location[2] + "</h4>";

		google.maps.event.addListener(marker, "click", function() {
			infowindow.setContent(contentString);
			infowindow.open(map, this);
		});
	}
}

google.maps.event.addDomListener(window, "load", initialize);
</script>