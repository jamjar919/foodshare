<?php
//TODO database connection file required
require '../db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (!isset($_GET['location'])) {
        echo json_encode(array("error" => "Location not defined"));
} else {
        //TODO predefine undefined parameters
        //make location required parameter
        $query = $_GET['q'];
        if(!isset($_GET['q'])) {
            $query = "";
        }
        $sort = $_GET['sort'];
        if(!isset($_GET['sort'])) {
            $sort = "bm";
        }
        $location = $_GET['location'];
        $distance = $_GET['distance'];
        if(!isset($_GET['distance'])) {
            $distance = 20;
        }


        $num = (int)$_GET['num'];
        $offset = (int)$_GET['offset'];
        get_food_listing($query, $location, $distance, $sort, $num, $offset);
    }
}


//TODO include use of tags in best match search
//check if item tags for each item in the searched tags then order them by the most matches and then sort
//the items with the same number of matches by the chosen sort method?
//Do we want to have priority tags?
//Search using keywords and tags?
/*
 * SELECT *, COUNT(*) AS tag_count, group_concat(t.name) AS tags, ( 3959 * acos( cos( radians(:center_lat) ) *
 * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:center_lng) )
 * + sin( radians(:center_lat) ) * sin( radians( latitude ) ) ) ) AS distance FROM food
 * INNER JOIN tag_list ON tag_list.food_id = food.id
 * INNER JOIN tag t ON t.id = tag_list.tag_id
 * WHERE t.name IN $tagList
 * HAVING distance < :distance
 * ORDER BY tag_count DESC
 */


/**
 * Return json object containing food items sorted and filtered based on the user's search
 *
 * @param string $query Keywords entered by user
 * @param array $location Central location
 * @param int $distance Max distance from the central location to food items
 * @param string $sort Sort type
 * @param int $num Number of food items displayed per page
 * @param int $offset Page offset
 */
