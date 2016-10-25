<?php
ini_set('display_errors',1);  error_reporting(E_ALL);
session_start();
require 'functions.php';
// Check if we are already logged in
if (isLoggedIn($_SESSION)) {
    // We are logged in, go to members page
    // header("Location: members.php");
    echo "Automatically logged in via session";
}
// Did we submit the form? If we did, check if it's correct and generate a new login token
if (isset($_POST["username"]) && isset($_POST["password"])) {
    echo "Form submitted";
    $uid = getUid($_POST["username"]);
    $token = generateLoginToken($_POST["username"], $_POST["password"]);
    var_dump($token);
    if ($token) { 
        $_SESSION["uid"] = $uid;
        $_SESSION["token"] = $token;
        echo "Session cooie set";
    } else {
        // Login was incorrect!
        $error = 'Your username or password was incorrect.';
    }
}
?>
<!doctype html>
<html>
    <head>
        <title>Login</title>
    </head>
    <body>
        <?php
            if (isset($error)) {
                echo $error;
            }
        ?>
        <form class="login-form" action="#" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username">
            <label for="password">Password:</label>
            <input type="password" name="password">
            <input type="submit" value="Login">
        </form>
    </body>
</html>