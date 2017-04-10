<?php
    define('__ROOT__',dirname(__FILE__));
    require __ROOT__.'/class/Page.class.php';
    $p = new Page("User Home", true);
    $p->buildHead();
    $p->buildHeader();
    $profile = $p->user->getPrivateProfile();
?>
    <div class="row">
        <div class="col-sm-6 col-md-3">
            <div class="card user-profile">
                <img src="<?php echo $profile['profile_picture_url']; ?>" class="card-img-top profilepicture">
                <div class="card-block">
                    <h2><a class="card-title" href="user.php?id=<?php echo $profile['username'];?>"><?php echo $profile['username'];?></a></h2>
                    <p class="card-text">Location: <?php echo $profile['postcode']; ?> (<?php echo $profile['latitude']; ?>, <?php echo $profile['longitude']; ?>)</p>
                    <a href="editprofile.php" class="btn btn-primary">Edit Details</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-9">
            <div class="card">
                <div class="card-block notifications">
                    <h3>Hey, you!</h3>
                    Messages to the user (claim notices, etc) would go here...
                </div>
            </div>
            <div class="row">
                <h2 class="col-md-6">Your items</h2>
                <h2 class="col-md-6 text-right"><small><a href="#">Add a new item</a></small></h2>
            </div>
            Load in via AJAX
            <h2>Items you might be interested in...</h2>
            Load in via AJAX
        </div>
    </div>
<?php
    $p->buildFooter();
?>