function get_food_listing($query, $location, $distance, $sort, $num, $offset)
{
    $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
    $words = strtolower($query);
    switch ($sort) {
        //alphabetical
        case 'az':
            try {
                $stmt = $db->prepare("SELECT *, ( 6371 * acos( cos( radians(:center_lat) ) * 
                cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:center_lng) )
 + sin( radians(:center_lat) ) * sin( radians( latitude ) ) ) ) AS distance FROM food HAVING distance < :distance   
 ORDER BY `name` ASC LIMIT :offset, :num");
                $stmt->bindValue(":center_lat", $location[0], PDO::PARAM_INT);
                $stmt->bindValue(":center_lng", $location[1], PDO::PARAM_INT);
                $stmt->bindValue(":distance", $distance, PDO::PARAM_INT);
                $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
                $stmt->bindParam(':num', $num, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $food = array(
                    "food" => $results
                );
                echo json_encode($food);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
        //location
        case 'loc':
            try {
                $stmt = $db->prepare("SELECT *, ( 6371 * acos( cos( radians(:center_lat) ) * 
                cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:center_lng) )
 + sin( radians(:center_lat) ) * sin( radians( latitude ) ) ) ) AS distance FROM food HAVING distance < :distance
 ORDER BY distance ASC
 LIMIT :offset , :num;");
                $stmt->bindValue(":center_lat", $location[0], PDO::PARAM_INT);
                $stmt->bindValue(":center_lng", $location[1], PDO::PARAM_INT);
                $stmt->bindValue(":distance", $distance, PDO::PARAM_INT);
                $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
                $stmt->bindValue(":num", $num, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $food = array(
                    "food" => $results
                );
                echo json_encode($food);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
        //best match
        case 'bm':
            //need to include search by tags some how rather than just name and description
            try {

                $stmt = $db->prepare("SELECT f.id, f.name, f.description, f.image_url, f.expiry, f.time, f.latitude,
f.longitude, f.user_username, f.claimer_username, ( 6371 * acos( cos( radians(:center_lat) ) * 
cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:center_lng) )
  + sin( radians(:center_lat) ) * sin( radians( latitude ) ) ) ) AS distance FROM food AS f
  INNER JOIN tag_list ON tag_list.id = f.tag_list_id
 INNER JOIN tag t ON t.id = tag_list.tag_id
 WHERE MATCH(f.name, f.description, t.name) 
  AGAINST ('$words' IN BOOLEAN MODE) HAVING distance < :distance  
 LIMIT :offset , :num;");
                $stmt->bindValue(":center_lat", $location[0], PDO::PARAM_INT);
                $stmt->bindValue(":center_lng", $location[1], PDO::PARAM_INT);
                $stmt->bindValue(":distance", $distance, PDO::PARAM_INT);
                $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
                $stmt->bindValue(":num", $num, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $food = array(
                    "food" => $results
                );
                echo json_encode($food);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
        //time
        case 'dt':
            try {
                $stmt = $db->prepare("SELECT f.id, f.name, f.description, f.image_url, f.expiry, f.time, f.latitude,
f.longitude, f.user_username, f.claimer_username,  ( 6371 * acos( cos( radians(:center_lat) ) * 
cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:center_lng) )
+ sin( radians(:center_lat) ) * sin( radians( latitude ) ) ) ) AS distance FROM food AS f
INNER JOIN tag_list ON tag_list.id = f.tag_list_id
 INNER JOIN tag t ON t.id = tag_list.tag_id
 WHERE MATCH(f.name, f.description, t.name) 
  AGAINST ('$words' IN BOOLEAN MODE) HAVING distance < :distance ORDER BY f.time DESC LIMIT :offset , :num;");
                $stmt->bindValue(":center_lat", $location[0], PDO::PARAM_INT);
                $stmt->bindValue(":center_lng", $location[1], PDO::PARAM_INT);
                $stmt->bindValue(":distance", $distance, PDO::PARAM_INT);
                $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
                $stmt->bindValue(":num", $num, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $food = array(
                    "food" => $results
                );
                echo json_encode($food);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
        case 'exp':
            try {
                $stmt = $db->prepare("SELECT f.id, f.name, f.description, f.image_url, f.expiry, f.time, f.latitude,
f.longitude, f.user_username, f.claimer_username,  ( 6371 * acos( cos( radians(:center_lat) ) * 
cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:center_lng) )
 + sin( radians(:center_lat) ) * sin( radians( latitude ) ) ) ) AS distance FROM food AS f  
 INNER JOIN tag_list ON tag_list.id = f.tag_list_id
 INNER JOIN tag t ON t.id = tag_list.tag_id
 WHERE MATCH(f.name, f.description, t.name) 
  AGAINST ('$words' IN BOOLEAN MODE) HAVING distance < :distance ORDER BY f.expiry DESC LIMIT :offset , :num;");
                $stmt->bindValue(":center_lat", $location[0], PDO::PARAM_INT);
                $stmt->bindValue(":center_lng", $location[1], PDO::PARAM_INT);
                $stmt->bindValue(":distance", $distance, PDO::PARAM_INT);
                $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
                $stmt->bindValue(":num", $num, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $food = array(
                    "food" => $results
                );
                echo json_encode($food);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
    }
}



/*

echo json_encode(array(
    "food" => array(
        array(
            "id" => 1,
            "name" => "Prote",
            "description" => "Itaque rerum qui aut quia voluptas officiis omnis. Beatae id itaque nemo. Architecto dolores laborum nostrum rerum iusto non non ea. Culpa sit ab est.
            Fugit at hic fuga occaecati. Et ipsa odit maiores. Et ad facilis tempore aliquid velit. Voluptatem facere natus fugit nemo consequatur a nihil quia.
            Aliquid fugit expedita doloremque minus itaque qui et. Provident earum doloribus ut soluta et itaque. Beatae ea velit rerum qui qui omnis.",
            "latitude" => 54.778687,
            "longitude" => -1.560531
        ),
        array(
            "id" => 4,
            "name" => "Cathedral City Cheese",
            "description" => "Itaque rerum qui aut quia voluptas officiis omnis. Beatae id itaque nemo. Architecto dolores laborum nostrum rerum iusto non non ea. Culpa sit ab est.
            Fugit at hic fuga occaecati. Et ipsa odit maiores. Et ad facilis tempore aliquid velit. Voluptatem facere natus fugit nemo consequatur a nihil quia.
            Aliquid fugit expedita doloremque minus itaque qui et. Provident earum doloribus ut soluta et itaque. Beatae ea velit rerum qui qui omnis.",
            "latitude" => 54.773019,
            "longitude" => -1.576281
        ),
        array(
            "id" => 90,
            "description" => "Itaque rerum qui aut quia voluptas officiis omnis. Beatae id itaque nemo. Architecto dolores laborum nostrum rerum iusto non non ea. Culpa sit ab est.
            Fugit at hic fuga occaecati. Et ipsa odit maiores. Et ad facilis tempore aliquid velit. Voluptatem facere natus fugit nemo consequatur a nihil quia.
            Aliquid fugit expedita doloremque minus itaque qui et. Provident earum doloribus ut soluta et itaque. Beatae ea velit rerum qui qui omnis.",
            "name" => "Yum Sandwiches",
            "latitude" => 54.76985,
            "longitude" => -1.569843
        )
    ),
    "get" => $_GET
));
*/