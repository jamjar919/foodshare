<?php
session_start();
require 'functions.php';
// Check if we are already logged in
if (isLoggedIn($_SESSION)) {
    // We are logged in, don't 
    header("Location: members.php");
}
?>
<!doctype html>
<html>
    <head>
        <title>Login</login>
    </head>
    <body>
        <form class="login-form" action="#" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username">
            <label for="password">Password:</label>
            <input type="password" name="password">
            <input type="submit" value="Login">
        </form>
    </body>
</html>