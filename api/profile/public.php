<?php
define('__ROOT__',dirname(dirname(dirname(__FILE__))));
require_once __ROOT__.'/db.php';
require_once __ROOT__.'/class/User.class.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    if (!isset($_GET["username"])) {
        echo json_encode(array("error"=>"No username supplied"));
        return;
    }
    $user = new User($_GET["username"]);
    if (! ($user->getPublicProfile())) {
        echo json_encode(array("error"=>"User does not exist"));
        return;
    }
    echo json_encode($user->getPublicProfile());
}