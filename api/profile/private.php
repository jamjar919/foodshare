<?php
define('__ROOT__',dirname(dirname(dirname(__FILE__))));
require __ROOT__.'/db.php';
require __ROOT__.'/class/User.class.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (!isset($_COOKIE["username"])) {
        echo json_encode(array("error" => "Username not defined"));
        return;
    }
    if (!isset($_COOKIE["token"])) {
        echo json_encode(array("error" => "Token not defined"));
        return;
    }
    $user = new User($_COOKIE["username"],$_COOKIE["token"]);
    if ( ! $user->isLoggedIn()) {
        echo json_encode(array("error" => "Incorrect token/username combination"));
        return;
    }
    try {
        $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
        $stmt = $db->prepare("SELECT username, email, postcode, latitude, longitude, score, profile_picture_url FROM user WHERE username = :user");
        $stmt->bindValue(":user", $_COOKIE["username"], PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($result);
    } catch (PDOException $e) {
        echo json_encode(array("error" => "Database error, please try again later."));
        return;
    }
}

