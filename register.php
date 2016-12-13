<?php
    session_start();
    require "functions.php";
    // Check if we are already logged in
    if (isLoggedIn($_SESSION)) {
        // We are logged in, go to members page
        // header("Location: members.php");
        header("Location: index.php");
    }
    if (isset($_POST["submitted"])) {
        $possibleErrors = registerBasicUser($_POST["username"], $_POST["email"], $_POST["password"], $_POST["confirmpassword"]);
        if ($possibleErrors === true) {
            // We registered successfully!
        }
    }
?>
<?php
    include "header.php";
    include "nav.php";
?>
    <div class="col-sm-12 col-md-12">
        <div class="thumbnail">
            <div class="form">
            <h3>Welcome to FoodShare!</h3><p>
            <div class="form-group">
                <input type="text" placeholder="Email" class="form-control" id="usr">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Password" class="form-control" id="pwd">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Re-enter Password" class="form-control" id="pwd">
            </div>
            <div class="form-group">
                <input type="text" placeholder="Country" class="form-control" id="pwd">
            </div>
            <div class="form-group">
                <input type="text" placeholder="City" class="form-control" id="pwd">
            </div>
            </p><a href="#" class="btn btn-default" role="button">Sign Up and Log In</a>
            </div>
        </div>
        </div>
    </div>
    </div>
<?php
    include "footer.php";
?>