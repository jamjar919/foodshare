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
    $p->name = $food->item["name"];
    $p->buildHead();
    $p->buildHeader();
?>
    <div class="col-sm-3">
        <div class="card food-item">
            <?php if (! empty($food->item["image_url"])) { ?>
                <img src="<?php echo $food->item["image_url"]; ?>" class="card-img-top">
            <?php } ?>
            <div class="card-block">
            <?php if ($isOwner) { ?>
                <div class="btn-group btn-group-fullwidth" role="group" aria-label="...">
                    <a class="btn btn-primary" href="edititem.php?item=<?php echo $food->item["id"];?>" role="button">Edit</a>
                    <a class="btn btn-danger" href="deleteitem.php?item=<?php echo $food->item["id"];?>" role="button" id="deleteItem">Delete</a>
                    <script>
                        $(document).ready(function() {
                            $("#deleteItem").click(function(e) {
                                var response = window.confirm("Are you sure you want to delete the item? This is undoable!");
                                if (response == false) {
                                    return false;
                                }
                            })
                        });
                    </script>
                </div>
            <?php } else { ?>
                <a class="btn btn-success btn-block" href="claim.php?item=<?php echo $food->item["id"];?>" role="button">Claim</a>
            <?php } ?>
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
                <div class="card">
                    <div class="card-header">
                        Tags
                    </div>
                    <div class="card-block tags">
                    <?php 
                    $tags = $food->getTags();
                    for ($i = 0; $i < sizeof($tags); $i++) {
                    ?>
                        <span class="tag"><?php echo $tags[$i]; ?></span>
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
    $p->buildFooter();
?>