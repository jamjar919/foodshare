<?php
header("Content-Type: application/json");
define('__ROOT__',dirname(dirname(dirname(__FILE__))));
require_once __ROOT__."/class/User.class.php";
require_once __ROOT__."/class/UserTools.class.php";

function update_profile() {
	$response = array();
	if (!isset($_POST["token"]) && !isset($_POST["username"])) {
		$response["error"] = "Username or token not supplied.";
		return $response;
	}
	$user = new User($_COOKIE["username"],$_COOKIE["token"]);
	if (!($user->isLoggedIn())) {
		$response["error"] = "Username and auth token supplied are invalid.";
	}
	// User is valid, let's check our parameters and do the update_profile
	if (isset($_POST["location"])) {
		
	}
	if (isset($_POST["postcode"])) {
		return $_POST;
	}
	if (isset($_POST["email"])) {
		
	}
	if (isset($_POST["profilepicture"])) {
		
	}
	if (isset($_POST["password"])) {
		
	}
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	echo json_encode(update_profile());
}