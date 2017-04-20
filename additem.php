<?php 
/**
 * Adds an item under the currently logged in user, then redirects to the edit page for that item.
 */
?>
<?php
    define('__ROOT__',dirname(__FILE__));
    require_once __ROOT__.'/class/Food.class.php';
    // Create item
    $f = new Food();
    if ($f != null) {
        header("Location: edititem.php?item=".$f->item["id"]);
    } else {
        header("Location: profile.php");
    }
?>