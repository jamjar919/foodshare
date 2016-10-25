<?php
ini_set('display_errors',1);  error_reporting(E_ALL);

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
        $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8mb4', DBUSER, DBPASS);
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
    $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8mb4', DBUSER, DBPASS);
    $stmt = $db->prepare("SELECT * FROM auth WHERE uid = :uid");
    $stmt->bindValue(":uid", $uid, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (($result["authkey"] == $token) && ($result["uid"] == $uid)) {
        // Token is valid, is it expired?
        if (time() < $result["expiry"]) {
            // Token is still in date and everything is valid, sweet.
            return true;
        } else {
            // The key is correct, but out of date. We should delete it from the records
            $stmt = $db->prepare("DELETE FROM table WHERE aid=:aid");
            $stmt->bindValue(":aid", $result["aid"], PDO::PARAM_INT);
            $stmt->execute();
            return false;
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
    $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8mb4', DBUSER, DBPASS);
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