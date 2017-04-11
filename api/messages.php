<?php

define('__ROOT__',dirname(dirname(__FILE__)));
require __ROOT__.'/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (isset($_GET['conv_id'])){
        $conv_id = $_GET['conv_id'];
        getConversation($conv_id);
    } else if (isset($_GET['id'])){
        $id = $_GET['id'];
        getMessage($id);
    } else {
        echo json_encode(array("error" => "no conversation or message id"));
    }

}else if($_SERVER['REQUEST_METHOD'] == "POST"){
    if (empty($_POST["id"]) || empty($_POST["conversation_id"]) || empty($_POST["text"])  || empty($_POST["read"]) || empty($_POST["message_type"])) {
        echo "Missing parameter(s)";
    } else {
        addMessage($_POST["id"],$_POST["conversation_id"],$_POST["text"],$_POST["read"],$_POST["message_type"]);
    }
}

function getConversation($conv_id){
    $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS); 
    $stmt = $db->prepare("SELECT m.id, m.conversation_id, m.text, m.time, m.read, m.message_type FROM message as m WHERE MATCH(`conversation_id`) AGAINST ('$conv_id*' IN BOOLEAN MODE) ORDER BY m.time");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $messages = array(
        "messages" => $results
    );
    echo json_encode($messages);
}

function getMessage($id){
    $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS); 
    $stmt = $db->prepare("SELECT m.id, m.conversation_id, m.text, m.time, m.read, m.message_type FROM message as m WHERE MATCH(`id`) AGAINST ('$id*' IN BOOLEAN MODE)");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $messages = array(
                "messages" => $results
            );
    echo json_encode($messages);
}

function addMessage($id, $conversation_id, $text, $read, $message_type){
    try {
        $stmt = $db->prepare("INSERT INTO `message` (`id`, `conversation_id`, `text`, `time`, `read`, `message_type`) VALUES (:id, :conversation_id, :text, NOW(), :read, :message_type);");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':conversation_id', $conversation_id, PDO::PARAM_INT);
        $stmt->bindValue(':text', $text, PDO::PARAM_STR);
        $stmt->bindValue(':read', $read, PDO::PARAM_BOOL);
        $stmt->bindValue(':message_type', $message_type, PDO::PARAM_INT);
        $stmt->execute();
    } catch(PDOEXCEPTION $e) {
        echo "There was a database error, please try again later.";
    }
}

?>