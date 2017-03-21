<?php

define('__ROOT__',dirname(__FILE__));
require __ROOT__.'/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    //make location required parameter
    if (!isset($_GET['location'])) {
        echo json_encode(array("error" => "Location not defined"));
} else {

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

        $expiry = $_GET['expiry'];
        if($expiry != "Any time") {
            $expiry[0] = date("Y-m-d", strtotime(str_replace('/', '-', $expiry[0])));
            $expiry[1] = date("Y-m-d", strtotime(str_replace('/', '-', $expiry[1])));
        }
        $time = $_GET['time'];
        if($time != "Any time") {
            $time[0] = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $time[0])));
            $time[1] = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $time[1])));
        }

        $num = (int)$_GET['num'];
        $offset = (int)$_GET['offset'];
        getFoodListing($query, $location, $distance, $expiry, $time, $sort, $num, $offset);
    }
}

/**
 * Return json object containing food items sorted and filtered based on the user's search
 *
 * @param string $query Keywords entered by user
 * @param array $location Central location
 * @param int $distance Max distance from the central location to food items
 * @param array $expiry expiry range
 * @param array $time time posted range
 * @param string $sort Sort type
 * @param int $num Number of food items displayed per page
 * @param int $offset Page offset
 */
function getFoodListing($q, $location, $distance, $expiry, $time, $sort, $num, $offset)
{
    $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
    $words = strtolower($q);

    $query = "SELECT f.id, f.name, f.description, f.image_url, f.expiry, f.time, f.latitude,
 f.longitude, f.user_username, f.claimer_username, f.tag_list_id, ( 6371 * acos( cos( radians(:center_lat) ) * 
                cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:center_lng) )
 + sin( radians(:center_lat) ) * sin( radians( latitude ) ) ) ) AS distance FROM food AS f ";
    //check if expiry date and time is empty for any time filter
    if (!$words == "" or $sort == "Best match") {
        $query .= "INNER JOIN tag_list ON tag_list.id = f.tag_list_id
        INNER JOIN tag t ON t.id = tag_list.tag_id
        WHERE MATCH(f.name, f.description, t.name) 
        AGAINST ('$words' IN BOOLEAN MODE) ";
    }

    if(!$expiry == "Any time") {
        $query .= "AND f.expiry BETWEEN '$expiry[0]' AND '$expiry[1]'";
    }
    if(!$time == "Any time") {
        $query .= "AND f.time BETWEEN '$time[0]' AND '$time[1]'";
    }
    $query .= "HAVING distance < :distance ";
    switch ($sort) {
        //alphabetical
        case 'Alphabetical':
            try {
                $query .= "ORDER BY `name` ASC LIMIT :offset, :num";
                $stmt = $db->prepare($query);
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
        case 'Closest':
            try {
                $query .= "ORDER BY distance ASC LIMIT :offset , :num;";
                $stmt = $db->prepare($query);
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
        case 'Best match':
            //need to include search by tags some how rather than just name and description
            try {
                $query .= "LIMIT :offset, :num;";

                $stmt = $db->prepare($query);
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
        case 'Most recent':
            try {
                $query .= "ORDER BY f.time DESC LIMIT :offset , :num;";
                $stmt = $db->prepare($query);
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
        case 'Expiry':
            try {
                $query .= "ORDER BY f.expiry DESC LIMIT :offset , :num;";
                $stmt = $db->prepare($query);
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