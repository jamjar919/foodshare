<?php 
require_once "class/User.class.php";
require_once "class/UserTools.class.php";
// Check we aren't already logged in via cookie
$loggedIn = false;
if (isset($_COOKIE["username"]) && isset($_COOKIE["token"])) {
	$user = new User($_COOKIE["username"],$_COOKIE["token"]);
	if ( ! ($user->isLoggedIn())) {
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