<?php

define('__ROOT__',dirname(dirname(__FILE__)));
require_once __ROOT__.'/db.php';
require_once __ROOT__.'/class/User.class.php';

class Food
{
    private $id;
    private $owner;
    public $item = null;
    function __construct($id) {
        try {
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