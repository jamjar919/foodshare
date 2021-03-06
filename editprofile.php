<?php 
/**
 * Page allowing the current user to edit their profile with AJAX requests to the API.
 */
?>
<?php
define('__ROOT__',dirname(__FILE__));
require __ROOT__.'/class/Page.class.php';
require_once __ROOT__."/class/User.class.php";
$p = new Page("Edit profile",true);
$p->buildHead();
$p->buildHeader();
$user = $p->user;
$profile = $p->user->getPrivateProfile();
?>
<h1>Edit Profile</h1>
<div id="all-errors"></div>
<div class="masonry masonry-two">
    <div class="card card-block">
        <div id="profilepicture-messages">
        </div>
        <div class="form-group">
            <div class="media">
                <div class="media-left">
                    <a href="<?php echo $user->getProfilePicture(); ?>">
                        <img src="<?php echo $user->getProfilePicture(); ?>" id="currentProfilePicture" height="100px" width="100px">
                    </a>
                </div>
                <div class="media-body">
                    <label>Profile Picture</label>
                    <input accept="image/*" type="file" id="profilepicture">
                </div>
            </div>
        </div>
        <a class="btn btn-default" id="changeProfilePicture">Change</a>
    </div>
    <div class="card card-block">
        <div id="postcode-messages">
        </div>
        <div class="form-group">
            <label for="postcode">Postcode</label>
            <input type="text" class="form-control" name="postcode" id="postcode" value="<?php echo $profile['postcode'];?>" placeholder="<?php echo $profile['postcode'];?>">
        </div>
        <a class="btn btn-default" id="changePostcode">Change</a>
    </div>
    <div class="card card-block">
        <div id="email-messages">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" id="email" value="<?php echo $profile['email'];?>" placeholder="<?php echo $profile['email'];?>">
        </div>
        <a class="btn btn-default" id="changeEmail">Change</a>
    </div>
    <div class="card card-block">
        <div id="password-messages">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" id="password"> <br>
            <label for="passwordverify">Password Verify</label>
            <input type="password" class="form-control" name="passwordverify" id="passwordverify">
        </div>
        <p class="help-block">Warning: Changing your password will log you out!</p>
        <a class="btn btn-default" id="changePassword">Change</a>
    </div>
</div>
<script src="js/cookie.js"></script>
<script src="js/editprofile.js"></script>
<script>
        function printError(message, selector) {
            $(selector).append(
                $("<div>")
                .addClass("alert alert-danger alert-dismissible")
                .text(message)
                .append("<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>")
            )
        }
        function printSuccess(message, selector) {
            $(selector).append(
                $("<div>")
                .addClass("alert alert-success alert-dismissible")
                .text(message)
                .append("<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>")
            )
        }
        function parseResult(r) {
            if (r.hasOwnProperty('error')) {
                printError(r.error, "#all-errors")
            }
            if (r.hasOwnProperty('postcode')) {
                if (r.postcode.success) {
                    printSuccess(r.postcode.message, "#postcode-messages");
                } else {
                    printError(r.postcode.message, "#postcode-messages");
                }
            }
            if (r.hasOwnProperty('email')) {
                if (r.email.success) {
                    printSuccess(r.email.message, "#email-messages");
                } else {
                    printError(r.email.message, "#email-messages");
                }
            }
            if (r.hasOwnProperty('profilepicture')) {
                if (r.profilepicture.success) {
                    printSuccess(r.profilepicture.message, "#profilepicture-messages");
                } else {
                    printError(r.profilepicture.message, "#profilepicture-messages");
                }
            }
            if (r.hasOwnProperty('password')) {
                if (r.password.success) {
                    printSuccess(r.password.message, "#password-messages");
                } else {
                    printError(r.password.message, "#password-messages");
                }
            }
        }
        function changeProfilePictureWrapper(id){
            var fileInput = document.getElementById(id.substring(1));
            var file = fileInput.files[0];
            uploadPicture(file)
            .then(function(result) {
                // Yay we uploaded, get link and send to our db
                var link = result["data"]["link"];
                changeProfilePicture(link)
                .then(function(result) {
                        $("#currentProfilePicture").attr("src",link);
                        parseResult(result);
                }).catch(function(error) {
                        parseResult(error);
                })
            })
        }
	function changePostcodeWrapper(id) {
		changePostcode($(id).val())
		.then(function(result) {
			parseResult(result);
		}).catch(function(error) {
			parseResult(error)
		})		
	}
	function changeEmailWrapper(id) {
		changeEmail($(id).val())
		.then(function(result) {
			parseResult(result);
		}).catch(function(error) {
			parseResult(error)
		})
	}
	function changePasswordWrapper(id, verify) {
            if ($(id).val() == $(verify).val()) {
                changePassword($(id).val())
                .then(function(result) {
                        parseResult(result);
                }).catch(function(error) {
                        parseResult(error)
                })	
            } else {
                printError("Passwords do not match.","#password-messages");
            }
	}
	$("#changeProfilePicture").click(function () {
		changeProfilePictureWrapper("#profilepicture");
	})
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
<?php
    $p->buildFooter();
?>