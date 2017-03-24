<?php
header("Content-Type: application/json");
require_once "../../class/User.class.php";
require_once "../../class/UserTools.class.php";

function update_profile() {
	$response = array();
	if (!isset($_POST["token"]) && !isset($_POST["username"])) {
		$response["error"] = "Username or token not supplied.";
		return $response;
	}
	$user = new User($_COOKIE["username"],$_COOKIE["token"]);
	if (!($user->isLoggedIn())) {
		$response["error"] = "Username and auth token supplied are invalid.";
		return $response;
	}
	// User is valid, let's check our parameters and do the update_profile
	if (isset($_POST["location"])) {
		$response["location"] = array();
		if (
			(gettype($_POST["location"]) === "array") &&
			(sizeof($_POST["location"]) > 1)
		) {
			$latitude = (float)$_POST["location"][0];
			$longitude = (float)$_POST["location"][1];
			if (($latitude != null) && ($longitude != null)) {
				if ($user->updateLocation($latitude,$longitude)) {
					$response["location"]["message"] = "Successfully updated location";
					$response["location"]["success"] = true;
				} else {
					$response["location"]["message"] = "Couldn't update the location";
					$response["location"]["success"] = false;
					$response["error"] = "An error occured updating your profile.";
				}
			} else {
				$response["location"]["message"] = "Invalid latitude or longitude specified";
				$response["location"]["success"] = false;
				$response["error"] = "An error occured updating your profile.";
			}
		} else {
			$response["location"]["message"] = "Location should be an array";
			$response["location"]["success"] = false;
			$response["error"] = "An error occured updating your profile.";
		}
	}
	if (isset($_POST["postcode"])) {
		if($user->updatePostcode($_POST["postcode"])) {
			$response["postcode"]["message"] = "Successfully updated postcode";
			$response["postcode"]["success"] = true;
		} else {
			$response["postcode"]["message"] = "Couldn't update the postcode.";
			$response["postcode"]["success"] = false;
			$response["error"] = "An error occured updating your profile.";
		}
	}
	if (isset($_POST["email"])) {
            if($user->updateEmail($_POST["email"])) {
                $response["email"]["message"] = "Successfully updated email. Please click the verification link in your email to proceed.";
                $response["email"]["success"] = true;
            } else {
                $response["email"]["message"] = "Couldn't update the email address.";
                $response["email"]["success"] = false;
                $response["error"] = "An error occured updating your profile.";
            }
		
	}
	if (isset($_POST["profilepicture"])) {
            if($user->updateProfilePictureURL($_POST["profilepicture"])) {
                $response["profilepicture"]["message"] = "Successfully updated profile picture.";
                $response["profilepicture"]["success"] = true;
            } else {
                $response["profilepicture"]["message"] = "Couldn't update your profile picture.";
                $response["profilepicture"]["success"] = false;
                $response["error"] = "An error occured updating your profile.";
            }
	}
	if (isset($_POST["password"])) {
            if($user->updatePassword($_POST["password"])) {
                $response["password"]["message"] = "Successfully updated password.";
                $response["password"]["success"] = true;
            } else {
                $response["password"]["message"] = "Couldn't update the password.";
                $response["password"]["success"] = false;
                $response["error"] = "An error occured updating your profile.";
            }
	}
	return $response;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	echo json_encode(update_profile());
} else {
	echo json_encode(array("error"=>"This API does not accept GET requests"));
}