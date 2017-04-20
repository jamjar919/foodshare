<?php 
/**
 * The page to register a new user to the site. Calls on the register methods in usertools, and will redirect
 * the user to their profile page if they're already logged in 
 */
?>
<?php
require 'class/Page.class.php';
$p = new Page("Register");

define('__ROOT__',dirname(__FILE__));
require_once __ROOT__."/class/User.class.php";
require_once __ROOT__."/class/UserTools.class.php";
$errors = array();

if (isset($_POST["submitted"])) {
	// We submitted form - now we try and register 
	$register_success = UserTools::registerBasicUser(
		$_POST["username"],
		$_POST["email"],
		$_POST["password"],
		$_POST["confirmpassword"]
	);
	if ($register_success === true) {
		$user = new User($_POST["username"]);
		$user->login($_POST["password"]);
		if ($user->isLoggedIn()) {
			header("Location: profile.php");	
		}
	} else {
		$errors = $register_success;
	}
}
$p->buildHead();
$p->buildHeader();
?>
<div class="row">
    <div class="col-md-6">
        <form action="#" method="POST">
            <div class="form">
                <h3>Welcome to FoodShare!</h3>
                <div class="form-group">
                    <input type="text" placeholder="Username" class="form-control" name="username">
                </div>
                <div class="form-group">
                    <input type="text" placeholder="Email" class="form-control" name="email">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Password" class="form-control" name="password">
                </div>
                <div class="form-group">
                    <input type="password" placeholder="Re-enter Password" class="form-control" name="confirmpassword">
                </div>
                <input type="hidden" name="submitted" value="1">
                <input type="submit" class="btn btn-default" value="Sign Up">
            </div>
        </form>
    </div>
    <div class="col-md-6">
        <?php UserTools::printErrors($errors); ?>
    </div>
</div>
<?php
$p->buildFooter();
?>