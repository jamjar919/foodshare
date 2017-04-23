<?php

/**
 * Food search API
 */

define('__ROOT__',dirname(dirname(__FILE__)));
require __ROOT__.'/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    //make location required parameter
    if (!isset($_GET['location']) || $_GET['location'] === "") {
        echo json_encode(array("error" => "Location not defined"));
} else {

        $query = strip_tags($_GET['q']);
        $sort = strip_tags($_GET['sort']);
        $location = explode(",", strip_tags($_GET['location']));
        if(sizeof($location) != 2) {
            echo json_encode(array("error" => "Latitude and longitude required"));
        }

        $distance = strip_tags($_GET['distance']);

        $expiry = strip_tags($_GET['expiry']);
        if($expiry != "Any time") {
            $expiry = explode(",", $expiry);
            $expiry[0] = date("Y-m-d", strtotime(str_replace('/', '-', $expiry[0])));
            $expiry[1] = date("Y-m-d", strtotime(str_replace('/', '-', $expiry[1])));
        }
        $time = strip_tags($_GET['time']);
        if($time != "Any time") {
            $time = explode(",", $time);
            $time[0] = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $time[0])));
            $time[1] = date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $time[1])));
        }

        $num = (int)strip_tags($_GET['num']);
        $offset = (int)strip_tags($_GET['offset']);
        getFoodListing($query, $location, $distance, $expiry, $time, $sort, $num, $offset);
    }
}

/**
 * Return json object containing food items filtered then sorted based on the user's search
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

    $query = "SELECT DISTINCT f.id, f.name, f.description, f.image_url, f.expiry, f.time, f.latitude,
 f.longitude, f.user_username, f.claimer_username, ( 6371 * acos( cos( radians(:center_lat) ) * 
                cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:center_lng) )
 + sin( radians(:center_lat) ) * sin( radians( latitude ) ) ) ) AS distance FROM food AS f ";
    //check if expiry date and time is empty for any time filter

    if (($q != "" && !ctype_space($q)) || $sort == "Best match") {
        $query .= "INNER JOIN tag_list ON tag_list.id = f.tag_list_id
        INNER JOIN tag t ON t.id = tag_list.tag_id
        WHERE MATCH(f.name, f.description, t.name) 
        AGAINST ('$q' IN BOOLEAN MODE) AND (f.claimer_username = NULL OR f.claimer_username = '') ";
    }
    else {
        $query .= "WHERE (f.claimer_username = NULL OR f.claimer_username = '') ";
    }

    if($expiry != "Any time") {
        $query .= "AND f.expiry BETWEEN '$expiry[0]' AND '$expiry[1]'";
    }
    if($time != "Any time") {
        $query .= "AND f.time BETWEEN '$time[0]' AND '$time[1]'";
    }

    $query .= "HAVING distance < :distance ";
    $countQuery = "SELECT COUNT(*) AS resultsCount FROM (" . $query . ") countTable";

    switch ($sort) {
        //alphabetical
        case 'Alphabetical':
            try {
                $query .= "ORDER BY f.name ASC LIMIT :offset, :num";
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
        //location
        case 'Distance: closest first':
            try {
                $query .= "ORDER BY distance ASC LIMIT :offset , :num;";
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
        //best match
        case 'Best match':
            //need to include search by tags some how rather than just name and description
            try {
                $query = str_replace("SELECT DISTINCT", "SELECT DISTINCT id, name, description, image_url,
                expiry, time, latitude, longitude, user_username, claimer_username FROM (SELECT MATCH(t.name) 
        AGAINST ('$q') AS score1, MATCH(f.name) AGAINST ('$q') AS score2, MATCH(f.description) AGAINST ('$q') AS score3,", $query);
                $query .= " ORDER BY score1 + score2 + score3 DESC LIMIT :offset, :num) bestMatch";
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
        //time
        case 'Time: newest first':
            try {
                $query .= "ORDER BY f.time DESC LIMIT :offset , :num;";
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
        case 'Time: oldest first':
            try {
                $query .= "ORDER BY f.time ASC LIMIT :offset , :num;";
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;

        case 'Expiry: latest':
            try {
                $query .= "ORDER BY f.expiry DESC LIMIT :offset , :num;";
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
        case 'Expiry: earliest':
            try {
                $query .= "ORDER BY f.expiry ASC LIMIT :offset , :num;";
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
    }
    try {
        $stmt = $db->prepare($query);
        $stmt->bindValue(":center_lat", $location[0], PDO::PARAM_INT);
        $stmt->bindValue(":center_lng", $location[1], PDO::PARAM_INT);
        $stmt->bindValue(":distance", $distance, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindValue(":num", $num, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $food = array(
            "food" => $results,
            "resultsCount" => ""
        );

        $stmt2 = $db->prepare($countQuery);
        $stmt2->bindValue(":center_lat", $location[0], PDO::PARAM_INT);
        $stmt2->bindValue(":center_lng", $location[1], PDO::PARAM_INT);
        $stmt2->bindValue(":distance", $distance, PDO::PARAM_INT);
        $stmt2->execute();
        $resultsCount = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        $food["resultsCount"] = $resultsCount[0]['resultsCount'];
        echo json_encode($food);
    }catch (PDOException $e) {
        echo $e->getMessage();
    }
}