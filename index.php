<?php

require 'config.php';

$sql = "SELECT * FROM tbl_gps WHERE 1";
$result = $db->query($sql);
if (!$result) {
  { echo "Error: " . $sql . "<br>" . $db->error; }
}

$row = $result->fetch_assoc();

?>
<html>
<head>
    <title>Live Location - Sat Nav</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background: black;
        color: white;
        padding: 0;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        flex-direction: column;
        text-align: center;
    }
    .title-container {
        text-align: center;
        width: 100%;
        margin-top: 20px;
    }
    .title-container h1 {
        font-size: 39px;
        font-weight: bold;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.6);
        margin-bottom: 1px;
    }
    .title-container .description {
        font-size: 20px;
        opacity: 0.8;
    }
    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 90%;
        max-width: 1200px;
        background: rgba(20, 20, 20, 0.9);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 15px rgba(255, 255, 255, 0.2);
        margin-top: 20px;
        height: 80%;
    }
    #map-layer {
        width: 70%;
        height: 100%;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(255, 255, 255, 0.2);
        margin-right: 20px;
    }
    .info-container {
        width: 30%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .info-container .textbox {
        margin-top: 15px;
    }

    </style>
</head>
<body>
    <div class="title-container">
        <h1>📍 Sat Nav</h1>
        <p class="description">Your Safety is our first priority</p>
    </div>

    <div class="container">
        <div id="map-layer"></div>
        <div class="info-container">
            <div class="textbox" style="display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 60px; background: white; border-radius: 5px; color: black; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                <p style="margin: 10px;">Why Sat-Nav?</p>
                <p style="margin: 5px;">Efficient</p>
                <p style="margin: 5px;">Reliable</p>
                <p style="margin: 5px;">Convenient</p>
            </div>
        </div>
    </div>

    <script 
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY;?>&callback=initMap"
        async defer>
    </script>

    <script>
        function initMap() {
            var mapLayer = document.getElementById("map-layer");
            var centerCoordinates = new google.maps.LatLng(<?php echo $row['lat']; ?>, <?php echo $row['lng']; ?>);
            var defaultOptions = { center: centerCoordinates, zoom: 10 };

            var map = new google.maps.Map(mapLayer, defaultOptions);

            <?php while($row = $result->fetch_assoc()){ ?>
                var location = new google.maps.LatLng(<?php echo $row['lat']; ?>, <?php echo $row['lng']; ?>);
                new google.maps.Marker({
                    position: location,
                    map: map
                });
            <?php } ?>
        }
    </script>
</body>
</html>
