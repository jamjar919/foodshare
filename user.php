<?php
    define('__ROOT__',dirname(__FILE__));
    require __ROOT__.'/class/Page.class.php';
    $p = new Page("User", true);
    if (! isset($_GET["id"])) {
        $p->buildHead();
        $p->buildHeader();
        require __ROOT__.'/class/template/404.php';
        $p->buildFooter();
        return;
    }
    $u = new User($_GET["id"]);
    $profile = $u->getPublicProfile();
    if (empty($profile)) {
        $p->buildHead();
        $p->buildHeader();
        require __ROOT__.'/class/template/404.php';
        $p->buildFooter();
        return;
    }
    $u->updateScore();
    $p->name = $profile["username"];
    $p->buildHead();
    $p->buildHeader();
?>
    <div class="row">
        <div class="col-md-3">
            <div class="card inline-userprofile">
                <?php if (!empty($profile['profile_picture_url']))  { ?>
                    <img src="<?php echo $profile["profile_picture_url"]; ?>" class="card-img-top narrowimg">
                <?php } ?>
                <div class="card-block">
                    <h2 class="card-title"><?php echo $profile["username"]; ?></h2>
                </div>
                <?php if ($p->user->isLoggedIn()) { ?>
                    <div class="card-block">
                        <a class="btn btn-block btn-primary" href="messages.php?user=<?php echo $profile["username"]; ?>">Message</a>
                    </div>
                <?php } ?>
                <div class="card-footer text-muted">
                    <?php echo $profile["score"]; ?> points
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <h2><?php echo $profile["username"]; ?>'s Items</h2>
            <div id="myitems" class="masonry">
            </div>
            <script src="js/useritems.js"></script>
            <script>
                $(document).ready(function() {
                    var username = "<?php echo $profile['username'];?>";
                    var selector = "#myitems";
                    getUserItems(username)
                    .then(function(data) {
                        console.log(data);
                        printFoodItems(data["food"],selector);
                    })
                    .catch(function(error) {
                        $(selector).text("There was an error loading <?php echo $profile['username'];?>'s items");
                    });
                });
            </script>
        </div>
    </div>
<?php
    $p->buildFooter();
?>