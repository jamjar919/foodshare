<?php 
/**
 * Displays the claim history for the currently logged in user
 */
?>
<?php
    define('__ROOT__',dirname(__FILE__));
    require __ROOT__.'/class/Page.class.php';
    require __ROOT__.'/class/Food.class.php';
    $p = new Page("History", true);
    $history = $p->user->getClaimHistory();
    $p->buildHead();
    $p->buildHeader();
?>
    <h2>Claim history</h2>
    <div class="row">
        <div class="col-sm-6">
            <h3>Current</h3>
            <div class="masonry-two">
                <?php foreach($history as $item) { ?>
                    <?php if (! $item["item_gone"]) { ?>
                        <div class="card food-item">
                            <img src="<?php echo $item["image_url"]; ?>" class="card-img-top" id="foodimage" <?php if (empty($item["image_url"])) { ?>style="display:none;"<?php } ?>)>
                            <div class="card-block">
                                <h4><?php echo $item["name"]; ?></h4>
                                <p class="card-text"><?php echo $item["description"]; ?></p>
                                <div class="btn-group btn-group-fullwidth">
                                    <a href="item.php?item=<?php echo $item["id"]; ?>" class="btn btn-primary">View</a>
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                Expires <span class="converttime"><?php echo $item["expiry"]; ?></span>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
                <?php if (sizeof($history) < 1) { ?>
                    <p class="no-items-text">No items to display</p>
                <?php } ?>
            </div>
        </div>
        <div class="col-sm-6">
            <h3>Past</h3>
            <div class="masonry-two">
                <?php foreach($history as $item) { ?>
                    <?php if ($item["item_gone"]) { ?>
                        <div class="card food-item gone-food-item">
                            <img src="<?php echo $item["image_url"]; ?>" class="card-img-top" id="foodimage" <?php if (empty($item["image_url"])) { ?>style="display:none;"<?php } ?>)>
                            <div class="card-block">
                                <h4><?php echo $item["name"]; ?></h4>
                                <p class="card-text"><?php echo $item["description"]; ?></p>
                                <div class="btn-group btn-group-fullwidth">
                                    <a href="item.php?item=<?php echo $item["id"]; ?>" class="btn btn-primary">View</a>
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                Expires <span class="converttime"><?php echo $item["expiry"]; ?></span>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
                <?php if (sizeof($history) < 1) { ?>
                    <p class="no-items-text">No items to display</p>
                <?php } ?>
            </div>
        </div>
    </div>
<?php 
    $p->buildFooter();
?>