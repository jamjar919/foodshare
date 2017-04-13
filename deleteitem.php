<?php
    define('__ROOT__',dirname(__FILE__));
    require __ROOT__.'/class/Food.class.php';
    if (!isset($_GET["item"])) {
        header("Location: profile.php");
        return;
    }
    $food = new Food(intval($_GET["item"]));
    if (empty($food->item)) {
        header("Location: profile.php");
        return;
    }
    // D E L E T   T H I S
    if($food->delete()) {
        header("Location: profile.php?message=".urlencode("Successfully deleted the item \"".$food->item["name"]."\""));
    } else {
        header("Location: edititem.php?item=".$food->item["id"]);
    }
?>