<?php

if ($_GET['mode'] == "now") {
	$coordinates = exec("python coordinates.py");
	print $coordinates;
}
else if ($_GET['mode'] == "line") {
	$coordinates = exec("python orbitline.py");
	print $coordinates;
}
?>