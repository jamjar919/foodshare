<?php

define('__ROOT__',dirname(dirname(__FILE__)));
require_once __ROOT__.'/db.php';
require_once __ROOT__.'/class/User.class.php';

/**
 *  This class is used to add and retrieve a set of messages between two users.
 */
class Messages {

    public $user = null;

    /**
     * Messages constructor.
     */
    function __construct() {
        $username = isset($_COOKIE["username"])? $_COOKIE["username"] : null;
        $token = isset($_COOKIE["token"])? $_COOKIE["token"] : null;
        $this->user = new User($username,$token);
    }

    /**
     * Get all of the user's conversations
     *
     * @return array|bool Array of conversations. Returns false on failure
     */
    public function getConversations() {
        if ($this->user->isLoggedIn()) {
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS); 
            $stmt = $db->prepare("SELECT * FROM conversation WHERE conversation.sender_username = :user OR conversation.receiver_username = :user");
            $stmt->bindValue(':user', $this->user->username, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        }
        return false;
    }

    /**
     * Send text to another user
     *
     * @param string $text  Message text
     * @param string $to    Recipient username
     */
    public function send($text,$to) {
    }

    /**
     * Get the messages with another user
     *
     * @param $to           Recipient username
     * @return array|bool   Array of messages. Returns false on failure
     */
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
            ORDER BY message.time ASC");
            $stmt->bindValue(':user1', $this->user->username, PDO::PARAM_STR);
            $stmt->bindValue(':user2', $to, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        }
        return false;
    }
}