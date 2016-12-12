<?php
session_start();
require 'functions.php';
// Check if we are already logged in
if (isLoggedIn($_SESSION)) {
    // We are logged in, go to members page
    // header("Location: members.php");
    echo "Automatically logged in via session";
    echo "<a href=\"logout.php\">Logout</a>";
} else {
    echo "You are not logged in";
}
?>
<h1>Recyclr</h1>
<p>Some test stuff</p>
<p>More test stuff</p>
