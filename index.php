<!DOCTYPE html>

<title>LUCID Data Browser</title>

<script src="//use.edgefonts.net/open-sans:n3,n4,i4.js"></script>
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

		<div id = "map">
			<img id = "lucid-icon" src = "img/lucid.jpg">
		</div>
	</div>
	<div id = "data">
		<div class = "frame" id = "tpx0"><img/></div>
		<div class = "frame" id = "tpx1"><img/></div>
		<div class = "frame" id = "tpx3"><img/></div>
		<div class = "label">TPX0</div>
		<div class = "label">TPX1</div>
		<div class = "label">TPX3</div>
	</div>
	<div id = "frame-indicator">Frame <span id = "current-frame"></span> of 20</div>

	<div id = "back-button" class = "button" onclick = "loadFrame(currentFrame - 1);"><img src = "img/back.svg"></div>
	<div id = "forward-button" class = "button" onclick = "loadFrame(currentFrame + 1);"><img src = "img/forward.svg"></div>

</div>

<div id = "mask" onclick = "$('#mask, #menu').fadeOut();"></div>
<div id = "menu">
	<div id = "menu-title">Select a LUCID file</div>
	<img src = "img/close.svg" id = "menu-close" onclick = "$('#mask, #menu').fadeOut();">
	<ul>
		<li onclick = "loadFile('2014-12-20');">2014-12-20</li>
	</ul>
</div>

<script>

metadata = "";
<?php

$metadata = file_get_contents("data/2014-12-20/metadata");
$metadata = explode("\n", $metadata);
for ($i = 1; $i <= 20; $i++) {
	print "metadata += \"{$metadata[$i]},\";\n";
}

?>
metadata = metadata.split(",");

currentFrame = 0;

function loadFrame(id) {
	if (id > 0 && id < 21) {
		currentFrame = id;
		$("#tpx0 img").attr("src", "data/2014-12-20/frame" + id + "/c0.png");
		$("#tpx1 img").attr("src", "data/2014-12-20/frame" + id + "/c1.png");
		$("#tpx3 img").attr("src", "data/2014-12-20/frame" + id + "/c3.png");

		var meta = metadata[id - 1];
		var metafields = meta.split(" ");
		var timestamp = metafields[1];
		var lat = Math.round(metafields[2] * 100) / 100; // hack for 2dp
		var lng = Math.round(metafields[3] * 100) / 100;

		//Place map marker in correct position
		$("#lucid-icon").css({"top": 90 - Math.round(lat), "left": Math.round(lng) + 180});
		// Update fields
		$("#timestamp").text(timestamp);
		$("#lat").text(lat);
		$("#lng").text(lng);

		$("#current-frame").text(id);
	}
}

loadFrame(1);

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
	window.location = "index.php";
}

</script>