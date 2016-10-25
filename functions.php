<?php

require 'db.php';

function isLoggedIn($s) {
    if (isset($s["token"])) {
        if (isset($s["uid"])) {
            try {
                return checkLoginToken($s["uid"],$s["token"]);
            } catch(PDOEXCEPTION $e) {
                // We errored out. 
                return false;
            }
        }
    } 
    return false;
}

function checkLoginToken($uid, $token) {
    $stmt = $db->query("SELECT * FROM auth WHERE uid = :uid");
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