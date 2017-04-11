<?php

define('__ROOT__',dirname(dirname(__FILE__)));
require_once __ROOT__.'/db.php';
require_once __ROOT__.'/class/User.class.php';

class Food
{
    private $id;
    private $owner;
    public $item = null;
    function __construct($id=null) {
        if ($id===null) {
            // Create a new item... 
            // User must be logged in ya dingus
            if (!isset($_COOKIE["username"])) {
                return null;
            }
            if (!isset($_COOKIE["token"])) {
                return null;
            }
            $username = $_COOKIE["username"];
            $token = $_COOKIE["token"];
            $user = new User($username,$token);
            if ( ! $user->isLoggedIn()) {
                return null;
            }
            $profile = $user->getPrivateProfile();
            // Do the actual creation
            try {
                $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
                // Make a new tag list entry
                $stmt = $db->prepare("INSERT INTO tag_list(id,tag_id) VALUES (NULL, 0)");
                $stmt->execute();
                $taglistid = $db->lastInsertId();
                // Create the food item with default params
                $stmt = $db->prepare("INSERT INTO `food` 
                (`id`, `name`, `description`, `image_url`, `expiry`, `time`, `latitude`, `longitude`, `user_username`, `claimer_username`, `tag_list_id`) 
                VALUES 
                (NULL, :name, :desc, '', CURRENT_DATE(), NOW(), :lat, :long, :user, '', :taglistid);");
                $stmt->bindValue(":name", "New Item", PDO::PARAM_STR);
                $stmt->bindValue(":desc", "Write a description of your item here. Include helpful things like number of items, weight, and any other important information!", PDO::PARAM_STR);
                $stmt->bindValue(":user", $username);
                $stmt->bindValue(":lat", $profile['latitude']);
                $stmt->bindValue(":long", $profile['longitude']);
                $stmt->bindValue(":taglistid", $taglistid);
                $stmt->execute();
                // Reset id to the id of the row to prepare for loading
                $id = $db->lastInsertId();
                if ($stmt->rowCount() < 1) {
                    return null;
                }
            } catch (PDOException $e) {
                return null;
            }
        }
        try {
            // Load the item
            $this->id = $id;
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $stmt = $db->prepare("SELECT * FROM food WHERE id = :id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $this->item = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->owner = $this->item["user_username"];
        } catch (PDOException $e) {
            return null;
        }
    }
    
   public function update($username, $token, $id, $name, $desc, $expiry, $lat, $long,$imageurl) {
        $user = new User($username,$token);
        if ( ! $user->isLoggedIn()) {
            return false;
        }
        if (! ($username === $this->owner)) {
            return false;
        }
        try {
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $stmt = $db->prepare("UPDATE `food` SET `name` = :name, `description` = :desc, `expiry` = :expiry, `time` = NOW(), `latitude` = :lat, `longitude` = :long, `image_url` = :url WHERE `id` = :id;");
            $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);
            $stmt->bindValue(":name", $name, PDO::PARAM_STR);
            $stmt->bindValue(":desc", $desc, PDO::PARAM_STR);
            $stmt->bindValue(":expiry", $expiry, PDO::PARAM_STR);
            $stmt->bindValue(":lat", $lat, PDO::PARAM_STR);
            $stmt->bindValue(":long", $long, PDO::PARAM_STR);
            $stmt->bindValue(":url", $imageurl, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }
}