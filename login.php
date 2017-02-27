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
        header("Location: index.php");
    } else {
        ?>
        Your username or password was incorrect.
        <?php
    }
}
?>
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