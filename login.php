<?php
session_start();
require "functions.php";
// Did we submit the form? If we did, check if it's correct and generate a new login token
if (isset($_POST["username"]) && isset($_POST["password"])) {
    $uid = getUid($_POST["username"]);
    $token = generateLoginToken($_POST["username"], $_POST["password"]);
    if ($token) {
        $_SESSION["uid"] = $uid;
        $_SESSION["token"] = $token;
        echo "Session set";
        header("Location: index.php");
    } else {
        // Login was incorrect!
        //include "header.php";
        include "nav.php";
        ?>
        Your username or password was incorrect.
        <?php
        include "footer.php";
    }
}
?>