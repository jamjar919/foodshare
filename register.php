<?php
session_start();
require 'functions.php';
// Check if we are already logged in
if (isLoggedIn($_SESSION)) {
    // We are logged in, go to members page
    // header("Location: members.php");
    echo "Automatically logged in via session";
    header("Location: index.php");
}
if (isset($_POST["submitted"])) {
    $possibleErrors = registerBasicUser($_POST["username"], $_POST["email"], $_POST["password"], $_POST["confirmpassword"]);
    if ($possibleErrors === true) {
        // We registered successfully!
    }
}
?>
<!doctype html>
<html>
    <head>
        <title>Register</title>
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
            <label for="username">Email:</label>
            <input type="email" name="email"><br>
            <label for="password">Password:</label>
            <input type="password" name="password"><br>
            <label for="password">Confirm Password:</label>
            <input type="password" name="confimpassword"><br>
            <input type="hidden" name="submitted" value="1">
            <input type="submit" value="Login">
        </form>
    </body>
</html>