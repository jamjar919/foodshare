<?php
if (isset($_GET["username"]) && isset($_GET["key"])) {
	require_once "class/UserTools.class.php";
	if (UserTools::validateUserEmail($_GET["username"],$_GET["key"])) {
		echo "Successfully validated email.";
	} else {
		echo "The link you have supplied is incorrect.";
	}
}
?>
Email confirmation page