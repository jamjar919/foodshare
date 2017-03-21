<?php

define('__ROOT__',dirname(dirname(__FILE__)));
require_once __ROOT__.'/db.php';
require_once __ROOT__.'/lib/password.php'; // Password hashing library

class User
{
    public $username;
	public $authtoken;
	private $isLoggedIn = false;

	function __construct($user, $auth="") {
		$this->username = $user;
		if ($auth !== "") {
			// User is trying to auth
			$this->authtoken = $auth;
			$this->isLoggedIn = $this->checkLoginToken($auth);
		}
	}
	
	public function isLoggedIn() {
		return $this->isLoggedIn;
	}
	
	/**
	* Sets the auth token as a cookie if password is correct
	**/
    public function login($password) {
        $token = $this->generateLoginToken($password);
		if ($token === false) {
			// Login unsuccessful
			return false;			
		} else {
			$this->authtoken = $token;
			$this->isLoggedIn = true;
			$this->setUserCookie();
		}
    }
	
	public function setUserCookie() {
		setcookie("token", $this->authtoken);
		setcookie("username", $this->username);
	}
	
	/**
	* Generate a login token string, given a valid username and password. Checks against the database to make sure the password matches the one on record. Returns false otherwise
	*
	* @param string $password Password of user to generate a hash for
	*
	* @return string The valid login token. Returns false if the data was invalid.
	*/
	public function generateLoginToken($password) {
		$username = $this->username;
		if (empty($username) || empty($password)) {
			return false;
		}
		try {
                        $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
			$stmt = $db->prepare("SELECT * FROM user WHERE username = :username");
			$stmt->bindValue(":username", $username, PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
		} catch(PDOEXCEPTION $e) {
			// We errored out. 
			echo $e;
			return false;
		}
		$hash = $result["password"];
		if (password_verify($password, $hash)) {
			// Valid! Make us a token!
			$token = $this->makeNewToken();
			$expiry = time() + (7*24*60*60);
			// Store it in the database, with expiry date 1 week in the future.
			$stmt = $db->prepare("INSERT INTO auth(authkey,username,expiry) VALUES(:token,:user,:expiry)");
			$stmt->execute(array(':token' => $token, ':user' => $username, ':expiry' => $expiry));
			return $token;
		} else {
			return false;
		}
	}

	/**
	* Makes a new token, based on random data
	*
	* @returns string A random login token
	**/
	public function makeNewToken() {
		return md5((string)rand(0,100000));
	}
	
	/**
	* Check the login token to make sure it is in date, correct for the user, and still valid.
	*
	* @param string $token Token you want to check if it is valid or not
	*
	* @return boolean True if the token is valid, False if the token is valid, or does not match the UID supplied
	**/
	public function checkLoginToken($token) {
		$db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
		$stmt = $db->prepare("SELECT * FROM auth WHERE username = :username");
		$stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($results as $result) {
			if (($result["authkey"] == $token) && ($result["uid"] == $uid)) {
				// Token is valid, is it expired?
				if (time() < $result["expiry"]) {
					// Token is still in date and everything is valid, sweet.
					return true;
				} else {
					// The key is correct, but out of date. We should delete it from the records
					$delstmt = $db->prepare("DELETE FROM table WHERE id=:id");
					$delstmt->bindValue(":id", $result["id"], PDO::PARAM_INT);
					$delstmt->execute();
				}
			}
		}
		return false;
	}
	
	public function updatePostcode($newPostcode) {
		if ($this->isLoggedIn()) {
			try {
				$db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
				$stmt = $db->prepare("UPDATE user SET postcode = :postcode WHERE username = :username");
				$stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
				$stmt->bindValue(":postcode", $newPostcode, PDO::PARAM_STR);
				$stmt->execute();
				if ($stmt->rowCount()) {
					return true;
				}
				return false;
			} catch(PDOException $ex) {
				return false;
			}
		}
		return false;
	}
	
	public function updateLocation($latitude, $longitude) {
		if ($this->isLoggedIn()) {
			if (($latitude <= 90) && ($latitude >= -90) &&	($longitude <= 180) && ($longitude >= -180)
			) {
				try {
					$db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
					$stmt = $db->prepare("UPDATE user SET latitude = :lat, longitude = :long WHERE username = :username");
					$stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
					$stmt->bindValue(":lat", $latitude, PDO::PARAM_STR); // Yes, I know they are decimals but PDO uses stringy magic 
					$stmt->bindValue(":long", $longitude, PDO::PARAM_STR);
					$stmt->execute();
					if ($stmt->rowCount()) {
						return true;
					}
                    return false;
				} catch(PDOException $ex) {
					return false;
				}
			}
		}
		return false;
	}
	
}
?>