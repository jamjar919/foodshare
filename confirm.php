<?php
define('__ROOT__',dirname(__FILE__));
require_once __ROOT__."/class/User.class.php";
require_once __ROOT__."/class/UserTools.class.php";
if (isset($_GET["username"]) && isset($_GET["key"])) {
	if (UserTools::validateUserEmail($_GET["username"],$_GET["key"])) {
		echo "Successfully validated email.";
	} else {
		echo "The link you have supplied is incorrect.";
	}
}
?>
Email confirmation page