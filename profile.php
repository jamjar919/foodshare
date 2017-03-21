<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="js/cookie.js"></script>
<script src="js/editprofile.js"></script>
Profile picture: <input type="text" name="profilepictureurl" id="profilepictureurl"> <a onClick="changeProfilePicture('profilepictureurl')">Change</a><br>
Postcode: <input type="text" name="postcode" id="postcode"> <a id="changePostcode">Change</a><br>
Email: <input type="text" name="email" id="email"> <a onClick="changeEmail('email')">Change</a><br>
Password: <input type="password" name="password" id="password"> <a onClick="changePassword('password')">Change</a>
<div id="message"></div>
<script>
	function changePostcodeWrapper() {
		var id = "#postcode";
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
	$("#changePostcode").click(function () {
		changePostcodeWrapper();
	})
</script>