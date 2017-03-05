<?php
//TODO database connection file required
header('Content-Type: application/json');

if (isset($_GET['submit'])) {
    $query = $_GET['q'];
    $location = $_GET['location'];
    $distance = $_GET['distance'];
    $sort = $_GET['sort'];
    $num = $_GET['num'];
    $offset = $_GET['offset'];
    get_food_listing($query, $location, $distance, $sort, $num, $offset);
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

function get_food_listing($query, $location, $distance, $sort, $num, $offset)
{
    switch ($sort) {
        //alphabetical
        case 'az':
            try {
                $stmt = $this->db->prepare("SELECT *, COUNT(*) AS tag_count, ( 3959 * acos( cos( radians(:center_lat) ) 
                * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:center_lng) )
 + sin( radians(:center_lat) ) * sin( radians( latitude ) ) ) ) AS distance FROM food HAVING distance < :distance  
 ORDER BY `name` ASC LIMIT :offset , :num;");
                $stmt->bindValue(":center_lat", $location[0], PDO::PARAM_INT);
                $stmt->bindValue(":center_lng", $location[1], PDO::PARAM_INT);
                $stmt->bindValue(":distance", $distance, PDO::PARAM_INT);
                $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
                $stmt->bindValue(":num", $num, PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll();
                $food = array(
                    "food" => $results
                );
                echo json_decode($food);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
        //location
        case 'loc':
            try {
                $stmt = $this->db->prepare("SELECT *, ( 3959 * acos( cos( radians(:center_lat) ) * 
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
                $results = $stmt->fetchAll();
                $food = array(
                    "food" => $results
                );
                echo json_decode($food);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
        //best match
        case 'bm':
            //need to include search by tags some how rather than just name and description
            try {
                $stmt = $this->db->prepare("SELECT *, ( 3959 * acos( cos( radians(:center_lat) ) * 
cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:center_lng) )
  + sin( radians(:center_lat) ) * sin( radians( latitude ) ) ) ) AS distance FROM food HAVING distance < :distance 
  WHERE MATCH(`name`, desciption) AGAINST (':query' IN BOOLEAN MODE)
 LIMIT :offset , :num;");
                $stmt->bindValue(":center_lat", $location[0], PDO::PARAM_INT);
                $stmt->bindValue(":center_lng", $location[1], PDO::PARAM_INT);
                $stmt->bindValue(":distance", $distance, PDO::PARAM_INT);
                $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
                $stmt->bindValue(":num", $num, PDO::PARAM_INT);
                $stmt->bindValue(":query", $query, PDO::PARAM_STR);
                $stmt->execute();
                $results = $stmt->fetchAll();
                $food = array(
                    "food" => $results
                );
                echo json_decode($food);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
        //time
        case 'dt':
            try {
                $stmt = $this->db->prepare("SELECT *, ( 3959 * acos( cos( radians(:center_lat) ) * 
cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:center_lng) )
+ sin( radians(:center_lat) ) * sin( radians( latitude ) ) ) ) AS distance FROM food HAVING distance < :distance 
 WHERE MATCH(`name`, desciption) AGAINST (':query' IN BOOLEAN MODE) ORDER BY `datetime` DESC LIMIT :offset , :num;");
                $stmt->bindValue(":center_lat", $location[0], PDO::PARAM_INT);
                $stmt->bindValue(":center_lng", $location[1], PDO::PARAM_INT);
                $stmt->bindValue(":distance", $distance, PDO::PARAM_INT);
                $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
                $stmt->bindValue(":num", $num, PDO::PARAM_INT);
                $stmt->bindValue(":query", $query, PDO::PARAM_STR);
                $stmt->execute();
                $results = $stmt->fetchAll();
                $food = array(
                    "food" => $results
                );
                echo json_decode($food);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
        case 'exp':
            try {
                $stmt = $this->db->prepare("SELECT *, ( 3959 * acos( cos( radians(:center_lat) ) * 
cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:center_lng) )
 + sin( radians(:center_lat) ) * sin( radians( latitude ) ) ) ) AS distance FROM food HAVING distance < :distance 
 WHERE MATCH(`name`, desciption) AGAINST (':query' IN BOOLEAN MODE) ORDER BY `expiry` DESC LIMIT :offset , :num;");
                $stmt->bindValue(":center_lat", $location[0], PDO::PARAM_INT);
                $stmt->bindValue(":center_lng", $location[1], PDO::PARAM_INT);
                $stmt->bindValue(":distance", $distance, PDO::PARAM_INT);
                $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
                $stmt->bindValue(":num", $num, PDO::PARAM_INT);
                $stmt->bindValue(":query", $query, PDO::PARAM_STR);
                $stmt->execute();
                $results = $stmt->fetchAll();
                $food = array(
                    "food" => $results
                );
                echo json_decode($food);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            break;
    }
}





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