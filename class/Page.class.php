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
        echo '</head><body>';
    }
    public function buildHeader() {
        echo '<header id="header">
            <div class="container">
                <div class="top-header">
                    <div class="site-title">
                        <h1><a href="index.php">Foodshare</a></h1>
                    </div>
                    <nav class="main-navigation">';
        $this->getNavItems();
        echo '      </nav>
                </div>
            </div>
            <div class="searchbar-container">
                <div class="container">';
        require  __ROOT__.'/class/template/searchbar-simple.html';        
        echo '  </div>
            </div>
        </header>
        <div class="container">';
    }
    public function buildFooter() {
        echo '</div><footer id="footer"><nav class="bottom-navigation">';
        $this->getNavItems();
        echo '</nav><p>Property of <a href="https://www.dur.ac.uk/">Durham University</a></p></footer>';
        require_once __ROOT__.'/class/template/footer-scripts.html'; 
        echo "</body></html>";
    }
    public function getNavItems() {
        echo '  <div class="nav-item">
                    <a href="index.php">Home</a>
                </div>
                <div class="nav-item">
                    <a href="about.php">About</a>
                </div>';
        if (! $requiresLogin) {
            echo '  <div class="nav-item">
                        <a href="register.php">Register</a>
                    </div>
                    <div class="nav-item">
                        <a href="login.php">Login</a>
                    </div>';
        }
    }
}