<?php

define('__ROOT__',dirname(dirname(__FILE__)));
require_once __ROOT__.'/db.php';
require_once __ROOT__.'/class/User.class.php';

class Page
{
    public $name;
    public $username;
    public $user;
    private $isLoggedIn;
    function __construct($name,$requiresLogin=false) {
        $this->name = $name;
        $this->username = isset($_COOKIE["username"])? $_COOKIE["username"] : null;
        $token = isset($_COOKIE["token"])? $_COOKIE["token"] : null;
        $this->user = new User($this->username,$token);
        $this->isLoggedIn = $this->user->isLoggedIn();
        if ($requiresLogin && !$this->isLoggedIn) {
            header("Location: login.php");
        }
    }
    public function buildHead() {
        echo "<!doctype html><html><head>";
        echo "<title>".$this->name." - Flavourtown</title>";
        require_once __ROOT__.'/class/template/head.html';
        echo '</head><body>';
    }
    public function buildHeader($includeSearchbar=true) {
        echo '<div class="scale-wrap"><header id="header">
            <div class="container">
                <div class="top-header">
                    <div class="site-title">
                        <h1><a href="index.php">FlavourTown</a></h1>
                    </div>
                    <nav class="main-navigation">';
        $this->getNavItems();
        echo '      </nav>
                </div>
            </div>';
        if ($includeSearchbar) {
        echo '<div class="searchbar-container">
                <div class="container">';
        require  __ROOT__.'/class/template/searchbar-simple.html';        
        echo '  </div>
            </div>';
        }
        echo '
        </header>
        <div class="container content">';
    }
    public function buildFooter() {
        echo '</div></div><footer id="footer"><nav class="bottom-navigation">';
        $this->getNavItems();
        // Bottom nav only items
        echo '<div class="nav-item">
                    <a href="help.php">Help &amp; FAQ\'s</a>
                </div>';
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
        if (! $this->isLoggedIn) {
            echo '  <div class="nav-item">
                        <a href="login.php">Login</a>
                    </div>
                    <div class="nav-item">
                        <a href="register.php">Register</a>
                    </div>';
        } else {
            echo '  <div class="nav-item">
                        <a href="messages.php">Messages</a>
                    </div>
                    <div class="nav-item">
                        <a href="profile.php">Profile</a>
                    </div>
                    <div class="nav-item">
                        <a href="logout.php" class="glyphicon glyphicon-log-out"></a>
                    </div>';
        }
    }
}