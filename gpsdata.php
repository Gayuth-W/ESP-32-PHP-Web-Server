<?php 
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$api_key = escape_data($_POST["api_key"]);
	if($api_key == ESP32_API_KEY) {
		$latitude = escape_data($_POST["lat"]);
		$longitude = escape_data($_POST["lng"]);
		
		$sql = "INSERT INTO tbl_gps(lat,lng,created_date) 
			VALUES('".$latitude."','".$longitude."','".date("Y-m-d H:i:s")."')";

		if($db->query($sql) === FALSE)
			{ echo "Error: " . $sql . "<br>" . $db->error; }

		echo "OK. INSERT ID: ";
		echo $db->insert_id;
	}
	else
	{
		echo "Wrong API Key";
	}
}
else
{
	echo "No HTTP POST request found";
}

function escape_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
