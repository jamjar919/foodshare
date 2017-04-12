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
    $isOwner = $food->item["user_username"] == $p->user->username;
    $p->name = "Claim ".$food->item["name"];
    $p->buildHead();
    $p->buildHeader();
?>
    <div class="col-sm-3">
        <div class="card food-item">
            <img src="<?php echo $food->item["image_url"]; ?>" class="card-img-top" id="foodimage" <?php if (empty($food->item["image_url"])) { ?>style="display:none;"<?php } ?>)>
            <div class="card-block">
                <h4><?php echo $food->item["name"]; ?></h4>
                <p class="card-text"><?php echo $food->item["description"]; ?></p>
                <div class="btn-group btn-group-fullwidth">
                    <a href="item.php?item=<?php echo $food->item["id"]; ?>" class="btn btn-primary">View</a>
                </div>
            </div>
            <div class="card-footer text-muted">
                Expires in <span class="converttime"><?php echo $food->item["expiry"]; ?></span>
            </div>
        </div>
    </div>
    <div class="col-sm-9">
        <div class="card claim">
            <div class="card-block">
                <h1>Claim <?php echo $food->item["name"]; ?></h1>
                <p>You are claiming the item shown to the left. Please only claim items if you are able to collect them!</p>
                <ul>
                    <li>Confirming a claim will <strong>notify the owner</strong> that their item has been claimed.</li>
                    <li>You should <strong>organise a pickup time</strong> with the item owner through the messaging system.</li>
                    <li>Please be <strong>nice and courteous</strong> when picking up the item!</li>
                </ul>
            </div>
            <div class="card-footer">
                <div class="btn-group btn-group-fullwidth">
                    <button class="btn btn-success">I understand. Claim!</button>
                    <a href="item.php?item=<?php echo $food->item["id"]; ?>" class="btn btn-primary">I changed my mind</a>
                </div>
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
                    <?php if (!empty($ownerProfile["profile_picture_url"]))  { ?>
                        <img src="<?php echo $ownerProfile["profile_picture_url"]; ?>" class="card-img-top narrowimg">
                    <?php } ?>
                    <div class="card-block">
                        <h2 class="card-title"><a href="user.php?id=<?php echo $food->item["user_username"]; ?>"><?php echo $food->item["user_username"]; ?></a><?php if ($isOwner) { ?><small>(you)</small><?php } ?></h2>
                    </div>
                    <div class="card-footer text-muted">
                        <?php echo $ownerProfile["score"]; ?> points
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
    $p->buildFooter();
?>