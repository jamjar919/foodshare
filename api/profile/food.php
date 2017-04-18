<?php
/**
Return all food items from a user, sorted by the sort parameter
**/
define('__ROOT__',dirname(dirname(dirname(__FILE__))));
require __ROOT__.'/db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === "GET") {
    if (!isset($_GET['username'])) {
        echo json_encode(array("error" => "Username not sent"));
        return;
    }
    $username = $_GET['username'];
    $sort = "Most recent";
    if (isset($_GET['sort'])) {
        $sort = $_GET['sort'];
    }
    $num = 10;
    if (isset($_GET['num'])) {
        $num = intval($_GET['num']);
    }
    $query = "SELECT * FROM food WHERE user_username = :user AND item_gone = b'0'";
    switch($sort) {
        case 'Expiry':
            $query .= " ORDER BY expiry DESC";
            break;
        case 'Most recent':
            $query .= " ORDER BY time ASC";
            break;
        case 'Alphabetical':
            $query .= " ORDER BY name ASC";
            break;
        default:
            echo json_encode(array("error" => "Unrecognised sort parameter"));
            return;
    }
    $query .= " LIMIT :num";
    try {
        $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
        $stmt = $db->prepare($query);
        $stmt->bindValue(":user", $username, PDO::PARAM_STR);
        $stmt->bindValue(":num", $num, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $food = array(
            "food" => $results
        );
        echo json_encode($food);
    } catch (PDOException $e) {
        echo json_encode(array("error" => "Database error, please try again later."));
        return;
    }
}