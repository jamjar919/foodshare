<?php
ini_set('display_errors',1);  error_reporting(E_ALL);
define("DOMAIN", "http://community.dur.ac.uk/cs.seg01/foodshare/");
require 'db.php';
require 'lib/password.php'; // Password hashing library

/**
* Translate a username to a UID
*
* @param string $username The username of the user you want to get a UID for
*
* @return integer The UID of the username supplied
**/
function getUid($username) {
    try {
        $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
        $stmt = $db->prepare("SELECT * FROM user WHERE username = :name");
        $stmt->bindValue(":name", $username, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!empty($result)) {
            return $result["uid"];
        }
        return false;
    } catch(PDOEXCEPTION $e) {
        // We errored out. 
        return false;
    }
}


/**
* Check if a user is logged in. Mainly a wrapper for checkLoginToken
*
* @param array $s The session variable of the user - passing $_SESSION to this function is recommended. The session variable should have the "token" and "uid" properties
*
* @return boolean True if the user is logged in, False if the user is not
**/
function isLoggedIn($s) {
    if (isset($s["token"])) {
        if (isset($s["uid"])) {
            try {
                $value = checkLoginToken($s["uid"],$s["token"]);
                return $value;
            } catch(PDOEXCEPTION $e) {
                // We errored out. 
                return false;
            }
        }
    } 
    return false;
}

/**
* Check the login token to make sure it is in date, correct for the user, and still valid.
*
* @param string $uid User id of the user you want to check
* @param string $token Token you want to check if it is valid or not
*
* @return boolean True if the token is valid, False if the token is valid, or does not match the UID supplied
**/
function checkLoginToken($uid, $token) {
    $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
    $stmt = $db->prepare("SELECT * FROM auth WHERE uid = :uid");
    $stmt->bindValue(":uid", $uid, PDO::PARAM_INT);
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
                $delstmt = $db->prepare("DELETE FROM table WHERE aid=:aid");
                $delstmt->bindValue(":aid", $result["aid"], PDO::PARAM_INT);
                $delstmt->execute();
            }
        }
    }
    return false;
}

/**
* Generate a login token string, given a valid username and password. Checks against the database to make sure the password matches the one on record. Returns false otherwise
*
* @param string $username Username of user to generate a hash for
* @param string $password Password of user to generate a hash for
*
* @return string The valid login token. Returns false if the data was invalid.
*/
function generateLoginToken($username, $password) {
    $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
    if (empty($username) || empty($password)) {
        return false;
    }
    try {
        $stmt = $db->prepare("SELECT * FROM user WHERE username = :username");
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOEXCEPTION $e) {
        // We errored out. 
        return false;
    }
    $hash = $result["password"];
    if (password_verify($password, $hash)) {
        // Valid! Make us a token!
        $token = makeNewToken();
        $expiry = time() + (7*24*60*60);
        $uid = getUid($username);
        // Store it in the database, with expiry date 1 week in the future.
        $stmt = $db->prepare("INSERT INTO auth(authkey,uid,expiry) VALUES(:token,:uid,:expiry)");
        $stmt->execute(array(':token' => $token, ':uid' => $uid, ':expiry' => $expiry));
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
function makeNewToken() {
    return md5((string)rand(0,100000));
}

/**
* Register a user with the database
*
* @param $username The desired username
* @param $email The email address of the user
* @param $password The password of the user (plaintext)
* @param $confirmpassword The confimation password of the user
* @param $name The real name of the user
* @param $postcode The postcode of the users location
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
        $hash = password_hash($password);
        $key = makeNewToken();
        $stmt = $db->prepare("INSERT INTO `user` (`uid`, `username`, `email`, `password`, `name`, `postcode`, `verified`, `confirm_email_key`) VALUES (NULL, :username, :email, :hash, NULL, NULL, 0, :key);");
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
    mail($email, "Confirmation of FoodShare signup", "Hey! \n \n You (or an imposter) signed up to foodShare under this email address. If you recieved this email in error, ignore this message. Else, click the link below: \n \n <a href=\"".DOMAIN."confirm.php?key=".$key."\">Confirm registration</a> \n \n Thanks, \n The FoodShare Team");
    return true;
}