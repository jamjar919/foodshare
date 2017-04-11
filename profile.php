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
                <?php if (!empty($profile['profile_picture_url']))  { ?>
                    <img src="<?php echo $profile['profile_picture_url']; ?>" class="card-img-top profilepicture">
                <?php } ?>
                <div class="card-block">
                    <h2><a class="card-title" href="user.php?id=<?php echo $profile['username'];?>"><?php echo $profile['username'];?></a></h2>
                    <p class="card-text">Location: <?php echo $profile['postcode']; ?> (<?php echo $profile['latitude']; ?>, <?php echo $profile['longitude']; ?>)</p>
                    <a href="editprofile.php" class="btn btn-primary">Edit Details</a>
                </div>
            </div>
            <div class="card user-profile-links">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="user.php?id=<?php echo $profile['username'];?>">Your public profile</a></li>
                    <li class="list-group-item"><a href="editprofile.php">Edit profile details</a></li>
                    <li class="list-group-item"><a href="messages.php">Messages</a></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 col-md-9">
            <div class="row">
                <div class="card-block notifications">
                    <h3>Hey, you!</h3>
                    <?php if ($p->user->hasIncompleteProfile()) { ?>
                        <div class="alert alert-info alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            Looks like you haven't filled in your profile yet. Visit the <a href="editprofile.php">Edit profile</a> page and enter a location and upload a profile picture!
                        </div>
                    <?php } ?>
                    <?php if (! $p->user->isVerified()) { ?>
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            Please verify your email address (<?php echo $profile["email"]; ?>) by clicking on the link in your email.
                        </div>
                    <?php } ?>
                    <?php if (isset($_GET["message"])) { ?>
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <?php echo strip_tags($_GET["message"]); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <h2 class="col-md-6">Your items</h2>
                <h2 class="col-md-6 text-right"><small><a href="additem.php">Add a new item</a></small></h2>
            </div>
            <div id="myitems" class="masonry">
            </div>
            <h2>Items you might be interested in...</h2>
            Load in via AJAX
        </div>
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
                $(selector).text("There was an error loading your items");
            });
        });
    </script>
<?php
    $p->buildFooter();
?>