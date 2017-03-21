<?php
define('__ROOT__',dirname(__FILE__));
require_once __ROOT__."/class/User.class.php";
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="js/cookie.js"></script>
<script src="js/editprofile.js"></script>

<?php
// Check we aren't already logged in via cookie
if (isset($_COOKIE["username"]) && isset($_COOKIE["token"])) {
	$user = new User($_COOKIE["username"],$_COOKIE["token"]);
	if ($user->isLoggedIn()) {
            echo "Logged in as user ".$_COOKIE["username"];
	} else {
            echo "Failed to log in in as user ".$_COOKIE["username"];
	}
} else {
    echo "Not logged in";
}
echo "<br>";
?>

Profile picture: <input type="text" name="profilepictureurl" id="profilepictureurl"> <a onClick="changeProfilePicture('profilepictureurl')">Change</a><br>
Postcode: <input type="text" name="postcode" id="postcode"> <a id="changePostcode">Change</a><br>
Email: <input type="text" name="email" id="email"> <a id="changeEmail">Change</a><br>
Password: <input type="password" name="password" id="password"> <br>
Password Verify: <input type="password" name="password" id="passwordverify"> <a id="changePassword">Change</a>
<div id="message"></div>
<script>
	function changePostcodeWrapper(id) {
		changePostcode($(id).val())
		.then(function(result) {
			console.log(result);
			$("#message").html = 'Success: ' + result;
		}).catch(function(error) {
			if (error.hasOwnProperty('error')) {
				alert(error.error);
			} else {
				console.log(error);
			}
		})		
	}
	function changeEmailWrapper(id) {
		changeEmail($(id).val())
		.then(function(result) {
			console.log(result);
			$("#message").html = 'Success: ' + result;
		}).catch(function(error) {
			if (error.hasOwnProperty('error')) {
				alert(error.error);
			} else {
				console.log(error);
			}
		})		
	}
	function changePasswordWrapper(id, verify) {
	
            if ($(id).val() == $(verify).val()) {
                changePassword($(id).val())
                .then(function(result) {
                        console.log(result);
                        $("#message").html = 'Success: ' + result;
                }).catch(function(error) {
                        if (error.hasOwnProperty('error')) {
                                alert(error.error);
                        } else {
                                console.log(error);
                        }
                })	
            } else {
                alert("yo those passwords ain't the same bruv");
            }
	}
	$("#changePostcode").click(function () {
		changePostcodeWrapper("#postcode");
	})
	$("#changeEmail").click(function () {
		changeEmailWrapper("#email");
	})
	$("#changePassword").click(function () {
		changePasswordWrapper("#password", "#passwordverify");
	})
</script>