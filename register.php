<?php
    session_start();
    require "functions.php";
    // Check if we are already logged in
    if (isLoggedIn($_SESSION)) {
        header("Location: index.php");
    }
    if (isset($_POST["submitted"])) {
        $errors = registerBasicUser($_POST["username"], $_POST["email"], $_POST["password"], $_POST["confirmpassword"]);
        if ($errors === true) {
            // We registered successfully!
        } else {
            var_dump($errors);
        }
    }
?>
<div class="col-sm-12 col-md-12">
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