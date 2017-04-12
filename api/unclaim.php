<?php

define('__ROOT__',dirname(dirname(__FILE__)));
require_once __ROOT__.'/db.php';
require_once __ROOT__.'/class/Food.class.php';
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] === "POST") {
    $response = array();
    if (!isset($_POST["id"])) {
        $response["error"] = "ID of food not provided";
    } else {
        $id = $_POST['id'];
        $f = new Food($id);
        if(!empty($f->item["claimer_username"])) {
            if ($f->unclaim()) {
                $response['message'] = "Food successfully unclaimed";
                $response['success'] = true;
            } else {
                $response['error'] = "Failed to unclaim food";
                $response['success'] = false;
            }
        }
        else {
            $response['claimed'] = "Food not claimed!";
        }
    }
    echo json_encode($response);
}