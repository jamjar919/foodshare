<?php 
/**
 * Logs a user out and redirects them
 */
?>
<?php
session_start();
setcookie("username");
setcookie("token");
header("Location: login.php");
?>