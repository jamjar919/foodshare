<?php
define('__ROOT__',dirname(dirname(__FILE__)));
require_once __ROOT__.'/db.php';
require_once __ROOT__.'/class/User.class.php';
require_once __ROOT__.'/class/UserTools.class.php';

/**
*  This class represents a food object. It can be loaded by any user - However it must be loaded by the owner with the correct cookie set in order to access methods that
*  modify database data. Constructing the object with a null reference will create a new food item and you must be logged in for this.
*/
class Food
{
    /** The unique database ID of the food item */
    private $id;
    /** The username of the owner */
    private $owner;
    /** The saved database details of the item as a dictionary */
    public $item = null;

    /**
     * Food constructor. If the id is null, create a new food item
     *
     * @param $id id of the food item
     */
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
                (`id`, `name`, `description`, `image_url`, `expiry`, `time`, `latitude`, `longitude`, `user_username`, `claimer_username`, `tag_list_id`, `item_gone`) 
                VALUES 
                (NULL, :name, :desc, '', CURRENT_DATE(), NOW(), :lat, :long, :user, '', :taglistid, 0);");
                $stmt->bindValue(":name", "New Item", PDO::PARAM_STR);
                $stmt->bindValue(":desc", "Write a description of your item here. Include helpful things like number of items, weight, and any other important information!", PDO::PARAM_STR);
                $stmt->bindValue(":user", htmlspecialchars($username, ENT_QUOTES));
                $stmt->bindValue(":lat", htmlspecialchars($profile['latitude'], ENT_QUOTES));
                $stmt->bindValue(":long", htmlspecialchars($profile['longitude'], ENT_QUOTES));
                $stmt->bindValue(":taglistid", htmlspecialchars($taglistid, ENT_QUOTES));
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

    /**
     * Update the user's specified food item details
     *
     * @param $username  Username of the user who wishes to update their food item
     * @param $token     User token
     * @param $id        id of the food item
     * @param $name      Name of the food item
     * @param $desc      Description of the food item
     * @param $expiry    Expiry date of the food item
     * @param $lat       Latitude of the food item
     * @param $long      Longitude of the food item
     * @param $imageurl  Image URL of the food item
     * @return bool      True if food item successfully updated with new details, false if the user is not logged in, the user is not the owner or on failure
     */
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
            $stmt->bindValue(":name", htmlspecialchars($name, ENT_QUOTES), PDO::PARAM_STR);
            $stmt->bindValue(":desc", htmlspecialchars($desc, ENT_QUOTES), PDO::PARAM_STR);
            $stmt->bindValue(":expiry", htmlspecialchars($expiry, ENT_QUOTES), PDO::PARAM_STR);
            $stmt->bindValue(":lat", htmlspecialchars($lat, ENT_QUOTES), PDO::PARAM_STR);
            $stmt->bindValue(":long", htmlspecialchars($long, ENT_QUOTES), PDO::PARAM_STR);
            $stmt->bindValue(":url", htmlspecialchars($imageurl, ENT_QUOTES), PDO::PARAM_STR);
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

    /**
     * Strip html tags off a tag if any
     *
     * @param $tag          Tag name
     * @return string       Stripped tag
     */
    
    private function stripTag($tag) {
        $tag = strtolower($tag);
        $tag = preg_replace("/[^a-z0-9_.@\-]/", '', $tag);
        return $tag;
    }
    

    /**
     * Create a new tag record, and return the ID of the new tag
     * @param $tag   Name of the tag
     * @return bool|int     Tag's id. Returns false on failure
     */
    private function createNewTag($tag) {
        try {
            $tag = $this->stripTag($tag);
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $stmt = $db->prepare("INSERT INTO `tag` (`id`, `name`) VALUES (NULL, :tag);");
            $stmt->bindValue(":tag", htmlspecialchars($tag, ENT_QUOTES), PDO::PARAM_STR);
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
     * @param $tag   Name of the tag
     * @return bool|int     The tag's id. Returns false on failure
     */
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
     *
     * @param $tag   Name of the tag
     * @return bool         true if the tag is successfully inserted otherwise false
     */
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
            $stmt->bindValue(":tagid", htmlspecialchars($tagId, ENT_QUOTES), PDO::PARAM_INT);
            $stmt->bindValue(":taglistid", htmlspecialchars($tagListId, ENT_QUOTES), PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Remove the tag connection from the food item
     *
     * @param $tag   Name of the tag
     * @return bool         True if successfully removed or didn't exist, false on failure.
     */
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

    /**
     * Updates the user's tag list with the new tags specified
     * @param $newTags
     * @return array|bool|null  null if no username or token set. Returns false if the user is not logged in or is not the
     *                          owner else returns the new tag list
     */
    
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

    /**
     * Retrieves the tags of the food item
     * @return array|bool Tags of the food item otherwise returns false on failure
     */
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

    /**
     * Delete the food item
     * @return bool|null    Returns null if the user is not the owner or the user is not logged in otherwise returns
     *                      true on success or false on failure
     *
     */
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
        if ( ! ($username === $this->owner)) {
            return null;
        }
        // DELETE THIS
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

    /**
     * Sets the food item claimer for this food item
     * @return bool|null    null if the user is not logged in. Returns true on success, false on failure
     */
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
            $result = $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
        // Send email!
        mail(UserTools::getEmail($this->owner), "Item claimed!", "Hey! \n \n An item you put up, titled \"".$this->item["name"]."\" has been claimed by the user ".$claimer."! They should be in contact via the messaging system soon to arrange a pickup time. \n\n Thanks, \n FlavourTown");
        return $result;
   }

    /**
     * Unclaim this food item
     * @return bool|null    null if the user isn't logged in. Returns true on success,  false on failure
     */
    public function unclaim() {
        // Must be auth'ed as owner!
        // Or as food claimer
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
        if ( ! (
            ($username === $this->owner) ||
            ($username === $this->item["claimer_username"])
        ) ) {
            return false;
        }
        try {
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $stmt = $db->prepare("UPDATE food SET claimer_username = '' WHERE id = :id");
            $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Mark this food item as gone
     * @return bool|null    null if the user isn't logged in. Returns true on success, false on failure
     */
    public function markAsGone() {
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
        if ( ! ($username === $this->owner)) {
            return null;
        }
        try {
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $stmt = $db->prepare("UPDATE food SET item_gone = b'1' WHERE id = :id");
            $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Unmark this food item as gone
     * @return bool|null    null if the user isn't logged in. Returns true on success or false on failure
     */
    public function unmarkAsGone() {
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
        if ( ! ($username === $this->owner)) {
            return null;
        }
        try {
            $db = new PDO('mysql:host='.DBSERV.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
            $stmt = $db->prepare("UPDATE food SET item_gone = b'0' WHERE id = :id");
            $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}