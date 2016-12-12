<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<title>Sign Up</title>
	</head>
	
	<style>
		html,body {
			height: 100%;
		}
		.col-centered{
			float: none;
			margin: 0 auto;
		}
		
		
		.vcenter {
			display: inline-block;
			vertical-align: middle;
			float: none;
		}
	
		.nav {
			background-color: #00b33c;
			padding-top: 20px;
			margin-bottom: 5%;
		}
		
		.btn-center {
			text-align-center;
		}
		
		.btn-custom {
			
			color: white;
			border-color: #00b33c;
			background-color: #00b33c;
		}
		.btn-custom:hover, .btn-custom:active, .btn-custom:focus {
			color: white;
			border-color: #00cc44;
			background-color: #00cc44;
		}
		
		.form-control:focus {
		  border-color: #00cc44;
		  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px #00cc44;
		}
		
		
	
	</style>
	
	<body>
		<div class="nav">
			<div class="container-fluid" >
				<div class="row">
					<div class="col-md-2 col-offset-sm-6">
						<a href=""><span class="glyphicon glyphicon-home"></span></a>
					</div>
					<div class="col-md-8">
						<h1 class="text-center">Company Name</h1>
					</div>
					<div id="navbar">
						<nav class="navbar">
						</nav>				
					</div>
				</div>
				
		
			</div>
		</div>
		
	
		<div class="container">
			<div class="row text-center">
				<h2>Sign Up</h2>
		
			<div class="row">
				<form class="sign-form" action="#" method="POST">
					<div class="form-group row">
						<!-- <label for="fname" class="col-xs-2 col-form-label">First Name</label> -->
						<div class="col-xs-8 col-sm-5 col-centered">
							<input class="form-control" type="text" name="fname" placeholder="First Name">
						</div>
					</div>
					<div class="form-group row">
						<!--<label for="sname" class="col-xs-2 col-form-label">Surname</label> -->
						<div class="col-xs-8 col-sm-5 col-centered">
							<input class="form-control" type="text" name="sname" placeholder="Surname">
						</div>
					</div>
					<div class="form-group row">
						<!-- <label for="email" class="col-xs-2 col-form-label">Email Address</label> -->
						<div class="col-xs-8  col-sm-5 col-centered">
							<input class="form-control" type="text"  name="email" placeholder="Email Address">
						</div>
					</div>
					<div class="form-group row">
						<!-- <label for="password" class="col-xs-2 col-form-label">Password</label> -->
						<div class="col-xs-8 col-sm-5 col-centered">
							<input class="form-control" type="password" name="password" placeholder="Password">
						</div>
					</div>
					<div class="form-group row">
						<!-- <label for="cpassword" class="col-xs-2 col-form-label">Confirm Password</label> -->
						<div class="col-xs-8 col-sm-5 col-centered">
							<input class="form-control" type="password" name="cpassword" placeholder="Confirm Password">
						</div>
					</div>
					<div class="form-group row">
						<div class="text-center col-xs-8 col-sm-5 col-centered">
							<button style="width: 100%" type="submit" class="btn btn-custom " name="submit" value="signUp" id="submit">Sign up</button>
						</div>

					</div>
					
				</form>
			</div>		
		</div>
	</body>

</html>