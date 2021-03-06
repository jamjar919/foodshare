<?php

define('__ROOT__',dirname(dirname(__FILE__)));
require_once __ROOT__.'/db.php';
require_once __ROOT__.'/class/Food.class.php';
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] === "POST") {
    $response = array();
    if (!isset($_POST["id"])) {
        $response["error"] = "Id of food not provided";
        echo json_encode($response);
        return;
    }
    if (!isset($_POST["val"])) {
        $response["error"] = "val not provided";
        echo json_encode($response);
        return;
    }
    $val = ($_POST["val"] == true);
    $id = $_POST['id'];
    $f = new Food($id);
    if ($val) {
        if ($f->markAsGone()) {
            $response['message'] = "Food successfully marked as gone.";
            $response['success'] = true;
        } else {
            $response['error'] = "Failed to modify food";
            $response['success'] = false;
        }
    } else {
        if ($f->unmarkAsGone()) {
            $response['message'] = "Food successfully unmarked as gone.";
            $response['success'] = true;
        } else {
            $response['error'] = "Failed to modify food";
            $response['success'] = false;
        }
    }
    echo json_encode($response);
}