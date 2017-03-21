<?php

define('__ROOT__',dirname(__FILE__));
require __ROOT__.'/db.php';
header('Content-Type: application/json');

$q = $_GET['q'];
$db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
$stmt = $db->prepare("SELECT `name`, MATCH(`name`) AGAINST ('$q*' IN BOOLEAN MODE) AS relevance FROM tag 
WHERE MATCH(`name`) AGAINST ('$q*' IN BOOLEAN MODE) ORDER BY relevance DESC LIMIT 6");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode(array('tags' => $result));

?>