<?php
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (isset($_POST["location"])) {
		
	}
}

echo json_encode($_POST);