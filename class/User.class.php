<?php
define('__ROOT__',dirname(dirname(__FILE__)));
require_once __ROOT__.'/db.php';
require_once __ROOT__.'/lib/password.php'; // Password hashing library

/**
 * This class represents a user. It can be initialised with just a username, in which case public data and read only access is permitted, or with a username/token combination that
 * will authorise the user, allowing access to functions able to modify data in the database about the user.
 */
class User
{
    public $username;
    public $authtoken;
    private $isLoggedIn = false;

    /**
     * User constructor.
     * @param string $user Username
     * @param string $auth Auth token, empty by default
     */
    function __construct($user, $auth="") {
        $this->username = $user;
        if ($auth !== "") {
            // User is trying to auth
            $this->authtoken = $auth;
            $this->isLoggedIn = $this->checkLoginToken($auth);
        }
    }

    /**
     * Checks to see if the user is logged in
     *
     * @return bool True if the user is logged in, false if they are not
     */
    public function isLoggedIn() {
        return $this->isLoggedIn;
    }

    /**
     * Sets the auth token as a cookie if password is correct
     *
     * @param $password Password of the user
     * @return bool     false if login unsuccessful
     */
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
	* @param string $password   Password of user to generate a hash for
	*
	* @return string            The valid login token. Returns false if the data was invalid.
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
	* @returns string   A random login token
	**/
	public function makeNewToken() {
		return md5((string)rand(0,100000));
	}
	
