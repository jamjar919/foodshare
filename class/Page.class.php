<?php

define('__ROOT__',dirname(dirname(__FILE__)));
require_once __ROOT__.'/db.php';
require_once __ROOT__.'/class/User.class.php';

class Page
{
    public $name;
    public $user;
    function __construct($name,$requiresLogin=false) {
        $this->name = $name;
        $user = null;
        if ($requiresLogin) {
            $user = isset($_COOKIE["username"])? $_COOKIE["username"] : null;
            $token = isset($_COOKIE["token"])? $_COOKIE["token"] : null;
            $user = new User($user,$token);
            if (! ($user->isLoggedIn())) {
                header("Location: login.php");
            } else {
                $this->user = $user;
            }
        }
    }
    public function buildHead() {
        echo "<!doctype html><html><head>";
        echo "<title>".$this->name." - Flavourtown</title>";
        require_once __ROOT__.'/class/template/head.html';
        echo '</head><body><div class="container">';
    }
    public function buildFooter() {
        require_once __ROOT__.'/class/template/footer-scripts.html'; 
        echo "</div></body></html>";
    }
}