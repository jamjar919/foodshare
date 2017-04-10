<?php
ob_start();
require 'class/Page.class.php';
$p = new Page("Login");


define('__ROOT__',dirname(__FILE__));
require_once __ROOT__."/class/User.class.php";
require_once __ROOT__."/class/UserTools.class.php";
$errors = array();

// Check we aren't already logged in via cookie
if (isset($_COOKIE["username"]) && isset($_COOKIE["token"])) {
	$user = new User($_COOKIE["username"],$_COOKIE["token"]);
	if ($user->isLoggedIn()) {
		header("Location: membersSearch.php");
	}
}

// Did we submit the form? If we did, check if it's correct and generate a new login token
if (isset($_POST["username"]) && isset($_POST["password"])) {
	$user = new User($_POST["username"]);
	$user->login($_POST["password"]);
	if ($user->isLoggedIn()) {
		header("Location: membersSearch.php");
	} else {
		$errors[] = "Incorrect username or password";
	}
}
$p->buildHead();
$p->buildHeader();
?>
<?php UserTools::printErrors($errors); ?>
<div class="row">
    <div class="col-sm-12 col-md-12">
        <form action="#" method="POST">
            <div class="form">
                <h3>Login</h3>
                <div class="form-group">
                    <input type="text" placeholder="Username" class="form-control" name="username">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Password" class="form-control" name="password">
                </div>
                <input type="submit" class="btn btn-default" value="Login">
            </div>
        </form>
    </div>
</div>
<?php
$p->buildFooter();
?>