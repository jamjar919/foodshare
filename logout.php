<?php
session_start();
setcookie("username");
setcookie("token");
header("Location: login.php");
?>