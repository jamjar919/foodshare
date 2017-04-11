<?php
    define('__ROOT__',dirname(__FILE__));
    require __ROOT__.'/class/Page.class.php';
    $p = new Page("User Home", true);
    $p->buildHead();
    $p->buildHeader();
    $profile = $p->user->getPrivateProfile();
?>
    <div class="">
    </div>
<?php
    $p->buildFooter();
?>