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
            $stmt = $db->prepare("UPDATE food SET `name` = :name, `description` = :desc, `expiry` = :expiry, `time` = NOW(), `latitude` = :lat, `longitude` = :long, `image_url` = :url WHERE `id` = :id;");
            $stmt->bindValue(":id", intval($this->id), PDO::PARAM_INT);
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
    
    private function stripTag($tag) {
        $tag = strtolower($tag);
        $tag = preg_replace("/[^a-z0-9_.@\-]/", '', $tag);
        return $tag;
    }
    
    /**
    * Create a new tag record, and return the ID of the new tag
    */
    private function createNewTag($tag) {
        try {
            $tag = $this->stripTag($tag);
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $stmt = $db->prepare("INSERT INTO `tag` (`id`, `name`) VALUES (NULL, :tag);");
            $stmt->bindValue(":tag", $tag, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount()) {
                return $db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
    * Looks up the value of $tag, and if it does not exist, creates it. Returns the new tag id. 
    **/
    private function lookupTag($tag) {
        try {
            $tag = $this->stripTag($tag);
            //echo "looking up tag ".$tag;
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $stmt = $db->prepare("SELECT * FROM `tag` WHERE name = :tag");
            $stmt->bindValue(":tag", $tag, PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($results)) {
                //echo "creating new tag ".$tag;
                return $this->createNewTag($tag);
            }
            //echo "found id of ".$tag." as ".$results[0]["id"];
            return $results[0]["id"];
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
    * Inserts the tag connection into the list, if it does not already exist;
    **/
    private function insertTag($tag) {
        $tagListId = $this->item["tag_list_id"];
        try {
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $tag = $this->stripTag($tag);
            $tagId = $this->lookupTag($tag);
            // Check if tag link exists
            //echo "looking for tag: ".$tag." (".$tagId.") taglistid: ".$tagListId;
            $stmt = $db->prepare("SELECT * FROM `tag_list` WHERE id = :taglistid AND tag_id = :tagid");
            $stmt->bindValue(":tagid", $tagId, PDO::PARAM_INT);
            $stmt->bindValue(":taglistid", $tagListId, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (! empty($results)) {
                return true;
            }
            // Create new tag link
            $stmt = $db->prepare("INSERT INTO tag_list (id, tag_id) VALUES (:taglistid, :tagid)");
            $stmt->bindValue(":tagid", $tagId, PDO::PARAM_INT);
            $stmt->bindValue(":taglistid", $tagListId, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount()) {
                return true;
            }
            echo "insert failed";
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
    * Remove the tag connection from the food item
    **/
    private function removeTag($tag) {
        $tagListId = $this->item["tag_list_id"];
        try {
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $tag = $this->stripTag($tag);
            $tagId = $this->lookupTag($tag);
            $stmt = $db->prepare("DELETE FROM tag_list WHERE id = :taglistid AND tag_id = :tagid");
            $stmt->bindValue(":tagid", $tagId, PDO::PARAM_INT);
            $stmt->bindValue(":taglistid", $tagListId, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount()) {
                return true;
            }
            // Tag didn't exist anyway (it's not there)
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    
    public function updateTags($newTags) {
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
            return false;
        }
        if (! ($username === $this->owner)) {
            return false;
        }
        $currentTags = $this->getTags();
        $tagsToAdd = array();
        $tagsToRemove = $currentTags;
        if (!empty($newTags)) {
            foreach($newTags as $tag) {
                $id = array_search($tag, $tagsToRemove);
                if ($id === false) {
                    // tag is not in current tag list, add to tags to tagsToAdd
                    $tagsToAdd[] = $tag;
                } else {
                    // Tag is in current tag list and new tag list, do nothing (remove from remove list)
                    unset($tagsToRemove[$id]);
                }
                // Else tag is in the current tags and not in the new one, so keep it in the remove list
            }
        }
        // Add all new tags...
        //echo "tags to add: ";
        //var_dump($tagsToAdd);
        if (!empty($tagsToAdd)) {
            foreach ($tagsToAdd as $tag) {
                if(!$this->insertTag($tag)) {
                    echo "failed to insert ".$tag;
                }
            }
        }
        //echo "tags to remove: ";
        //var_dump($tagsToRemove);
        // Remove all old ones...
        if (!empty($tagsToRemove)) {
            foreach ($tagsToRemove as $tag) {
                if(!$this->removeTag($tag)) {
                    echo "failed to remove ".$tag;
                }
            }
        }
        // Return the new tag list!
        return $this->getTags();
    }
    
    public function getTags() {
        if (!empty($this->item)) {
            try {
                $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
                $stmt = $db->prepare("SELECT tag.name FROM tag,tag_list WHERE tag_list.id = :id AND tag_list.tag_id = tag.id");
                $stmt->bindValue(":id", intval($this->item["tag_list_id"]), PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $tags = array();
                for ($i = 0; $i < sizeof($results); $i++) {
                    $tags[] = $results[$i]["name"];
                }
                return $tags;
            } catch (PDOException $e) {
                return false;
            }
        }
        return array();
    }
    
    public function delete() {
        // Must be auth'ed
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
        // DELET THIS
        $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
        $stmt = $db->prepare("DELETE FROM food WHERE id = :id");
        $stmt->bindValue(":id", intval($this->id), PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount()) {
            return true;
        } else {
            return false;
        }
    }
    
    public function claim() {
        // Must be auth'ed
        // Log in as the user that wants to claim
        if (!isset($_COOKIE["username"])) {
            return null;
        }
        if (!isset($_COOKIE["token"])) {
            return null;
        }
        $claimer = $_COOKIE["username"];
        $token = $_COOKIE["token"];
        $user = new User($claimer,$token);
        if ( ! $user->isLoggedIn()) {
            return null;
        }
        try {
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $stmt = $db->prepare("UPDATE food SET claimer_username = :claimer WHERE id = :id");
            $stmt->bindValue(":claimer", $claimer, PDO::PARAM_STR);
            $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
        
    public function unclaim() {
        // Must be auth'ed as owner!
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
        if (! ($username === $this->owner)) {
            return false;
        }
        try {
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $stmt = $db->prepare("UPDATE food SET claimer_username = '' WHERE id = :id");
            $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
}