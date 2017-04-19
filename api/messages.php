<?php

define('__ROOT__',dirname(dirname(__FILE__)));
require __ROOT__.'/db.php';
require_once __ROOT__.'/class/Messages.class.php';
header('Content-Type: application/json');

$m = new Messages();

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (isset($_GET['user'])){
        $to = $_GET['user'];
        getConversation($to);
    } else {
        getConversationList();
    }
} else if($_SERVER['REQUEST_METHOD'] == "POST"){
    if (!isset($_POST["from"]) ||!isset($_POST["sendTo"]) || !isset($_POST["text"])  || !isset($_POST["read"]) || !isset($_POST["message_type"])) {
        echo "Missing parameter(s)";
    } else {
        addMessage($_POST["from"],$_POST["sendTo"],$_POST["text"],$_POST["read"],$_POST["message_type"]);
    }
}

function getConversation($user2){
    global $m;
    $results = $m->getMessagesWith($user2);
    $messages = array(
        "messages" => $results
    );
    echo json_encode($messages);
}

function getConversationList() {
    global $m;
    $results = $m->getConversations();
    $messages = array(
        "conversations" => $results
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

function getConversationID($from, $to){
	//if conversation exists get the id, else create the conversation
	$db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
	$stmt = $db->prepare("
	SELECT conversation.id FROM `conversation` WHERE (conversation.sender_username = :user1) AND (conversation.receiver_username = :user2)
	");
	$stmt->bindValue(':user1', $from, PDO::PARAM_STR);
    $stmt->bindValue(':user2', $to, PDO::PARAM_STR);
	$stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (isset($results[0])){
		return $results[0]['id'];
	}else{
			try {
			$stmt = $db->prepare("INSERT INTO `conversation` (`id`, `sender_username`, `receiver_username`) VALUES (NULL, :sender_username, :receiver_username);");
			$stmt->bindValue(':sender_username', $from, PDO::PARAM_STR);
			$stmt->bindValue(':receiver_username', $to, PDO::PARAM_STR);
			$stmt->execute();
		} catch(PDOEXCEPTION $e) {
			echo "There was a database error, please try again later.";
		}
		$conversation_id = getConversationID($from, $to);
		echo $conversation_id;
		return $conversation_id;
	}
}

function addMessage($from, $to, $text, $read, $message_type){
    $conversation_id = getConversationID($from, $to);
	try {
		$db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS); 
        $stmt = $db->prepare("INSERT INTO `message` (`id`, `conversation_id`, `text`, `time`, `read`, `message_type`) VALUES (NULL, :conversation_id, :text, NOW(), :read, :message_type);");
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