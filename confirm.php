<?php 
/**
 * Page that verifies an email address using the get parameters ?username=val and ?key=val
 */
?>
<?php
    define('__ROOT__',dirname(__FILE__));
    require_once __ROOT__."/class/User.class.php";
    require_once __ROOT__."/class/UserTools.class.php";
    require 'class/Page.class.php';
    $p = new Page("Confirm email address");
    if (isset($_GET["username"]) && isset($_GET["key"])) {
        $result = UserTools::validateUserEmail($_GET["username"],$_GET["key"]);
        if (true === $result) {
            $p->buildHead();
            $p->buildHeader();
            echo "<h1 class=\"text-center\">Successfully validated email. Redirecting...</h1>";
            // Redir script
            echo "<script>$(document).ready(function(){setTimeout(function(){window.location.replace(\"profile.php\");},2000)})</script>";
        } else {
            $p->buildHead();
            $p->buildHeader();
            echo "<h1 class=\"text-center\">Error validating email</h1>";
            UserTools::printErrors($result);
        }
    } else {
        header("Location: index.php");
    }
    $p->buildFooter();
?>