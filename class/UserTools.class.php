<?php
require 'db.php';
require 'lib/password.php'; // Password hashing library

define("DOMAIN", "http://community.dur.ac.uk/cs.seg01/foodshare/");
class UserTools {
	
	function printErrors($errors) {
		if (gettype($errors) == "array") {
			foreach($errors as $error) {
				?>
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<?php echo $error; ?>
				</div>
			<?php
			}
		}
	}
	
	function validateUserEmail($username,$key) {
		try {
			$db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
		} catch(PDOEXCEPTION $e) {
			$errors[] = "There was a database error, please try again later.";
			return $errors;
		}
	}
	
	/**
	* Register a user with the database
	*
	* @param $username The desired username
	* @param $email The email address of the user
	* @param $password The password of the user (plaintext)
	* @param $confirmpassword The confimation password of the user
	* 
	* @returns Array $errors An array of strings containing any errors encounted during the process, or true on success
	**/
	function registerBasicUser($username, $email, $password, $confirmpassword) {
		try {
			$db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
		} catch(PDOEXCEPTION $e) {
			$errors[] = "There was a database error, please try again later.";
			return $errors;
		}
		// Are all fields filled in?
		if (empty($username) || empty($password) || empty($email) || empty($confirmpassword)) {
			$errors[] = "All fields must not be empty.";
		}
		// Is the email valid?
		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			$errors[] = "Please enter a valid email address.";
		}
		// Is the password correct? 
		if ($password !== $confirmpassword) {
			$errors[] = "Passwords do not match";
		}
		// Has the username or email been taken?
		try {
			$stmt = $db->prepare('SELECT * FROM user WHERE email = :email');
			$stmt->bindValue(':email', $email, PDO::PARAM_STR);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$errors[] = "That email address has already been registered";
			}
			$stmt = $db->prepare('SELECT * FROM user WHERE username = :username');
			$stmt->bindValue(':username', $username, PDO::PARAM_STR);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				$errors[] = "That username has already been taken";
			}
		} catch(PDOEXCEPTION $e) {
			$errors[] = "There was a database error, please try again later.";
			return $errors;
		}
		if (!empty($errors)) {
			return $errors;
		}
		// We are free from errors! Insert into the DB! 
		try {
			// Get hashed password
			$hash = password_hash($password, PASSWORD_BCRYPT);
			$key = md5((string)rand(0,100000));
			$stmt = $db->prepare("INSERT INTO `user` (`username`, `email`, `password`, `postcode`, `verified`, `confirm_email_key`) VALUES (:username, :email, :hash, NULL, 0, :key);");
			$stmt->bindValue(':username', $username, PDO::PARAM_STR);
			$stmt->bindValue(':email', $email, PDO::PARAM_STR);
			$stmt->bindValue(':hash', $hash, PDO::PARAM_STR);
			$stmt->bindValue(':key', $key, PDO::PARAM_STR);
			$stmt->execute();
		} catch(PDOEXCEPTION $e) {
			$errors[] = "There was a database error, please try again later.";
			return $errors;
		}
		// Send confirmation email
		mail($email, "Confirmation of FoodShare signup", "Hey! \n \n You (or an imposter) signed up to foodShare under this email address. If you recieved this email in error, ignore this message. Else, click the link below: \n \n <a href=\"".DOMAIN."confirm.php?key=".$key."&user=".$username."\">Confirm registration</a> \n \n Thanks, \n The FoodShare Team");
		return true;
	}
	
}