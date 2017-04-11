<?php

define('__ROOT__',dirname(dirname(__FILE__)));
require_once __ROOT__.'/db.php';
require_once __ROOT__.'/class/User.class.php';

class Messages {

    public $user = null;
    
    function __construct() {
        $username = isset($_COOKIE["username"])? $_COOKIE["username"] : null;
        $token = isset($_COOKIE["token"])? $_COOKIE["token"] : null;
        $this->user = new User($username,$token);
    }

    public function send($text,$to) {
    }

    public function getMessagesWith($to) {
        if ($this->user->isLoggedIn()) {
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS); 
            $stmt = $db->prepare("
            SELECT message.id, message.conversation_id, message.text, message.time, message.read, message.message_type, conversation.receiver_username, conversation.sender_username 
            FROM message, conversation 
            WHERE message.conversation_id = conversation.id 
            AND (
                    (conversation.sender_username = :user1) AND (conversation.receiver_username = :user2)
                OR 
                    (conversation.receiver_username = :user1) AND (conversation.sender_username = :user2)
                    )
            ORDER BY message.time DESC");
            $stmt->bindValue(':user1', $this->user->username, PDO::PARAM_STR);
            $stmt->bindValue(':user2', $to, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        }
        return false;
    }
}