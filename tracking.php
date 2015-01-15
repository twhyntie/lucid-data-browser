<!DOCTYPE html>
<!-- API key AIzaSyDJlcv7E3JJl6ejN109-wFHm5E26HkCH_k -->
<html>
 <head>
    <script src="//use.edgefonts.net/open-sans:n3,n4,i4.js"></script>
    <style type="text/css">
      body, html {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map-canvas { 
        position: fixed;
        top: 0px;
        left: 380px;
        height: 100%;
        width: calc(100% - 380px);
      }
      #title-container {
        position: fixed;
        top: 0px;
        left: 0px;
        background-color: #eeeeee;
        width: 380px;
        height: 130px;  
      }

      #title {
        font-family: open-sans;
        font-weight: 300;
        font-size: 35px;
        position: absolute;
        top: 50px;
        left: 15px;
        color: #222222;
      }
      #sidebar {
        position: fixed;
        top: 0px;
        left: 0px;
        width: 380px;
        height: 100%;
        background-color: #ffffff;
        box-shadow: 3px 0px 5px 0 rgba(0,0,0,.5);
        z-index: 300;
      }
      #details {
        position: absolute;
        top: 160px;
        left: 30px;
        color: #555555;
        font-family: open-sans;
      }
      #menu-button {
        position: fixed;
        top: 105px;
        left: 320px;
        width: 50px;
        height: 50px;
        border-radius: 25px;
        background-color: #c62828;
        cursor: pointer;
        box-shadow: 0 2px 5px 0 rgba(0,0,0,.3)
      }

      #menu-button:hover {
        background-color: #b71c1c;
      }

      #menu-button img {
        width: 30px;
        height: 30px;
        margin: 10px;
      }
    </style>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDJlcv7E3JJl6ejN109-wFHm5E26HkCH_k"></script>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script type="text/javascript">
      var lucidMarker;
      var map;

      function initialize() {
        var mapOptions = {
          center: { lat: 0, lng: 0},
          zoom: 2,
          disableDefaultUI: true
        };
        map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);

        var image = {
    		  url: "img/lucidthumb.png",
    		  size: new google.maps.Size(40, 40),
    		  origin: new google.maps.Point(0, 0),
  		  anchor: new google.maps.Point(20, 20),
    		};
    		lucidMarker = new google.maps.Marker({
    		    map: map,
    		    icon: image,
    		    anchor: new google.maps.Point(20, 20)
    		});

      }

      function setPosition(lat, lng) {
        console.log(lat)
        console.log(lng)
     		newLatLng = new google.maps.LatLng(lat, lng);
     		lucidMarker.setPosition(newLatLng);
        map.setCenter(newLatLng);
      }
    </script>
  </head>
  <body>
	<div id="map-canvas"></div>
  <div id="sidebar">
    <div id = "title-container">
      <div id = "title">LUCID Tracking</div>
    </div>
    <div id = "menu-button" onclick = "window.location = './';"><img src = "img/back.svg"></div>
    <div id = "details">
      Latitude: <span id = "lat"></span>&deg;<br>
      Longitude: <span id = "lng"></span>&deg;
    </div>
  </div>
  </body>

  <script>
  function grabData() {
    console.log("Grabbing coordinates")
    $.get("tracking/coordinates.php", function(data) {
      data = data.split(",")
      data[0] = Math.round(data[0] * 100) / 100
      data[1] = Math.round(data[1] * 100) / 100
      setPosition(data[0], data[1])
      $("#lat").text(data[0]);
      $("#lng").text(data[1]);
      setTimeout('grabData()', 3000); // schedule for 3 secs...
    })
  }
  $(document).ready(function() {
    initialize();
    grabData();
  })
  </script>
</html>