	/**
	* Check the login token to make sure it is in date, correct for the user, and still valid.
	*
	* @param string $token  Token you want to check if it is valid or not
	*
	* @return boolean       True if the token is valid, False if the token is valid, or does not match the UID supplied
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

    /**
     * Updates the users postcode to the specified value. Returns FALSE on failure.
     *
     * @param string $newPostcode   New postcode
     * @return bool                 True if the postcode was successfully updated, false if the user is not logged in or
     *                              the update failed
     */
	public function updatePostcode($newPostcode) {
		if ($this->isLoggedIn()) {
			try {
				$db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
				$stmt = $db->prepare("UPDATE user SET postcode = :postcode WHERE username = :username");
				$stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
				$stmt->bindValue(":postcode", htmlspecialchars($newPostcode, ENT_QUOTES), PDO::PARAM_STR);
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

    /**
     * Updates the user location to the specified lat/long. Returns FALSE on failure.
     *
     * @param $latitude     Latidude of the user
     * @param $longitude    Longitude of the user
     * @return bool         True if the update is successful, false if the user is not logged in or on update failure
     */
	public function updateLocation($latitude, $longitude) {
		if ($this->isLoggedIn()) {
			if (($latitude <= 90) && ($latitude >= -90) &&	($longitude <= 180) && ($longitude >= -180)
			) {
				try {
					$db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
					$stmt = $db->prepare("UPDATE user SET latitude = :lat, longitude = :long WHERE username = :username");
					$stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
					$stmt->bindValue(":lat", htmlspecialchars($latitude, ENT_QUOTES), PDO::PARAM_STR); // Yes, I know they are decimals but PDO uses stringy magic 
					$stmt->bindValue(":long", htmlspecialchars($longitude, ENT_QUOTES), PDO::PARAM_STR);
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

    /**
     * Resend the verification email to the email address listed in the user profile, and generate new keys and that.
     *
     * @return bool True on successful verification, false on failure
     */
	private function reverifyEmail() {
                try {
                        $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
                        // Make a new verify token
                        $key = $this->makeNewToken();
                        $stmt = $db->prepare("UPDATE user SET confirm_email_key = :key WHERE username = :username");
                        $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                        $stmt->bindValue(":key", $key, PDO::PARAM_STR);
                        $stmt->execute();
                        // Get user email
                        $stmt = $db->prepare("SELECT * FROM user WHERE username = :username");
                        $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                        $stmt->execute();
                        $row = $stmt->fetch();
                        $email = $row["email"];
                        // Send confirmation mail
                        mail($email, "Email Change", "Hey! \n \n You changed your email to this new address. Click the link below to confirm that you own this email address: \n \n <a href=\"".DOMAIN."confirm.php?key=".$key."&username=".$this->username."\">Confirm registration</a> \n \n Thanks, \n The FoodShare Team");
                        return true;
                } catch(PDOException $ex) {
                        return false;
                }
	}

    /**
     * Update the listed email address. Returns FALSE on failure. Also triggers a verification update.
     *
     * @param $email    User email address
     * @return bool     True if email is successfully updated, false if the user is not logged in or on failure
     */
    public function updateEmail($email) {
        if ($this->isLoggedIn()) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        try {
                                $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
                                $stmt = $db->prepare("UPDATE user SET verified = 0, email = :email WHERE username = :username");
                                $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                                $stmt->bindValue(":email", htmlspecialchars($email, ENT_QUOTES), PDO::PARAM_STR);
                                $stmt->execute();
                                if ($stmt->rowCount()) {
                                        $this->reverifyEmail();
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

    /**
     * Expires all login tokens for the user! This will effectively log them out everywhere.
     *
     * @return bool True on success, false if the user is not logged in or on failure
     */
    public function expireLoginTokens() {
            if ($this->isLoggedIn()) {
                    try {
                            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
                            $stmt = $db->prepare("DELETE FROM auth WHERE username = :username");
                            $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                            $stmt->execute();
                            return true;
                    } catch(PDOException $ex) {
                            return false;
                    }
            }
            return false;
    }


    /**
     * Changes the user password. Returns FALSE on failure. Will also expire all user tokens!
     *
     * @param $password User password
     * @return bool     True if the user password was changed successfully, false if the password is empty or less than
     *                  3 characters or the user is not logged in or on failure
     */
	public function updatePassword($password) {
                if ($this->isLoggedIn()) {
                        // Check for dumb cases
                        if (empty($password)) {
                                return false;
                        }
                        if (strlen($password) < 3) {
                                return false;
                        }
                        // Generate hash
                        $hash = password_hash($password, PASSWORD_BCRYPT);
                        // Actually change the pw
                        try {
                                $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
                                $stmt = $db->prepare("UPDATE user SET password = :pass WHERE username = :username");
                                $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                                $stmt->bindValue(":pass", $hash, PDO::PARAM_STR);
                                $stmt->execute();
                                if ($stmt->rowCount()) {
                                        $this->expireLoginTokens();
                                        return true;
                                }
                                return false;
                        } catch(PDOException $ex) {
                                return false;
                        }
                }
                return false;
        }

    /**
     * Returns the location stored in the database. Returns FALSE if not stored, or could not be retrieved.
     *
     * @return array|bool   Array containing the latitude and longitude of the location. Returns false if the user is
     *                      not logged in or on failure
     */
    public function getLocation() {
            if ($this->isLoggedIn()) {
                    try {
                            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
                            $stmt = $db->prepare("SELECT * FROM user WHERE username = :username");
                            $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                            $stmt->execute();
                            $row = $stmt->fetch();
                            // Check if lat/long are nonzero
                            // if long/lat are NULL then this is also evaluated due to type juggling
                            if (
                                    !(
                                            ($row["latitude"] == 0) &&
                                            ($row["longitude"] == 0)
                                    )
                            ) {
                                    return array((float)$row["latitude"],(float)$row["longitude"]);
                            }
                            return false;
                    } catch(PDOException $ex) {
                            return false;
                    }
            }
            return false;
    }

    /**
     * Returns the URL stored in the database.
     *
     * @return string|bool Profile picture url. Returns false on failure.
     */
    public function getProfilePicture() {
            try {
                    $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
                    $stmt = $db->prepare("SELECT profile_picture_url FROM user WHERE username = :username");
                    $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                    $stmt->execute();
                    $row = $stmt->fetch();
                    return $row["profile_picture_url"];
            } catch(PDOException $ex) {
                    return false;
            }
    }

    /**
     * Update the stored url to that of the parameter passed. Returns FALSE on failure.
     *
     * @param $url  URL of the picture
     * @return bool True if $url is successfully set, false if the user is not logged in or on failure.
     */
    public function updateProfilePictureURL($url) {
            if ($this->isLoggedIn()) {
                    // only accept imgur images
                    if (substr($url,0,19) === "http://i.imgur.com/") {
                            try {
                                    $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
                                    $stmt = $db->prepare("UPDATE user SET profile_picture_url = :url WHERE username = :username");
                                    $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                                    $stmt->bindValue(":url", htmlspecialchars($url, ENT_QUOTES), PDO::PARAM_STR);
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

    /**
     * Get the public profile of the user - username, profile URL, score
     *
     * @return bool|mixed   Public profile information. Returns false on failure
     */
    public function getPublicProfile() {
            try {
                    $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
                    $stmt = $db->prepare("SELECT username, score, profile_picture_url FROM user WHERE username = :username");
                    $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                    $stmt->execute();
                    $row = $stmt->fetch();
                    return $row;
            } catch(PDOException $ex) {
                    return false;
            }
    }

    /**
     * Get the private profile of the user. They need to be logged in for this.
     *
     * @return bool|mixed Private profile information. Returns false if the user is not logged in or on failure.
     */
    public function getPrivateProfile() {
            if ($this->isLoggedIn()) {
                    try {
                            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
                            $stmt = $db->prepare("SELECT username, email, postcode, latitude, longitude, score, profile_picture_url FROM user WHERE username = :username");
                            $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                            $stmt->execute();
                            $row = $stmt->fetch();
                            return $row;
                    } catch(PDOException $ex) {
                            return false;
                    }
            }
            return false;
    }

    /**
     * Checks to see if the user's profile has missing data
     *
     * @return bool|null    True if incomplete, null on failure
     */
	public function hasIncompleteProfile() {
            try {
                $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
                $stmt = $db->prepare("SELECT username, email, postcode, latitude, longitude, score, profile_picture_url FROM user WHERE username = :username");
                $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch();
                return (($row["latitude"] == 0) || ($row["longitude"] == 0) || empty($row["postcode"]) || empty($row["profile_picture_url"]));
            } catch(PDOException $ex) {
                    return null;
            }
	}

    /**
     * Checks to see if the user is verified
     *
     * @return null|int 1 if the user is verified, 0 if the user is not. Returns null on failure
     */
	public function isVerified() {
            try {
                $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
                $stmt = $db->prepare("SELECT verified FROM user WHERE username = :username");
                $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch();
                return $row["verified"];
            } catch(PDOException $ex) {
                    return null;
            }
	}
	
	/**
	* Generate a javascript object containing user details. 
	* @param private Default false. Whether to include private data (user has to be logged in)
	* @returns A UTF8 encoded JSON string containing the user details.
	**/
	public function getJSON($private = false) {
                $details = array();
                if ($private) {
                        $details = $this->getPrivateProfile();
                } else {
                        $details = $this->getPublicProfile();
                }
                if ($details !== false) {
                        return json_encode($details);
                } 
                return json_encode((object) null);
        }

    /**
     * Get the user's own food items that have been claimed
     *
     * @return array|null   Array of food items. Returns null on failure
     */
    public function getOwnedClaimedFoods() {
        try {
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $stmt = $db->prepare("SELECT * FROM food WHERE user_username = :username AND claimer_username != '' AND item_gone = b'0'");
            $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll();
            return $results;
        } catch(PDOException $ex) {
                return null;
        }
    }

    /**
     * Get the user's claimed food items
     *
     * @return array|null   Array of food items. Returns null on failure
     */
    public function getClaimedFoods() {
        try {
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $stmt = $db->prepare("SELECT * FROM food WHERE claimer_username = :username AND item_gone = b'0'");
            $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll();
            return $results;
        } catch(PDOException $ex) {
            return null;
        }
    }

    /**
     * Get the user's claim history
     *
     * @return array|null   Array of food items. Returns null on failure
     */
    public function getClaimHistory() {
        try {
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $stmt = $db->prepare("SELECT * FROM food WHERE claimer_username = :username");
            $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll();
            return $results;
        } catch(PDOException $ex) {
                return null;
        }
    }

    /**
     * Get the user's post history
     *
     * @return array|null   Array of food items. Returns null on failure
     */
    public function getPostHistory() {
        try {
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $stmt = $db->prepare("SELECT * FROM food WHERE user_username = :username");
            $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll();
            return $results;
        } catch(PDOException $ex) {
                return null;
        }
    }
        
        /**
        * Calculate the new user score based on items posted and collected. A item posted gets 10 points. A item posted that has been claimed gets an additional 10 points. 
        * A posted item, that has been claimed, that has been picked up gets another 30 points! No points for just claiming items though (As this could lead to abuse)
        * If you collect an item that someone else posted, you get 25 points. There is also a one time bonus of 25 points each for filling in a location or a profile picture.
        * This total is then stored in the database.
        *
        * @returns  array Point bonus totals ["history"][BONUS_TYPE], and the total score ["score"] as a dictionary.
        */
        public function updateScore() {
            try {
                $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
                $stmt = $db->prepare("SELECT * FROM food WHERE user_username = :username  OR claimer_username = :username");
                $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                $stmt->execute();
                $foodResults = $stmt->fetchAll();
                $stmt = $db->prepare("SELECT * FROM user WHERE username = :username");
                $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
                $stmt->execute();
                $userInfo = $stmt->fetch();
            } catch(PDOException $ex) {
                return false;
            }
            $score = 0;
            $return = array();
            $return["OWN"] = 0;
            $return["OWN_CLAIMED"] = 0;
            $return["OWN_GONE"] = 0;
            $return["PICKEDUP"] = 0;
            $return["PROFILE"] = 0;
            foreach($foodResults as $result) {
                $historyRow = $result;
                if ($result["user_username"] == $this->username) {
                    // User owns this item
                    $return["OWN"] += 10;
                    $score += 10;
                    if (!empty($result["claimer_username"])) {
                        $return["OWN_CLAIMED"] += 10;
                        $score += 10;
                        if ($result["item_gone"]) {
                            $return["OWN_GONE"] += 30;
                            $score += 30;
                        }
                    }
                } else if($result["claimer_username"] == $this->username) {
                    // User claimed this item
                    // No points for just claiming
                    if ($result["item_gone"]) {
                        $return["PICKEDUP"] += 25;
                        $score += 25;
                    }
                }
            }
            // Calculate one time bonuses
            if (!empty($userInfo["postcode"])) {
                $return["PROFILE"] += 25;
                $score += 25;
            }
            if (!empty($userInfo["profile_picture_url"])) {
                $return["PROFILE"] += 25;
                $score += 25;
            }
            if ($userInfo["verified"]) {
                $return["PROFILE"] += 25;
                $score += 25;
            }
            $return["score"] = $score;
            // Update the database with the new score
            $stmt = $db->prepare("UPDATE user SET score = :score WHERE username = :username");
            $stmt->bindValue(":username", $this->username, PDO::PARAM_STR);
            $stmt->bindValue(":score", $score, PDO::PARAM_INT);
            $stmt->execute();
            return $return;
        }
}
?>