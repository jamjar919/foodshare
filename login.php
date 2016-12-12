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
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <title>Login</title>
		
		<style>		
			.vertical-alignment-helper {
				display:table;
				height: 100%;
				width: 100%;
				pointer-events: none; 
			}
			
			.vertical-alignment-center {
				display: table-cell;
				vertical-align: middle;
				pointer-events: none;
			}
			
			.modal-content {
				width: inherit;
				height: inherit;
				margin: 0 auto;
				pointer-events: all;
			}
			.cwrapper {
				width: 70%;
				margin:auto;
			}	
			html,body {
				height: 100%;
			}
			.col-centered{
				float: none;
				margin: 0 auto;
			}
			
			.form-control:focus {
			  border-color: #00cc44;
			  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px #00cc44;
			}
			
			.btn-custom {
				color: white;
				border-color: #00b33c;
				background-color: #00b33c;
			}
			.btn-custom:hover, .btn-custom:active,.btn-custom:focus {
				color: white;
				border-color: #00cc44;
				background-color: #00cc44;
			}
			
			
			
		</style>
		
    </head>
    <body>

		<div class="container">
		<h2>HomePage</h2>
		<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-1">Login</button>
			
			<div class="modal fade" tabindex="-1" role="dialog" id="modal-1">
				<div class="vertical-alignment-helper">
					<div class = "modal-dialog vertical-alignment-center">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h3 class="modal-title text-center">Login</h3>
							</div>
							<div class="modal-body">
								<form class="login-form" action="#" method="POST">
									<div class="form-group row">
										<!-- <label for="fname" class="col-xs-2 col-form-label">First Name</label> -->
										<div class="col-xs-12 col-sm-6 col-centered">
											<input class="form-control" type="text" name="fname" placeholder="Username">
										</div>
									</div>
									<div class="form-group row">
										<!-- <label for="password" class="col-xs-2 col-form-label">Password</label> -->
										<div class="col-xs-12 col-sm-6 col-centered">
											<input class="form-control" type="password" name="password" placeholder="Password">
										</div>
									</div>
									<div class="form-group row">
										<div class="text-center col-xs-12 col-sm-6 col-centered">
											<button style="width: 100%" type="submit" class="btn btn-custom " name="submit" value="Login">Log up</button>
										</div>

									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		
		</div>
		
		
		
	
    </body>
</html>