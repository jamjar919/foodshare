<?php 
define('__ROOT__',dirname(__FILE__));
require_once __ROOT__."/class/User.class.php";
require_once __ROOT__."/class/UserTools.class.php";
// Check we aren't already logged in via cookie
$loggedIn = false;
if (isset($_COOKIE["username"]) && isset($_COOKIE["token"])) {
	$user = new User($_COOKIE["username"],$_COOKIE["token"]);
	if ($user->isLoggedIn()) {
		$loggedIn = true;
	}
}
if (!$loggedIn) {
	header("Location: login.php");	
}
?>
Eventually there will be stuff here
<br>
<a href="logout.php">Logout</a>

Cookies:
<br>
<?php

var_dump($_COOKIE);

?>