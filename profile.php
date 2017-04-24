<?php 
/**
 * The nerve center of a logged in user. Displays their items, links to other pages, profile summary, and suggested items
 */
?>
<?php
    define('__ROOT__',dirname(__FILE__));
    require __ROOT__.'/class/Page.class.php';
    $p = new Page("User Home", true);
    $p->buildHead();
    $p->buildHeader();
    $profile = $p->user->getPrivateProfile();
    $p->user->updateScore();
?>
    <div class="row">
        <div class="col-sm-3">
            <div class="card user-profile">
                <?php if (!empty($profile['profile_picture_url']))  { ?>
                    <img src="<?php echo $profile['profile_picture_url']; ?>" class="card-img-top profilepicture">
                <?php } ?>
                <div class="card-block">
                    <h2><a class="card-title" href="user.php?id=<?php echo $profile['username'];?>"><?php echo $profile['username'];?></a></h2>
                    <p class="card-text">Location: <?php echo $profile['postcode']; ?> (<?php echo $profile['latitude']; ?>, <?php echo $profile['longitude']; ?>)</p>
                    <a href="editprofile.php" class="btn btn-primary">Edit Details</a>
                </div>
                <div class="card-footer">
                <?php echo $profile['score'];?> points
                </div>
            </div>
            <div class="card user-profile-links">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="user.php?id=<?php echo $profile['username'];?>">Your public profile</a></li>
                    <li class="list-group-item"><a href="editprofile.php">Edit profile details</a></li>
                    <li class="list-group-item"><a href="messages.php">Messages</a></li>
                    <li class="list-group-item"><a href="claimhistory.php">Claim History</a></li>
                    <li class="list-group-item"><a href="posthistory.php">Post History</a></li>
                    <li class="list-group-item"><a href="score.php">Score Breakdown</a></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-9">
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
                    <?php 
                        $ownedClaimedItems = $p->user->getOwnedClaimedFoods();
                        if (!empty($ownedClaimedItems)) {
                            ?>
                            <div class="alert alert-success">
                                <p><strong>Some of your items have been claimed!</strong> They are listed below. The user who claimed the item should message you soon. If any of these items are gone, you should mark them as such.</p>
                                <ul>
                                    <?php
                                        foreach($ownedClaimedItems as $item) {
                                    ?>
                                        <li>"<?php echo $item["name"]; ?>" claimed by <a href="messages.php?user=<?php echo $item["claimer_username"]; ?>"><?php echo $item["claimer_username"]; ?></a>. <a href="edititem.php?item=<?php echo $item["id"]; ?>">(Mark as gone)</a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php
                            
                        }
                        $claimedItems = $p->user->getClaimedFoods();
                        if (!empty($claimedItems)) {
                            ?>
                            <div class="alert alert-warning">
                                <p><strong>You've claimed the following items.</strong> Message the user to organise pickup times and remind them to mark the item as gone after you've picked it up.</p>
                                <ul>
                                    <?php
                                        foreach($claimedItems as $item) {
                                    ?>
                                        <li>"<?php echo $item["name"]; ?>" owned by <a href="messages.php?user=<?php echo $item["user_username"]; ?>"><?php echo $item["user_username"]; ?></a>. <a href="item.php?item=<?php echo $item["id"]; ?>">(View)</a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php
                        }
                    ?>
                </div>
            </div>
            <div class="collapsible-panels-mob">
                <div class="row panel-control">
                    <h2 class="col-xs-6">Your items &nbsp;<span class="glyphicon glyphicon-collapse-down mobileonly" style="font-size: 0.75em"></span></h2>
                    <h2 class="col-xs-6 text-right"><small><a href="additem.php">Add a new item</a></small></h2>
                </div>
                <div id="myitems" class="masonry panel-content">
                </div>
            </div>
            <div class="collapsible-panels-mob">
                <div class="row panel-control">
                    <h2 class="col-xs-12">Items you might be interested in... &nbsp;<span class="glyphicon glyphicon-collapse-down mobileonly" style="font-size: 0.75em"></span></h2>
                </div>
                <div class="masonry panel-content" id="exampleitems">
                </div>
            </div>
            <script>
                $(document).ready(function () {
                    $.get("api/food.php?q=&location=<?php echo $profile['latitude']; ?>%2C<?php echo $profile['longitude']; ?>&distance=30&expiry=Any%20time&time=Any%20time&sort=Distance:%20closest%20first&num=10&offset=0")
                    .then(function(data) {
                        var food = [];
                        data["food"].forEach(function(f){
                            if(f.user_username != "<?php echo $profile['username'];?>"){
                                printFoodItems([f],"#exampleitems")
                            }
                        });
                        if (data["food"].length < 1) {
                            $("#exampleitems").append('<p class="no-items-text">No items to display</p>')
                        }
                    }) 
                })
            </script>
        </div>
    </div>
    <script src="js/useritems.js"></script>
    <script>
        $(document).ready(function() {
            var username = "<?php echo $profile['username'];?>";
            var selector = "#myitems";
            getUserItems(username)
            .then(function(data) {
                printFoodItems(data["food"],selector,true);
            })
            .catch(function(error) {
                $(selector).text("There was an error loading your items");
            });
        });
    </script>
<?php
    $p->buildFooter();
?>
