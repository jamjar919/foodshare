<?php
/**
 *  TODO Description of the class goes here
 */
define('__ROOT__',dirname(dirname(__FILE__)));
require_once __ROOT__.'/db.php';
require_once __ROOT__.'/class/User.class.php';

class Page
{
    public $name;
    public $username;
    public $user;
    private $isLoggedIn;

    /**
     * Page constructor.
     * @param $name                 Name of the page
     * @param bool $requiresLogin   Determines whether the page needs login. false by default
     */
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

    /**
     * Create the head of the page
     */
    public function buildHead() {
        echo "<!doctype html><html><head>";
        echo "<title>".$this->name." - Flavourtown</title>";
        require_once __ROOT__.'/class/template/head.html';
        echo '</head><body>';
    }

    /**
     * Create the header of the page with a searchbar by default
     *
     * @param bool $includeSearchbar    Determines whether the header contains the searchbar. True by default
     */
    public function buildHeader($includeSearchbar=true) {
        echo '<div class="scale-wrap"><header id="header">
            <div class="container">
                <div class="top-header">
                    <div class="site-title">';
        if ($this->isLoggedIn) {
        echo '            <h1><a href="profile.php">FlavourTown</a></h1>';
        } else {
        echo '            <h1><a href="index.php">FlavourTown</a></h1>';
        }
        echo '       </div>
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

    /**
     *  Create the footer of the page
     */
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

    /**
     * Create the nav item links
     */
    public function getNavItems() {
        if (! $this->isLoggedIn) {
            echo '  <div class="nav-item">
                        <a href="index.php">Home</a>
                    </div>
                    <div class="nav-item">
                        <a href="about.php">About</a>
                    </div>';
            echo '  <div class="nav-item">
                        <a href="login.php">Login</a>
                    </div>
                    <div class="nav-item">
                        <a href="register.php">Register</a>
                    </div>';
        } else {
            echo '  <div class="nav-item">
                        <a href="profile.php">'.$this->user->username.'</a>
                    </div>
                    <div class="nav-item">
                        <a href="messages.php">Messages</a>
                    </div>
                    <div class="nav-item">
                        <a href="logout.php" class="glyphicon glyphicon-log-out"></a>
                    </div>';
        }
    }
}