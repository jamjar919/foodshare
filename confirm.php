<?php
    define('__ROOT__',dirname(__FILE__));
    require_once __ROOT__."/class/User.class.php";
    require_once __ROOT__."/class/UserTools.class.php";
    require 'class/Page.class.php';
    $p = new Page("Confirm email address");
    if (isset($_GET["username"]) && isset($_GET["key"])) {
            if (UserTools::validateUserEmail($_GET["username"],$_GET["key"])) {
                    $p->buildHead();
                    $p->buildHeader();
                    echo "<h1>Successfully validated email.</h1>";
            } else {
                    $p->buildHead();
                    $p->buildHeader();
                    echo "<h1>The key and username you have supplied is incorrect.</h1>";
            }
    } else {
        header("Location: index.php");
    }
    $p->buildFooter();
?>