<?php

define('__ROOT__',dirname(dirname(__FILE__)));
require_once __ROOT__.'/db.php';
require_once __ROOT__.'/class/Food.class.php';
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] === "POST") {
    $response = array();
    if (!isset($_POST["id"]) || !isset($_POST["claimer"])) {
        $response["error"] = "id of food or username of claimer not provided";
    } else {
        $id = $_POST['id'];
        $claimer = $_POST['claimer'];
        $f = new Food($id);
        if(empty($f->item["claimer_username"])) {
            if ($f->claim()) {
                $response['message'] = "Food successfully claimed";
                $response['success'] = true;
            } else {
                $response['error'] = "Failed to claim food";
                $response['success'] = false;
            }
        }
        else {
            $response['claimed'] = "Sorry, the food has already been claimed";
        }
    }
    echo json_encode($response);
}