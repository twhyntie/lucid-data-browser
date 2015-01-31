<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$folder = $_GET['datafile'];
$frame = $_GET['frame'];
$channel = $_GET['channel'];
$radius = $_GET['radius'];
$diameter = $radius * 2;
$centroid_x = $_GET['centroid-x'];
$centroid_y = $_GET['centroid-y'];

$imagePath = "data/" . $folder . "/frame" . $frame . "/c" . $channel . ".png";

$image = new Imagick($imagePath);

$image->cropImage($diameter, $diameter, $centroid_y - $radius, $centroid_x - $radius); // Crop image to cluster proportions given
$image->sampleImage(512, 512);

header('Content-Type: image/'.$image->getImageFormat());
echo $image;

?>