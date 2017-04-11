<?php
    define('__ROOT__',dirname(__FILE__));
    require __ROOT__.'/class/Page.class.php';
    require __ROOT__.'/class/Food.class.php';
    $p = new Page("Food", false);
    if (!isset($_GET["item"])) {
        $p->buildHead();
        $p->buildHeader();
        require __ROOT__.'/class/template/404.php';
        $p->buildFooter();
        return;
    }
    $food = new Food(intval($_GET["item"]));
    if (empty($food->item)) {
        $p->buildHead();
        $p->buildHeader();
        require __ROOT__.'/class/template/404.php';
        $p->buildFooter();
        return;
    }
    $owner = new User($food->item["user_username"]);
    $ownerProfile = $owner->getPublicProfile();
    $p->name = $food->item["name"];
    $p->buildHead();
    $p->buildHeader();
?>
    <div class="col-sm-3">
        
        <div class="card food-item">
            <img src="<?php echo $food->item["image_url"]; ?>" class="card-img-top">
            <div class="card-block">
                <a class="btn btn-success btn-block" href="#" role="button">Claim</a>
            </div>
        </div>
    </div>
    <div class="col-sm-9">
        <div class="card food-details">
            <div class="card-block">
                <h1 class="card-title"><?php echo $food->item["name"]; ?></h1>
                <p class="card-subtitle text-muted">Posted <span class="converttime"><?php echo $food->item["time"]; ?></span></p>
                <p class="card-text"><?php echo $food->item["description"]; ?></p>
            </div>
            <div class="card-footer text-muted">
                Expires <span class="converttime"><?php echo $food->item["expiry"]; ?></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card inline-map">
                    <iframe
                        style="height: 500px"
                        frameborder="0" style="border:0"
                        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyBsIs05rl3R9lbL6q3vluRXERaIVesToRA
                            &q=<?php echo $food->item["latitude"]; ?>,<?php echo $food->item["longitude"]; ?>" allowfullscreen>
                    </iframe>
                    <div class="card-footer text-muted">
                        Approximate location: Message user for pickup location.
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card inline-userprofile">
                    <img src="<?php echo $ownerProfile["profile_picture_url"]; ?>" class="card-img-top narrowimg">
                    <div class="card-block">
                        <h2 class="card-title"><?php echo $food->item["user_username"]; ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
    $p->buildFooter();
?>