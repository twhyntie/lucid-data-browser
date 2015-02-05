<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include("evil.php");

$DEFAULT_FILE = "2015-01-30 01.14";

$id = $_GET['id'];
if (!isset($id)) {
	$id = $DEFAULT_FILE; //default file
}

$frame = $_GET['frame'];
if (!isset($frame)) {
	$frame = '1'; //start at first frame
}

$files = preg_grep('/^([^.])/',scandir("data"));

if (!in_array($id, $files)) {
	die(file_get_contents("404.html"));
}

$metadata = file_get_contents("data/{$id}/metadata");
$metadata = explode("\n", $metadata);
$num_frames = $metadata[0];
$num_frames = explode(" ", $num_frames);
$num_frames = $num_frames[0];

?>
<!DOCTYPE html>

<title>LUCID Data Browser</title>

<script src="//use.edgefonts.net/open-sans:n3,n4,i4.js"></script>
<script src="js/moment.js"></script>
<link rel = "stylesheet" href = "css/main.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src = "js/main.js"></script>

<div id = "container">
	<div id = "sidebar">
		<div id = "title-container">
			<div id = "title">LUCID Data Browser</div>
		</div>
		<div id = "menu-button" onclick = "$('#mask, #menu').fadeIn();"><img src = "img/menu.svg"></div>
		<div id = "details">
			Frame timestamp: <span id = "timestamp"></span><br>
			Latitude: <span id = "lat"></span>&deg;<br>
			Longitude: <span id = "lng"></span>&deg;
		</div>
		<div id = "tracking-button" onclick = "window.location = 'tracking.php';">Track LUCID</div>
		<div id = "map">
			<img id = "lucid-icon" src = "img/lucid.jpg">
		</div>
	</div>
	<div id = "data">
		<div class = "frame" id = "tpx0"><img/><div class = "clusters"></div></div>
		<div class = "frame" id = "tpx1"><img/><div class = "clusters"></div></div>
		<div class = "frame" id = "tpx3"><img/><div class = "clusters"></div></div>
		<div class = "label">TPX0</div>
		<div class = "label">TPX1</div>
		<div class = "label">TPX3</div>
	</div>
	<div id = "frame-indicator">Frame <span id = "current-frame"></span> of <?php print $num_frames; ?></div>

	<div id = "back-button" class = "button" onclick = "loadFrame(currentFrame - 1);"><img src = "img/back.svg"></div>
	<div id = "forward-button" class = "button" onclick = "loadFrame(currentFrame + 1);"><img src = "img/forward.svg"></div>
	<div id = "clustering-checkbox" onclick = "enableClustering();">
		Enable Clustering (Experimental)
	</div>
</div>

<div id = "mask" onclick = "$('#mask, #menu, #cluster-viewer').fadeOut();"></div>
<div id = "menu">
	<div id = "menu-title">Select a LUCID file</div>
	<img src = "img/close.svg" id = "menu-close" onclick = "$('#mask, #menu').fadeOut();">
	<ul>
		<?php 
		foreach ($files as $file) {
			if ($file == $id) print "<b>";
			$cfile = str_replace(".", ":", $file);
			print "<li onclick = \"loadFile('{$file}');\">{$cfile}</li>";
			if ($file == $id) print "</b>";
		}
		?>
	</ul>
</div>

<div id = "cluster-viewer">
	<img id = "cluster-img">
</div>

<script>

clustering = false;

metadata = "";
<?php

for ($i = 1; $i <= $num_frames; $i++) {
	print "metadata += \"{$metadata[$i]},\";\n";
}

?>
metadata = metadata.split(",");

currentFrame = 0;

