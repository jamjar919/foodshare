<?php
    define('__ROOT__',dirname(__FILE__));
    require __ROOT__.'/class/Page.class.php';
    $p = new Page("User", true);
    if (! isset($_GET["user"])) {
        $p->buildHead();
        $p->buildHeader();
        require __ROOT__.'/class/template/404.php';
        $p->buildFooter();
        return;
    }
    $u = new User($_GET["user"]);
    $profile = $u->getPublicProfile();
    if (empty($profile)) {
        $p->buildHead();
        $p->buildHeader();
        require __ROOT__.'/class/template/404.php';
        $p->buildFooter();
        return;
    }
    $p->name = $profile->item["name"];
    $p->buildHead();
    $p->buildHeader();
?>

<?php
    $p->buildFooter();
?>