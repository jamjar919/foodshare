<?php
session_start();
require 'functions.php';
// Check if we are already logged in
var_dump($_SESSION);
if (isLoggedIn($_SESSION)) {
    // We are logged in, go to members page
    // header("Location: members.php");
    echo "Automatically logged in via session";
    header("Location: index.php");
}
// Did we submit the form? If we did, check if it's correct and generate a new login token
if (isset($_POST["username"]) && isset($_POST["password"])) {
    $uid = getUid($_POST["username"]);
    $token = generateLoginToken($_POST["username"], $_POST["password"]);
    if ($token) { 
        $_SESSION["uid"] = $uid;
        $_SESSION["token"] = $token;
        echo "Session set";
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
            <input type="text" name="username"><br>
            <label for="password">Password:</label>
            <input type="password" name="password"><br>
            <input type="submit" value="Login">
        </form>
    </body>
</html>