function loadFrame(id) {
	if (id > 0 && id <= <?php print $num_frames; ?>) {
		currentFrame = id;
		$("#tpx0 img").attr("src", "data/<?php print $id; ?>/frame" + id + "/c0.png");
		$("#tpx1 img").attr("src", "data/<?php print $id; ?>/frame" + id + "/c1.png");
		$("#tpx3 img").attr("src", "data/<?php print $id; ?>/frame" + id + "/c3.png");

		var meta = metadata[id - 1];
		var metafields = meta.split(" ");
		var timestamp = moment(metafields[1], "X").format("DD/MM/YYYY HH:mm.ss");
		var lat = Math.round(metafields[2] * 100) / 100; // hack for 2dp
		var lng = Math.round(metafields[3] * 100) / 100;

		//Place map marker in correct position
		$("#lucid-icon").css({"top": 90 - Math.round(lat), "left": Math.round(lng) + 180});
		// Update fields
		$("#timestamp").text(timestamp);
		$("#lat").text(lat);
		$("#lng").text(lng);

		$("#current-frame").text(id);

		//Clear cluster circles
		$(".clusters").empty();

		// Get cluster files for each frame
		$.get("data/<?php print $id; ?>/frame" + id + "/c0.clusters", function(clusters_0) {
		$.get("data/<?php print $id; ?>/frame" + id + "/c1.clusters", function(clusters_1) {
		$.get("data/<?php print $id; ?>/frame" + id + "/c3.clusters", function(clusters_3) {
			var clusters = [clusters_0, clusters_1, clusters_3];
			var chip = [0, 1, 3];
			$(clusters).each(function(index, clusterfile) {
				clusterfile = clusterfile.split("\n");
				clusterfile.pop(); //Last element will be blank
				$(clusterfile).each(function(index2, cluster) {
					//find individual parameters
					cluster = cluster.split(" ");
					$("#tpx" + chip[index] + " .clusters").append(" <div class = 'clustercircle' data-centroid-x='" + cluster[1] + "' data-centroid-y='" + cluster[0] + "' data-radius='" + cluster[2] + "' data-cluster-id = '" + (index2 + 1) + "' data-channel = '" + chip[index] + "'> ");
				})
			});	
			clusterCircles();

		}); }); });

		//Push frame id to history API
		window.history.replaceState( {} , 'foo', '?id=<?php print $id; ?>&frame=' + id);
	}
}

function clusterCircles() {
	if (clustering) {
		$(".clustercircle").each(function(index, value) {
			var centroid_x = $(this).attr("data-centroid-x");
			var centroid_y = $(this).attr("data-centroid-y");
			var radius = $(this).attr("data-radius");
			var clusterId = $(this).attr("data-cluster-id");
			var channel = $(this).attr("data-channel");
			$(this).css({
				"width": radius * 2 + "px",
				"height": radius * 2 + "px",
				"top": centroid_x + "px",
				"left": centroid_y + "px",
				"margin-top": radius * -1 + "px",	
				"margin-left": radius * -1 + "px"
			});
			$(this).click(function() {
	    		viewCluster("viewcluster.php?datafile=<?php print $id; ?>&channel=" + channel + "&frame=" + currentFrame + "&centroid-x=" + centroid_x + "&centroid-y=" + centroid_y + "&radius=" + radius);
	    	})
		});
	}
}

function viewCluster(url) {
	$("#cluster-img").attr("src", ""); //hide last image so it is not displayed over a slow connection
	$("#cluster-img").attr("src", url);
	$("#mask").fadeIn();
	$("#cluster-viewer").fadeIn();
}

function enableClustering() {
	clustering = true;
	$(".clusters").show();
	$("#clustering-checkbox").fadeOut("fast");
	clusterCircles();
}

clusterCircles();

loadFrame(<?php print $frame; ?>);

$(document).keydown(function(e) {
    switch(e.which) {
        case 37: // left
        	loadFrame(currentFrame - 1);
        break;

        case 39: // right
        	loadFrame(currentFrame + 1);
        break;

        default: return;
    }
    e.preventDefault();
});

function showMenu() {
	$("#mask").fadeIn();
	$("#menu").fadeIn();
}

function loadFile(file) {
	window.location = "./?id=" + file;
}

</script>