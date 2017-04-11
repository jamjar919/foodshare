<?php
    define('__ROOT__',dirname(__FILE__));
    require_once __ROOT__.'/class/Page.class.php';
    require_once __ROOT__.'/class/Food.class.php';
    $p = new Page("Food", true);
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
    if (!$isOwner) {
        $p->buildHead();
        $p->buildHeader();
        require __ROOT__.'/class/template/404.php';
        $p->buildFooter();
        return;
    }
    $p->name = "Edit ".$food->item["name"];
    $p->buildHead();
    $p->buildHeader();
?>
    <div class="col-sm-3">
        <div class="card food-item">
            <div class="overlay">
                <span class="glyphicon glyphicon-edit edit-image-icon" id="edit-image-icon"></span>
            </div>
            <div class="edit-click-trap overlay">
                <input accept="image/*" type="file" id="editpicture">
            </div>
            <img src="<?php echo $food->item["image_url"]; ?>" class="card-img-top" id="foodimage" <?php if (empty($food->item["image_url"])) { ?>style="min-height: 100px;"<?php } ?>)>
            <script>
                $(document).ready(function() {
                    $('#editpicture').live('change', function(){
                        var fileInput = document.getElementById("editpicture");
                        var file = fileInput.files[0];
                        $("#edit-image-icon").attr('class',"glyphicon glyphicon-refresh glyphicon-refresh-animate edit-image-icon")
                        uploadPicture(file)
                        .then(function(result) {
                            $("#edit-image-icon").attr('class',"glyphicon glyphicon-edit edit-image-icon")
                            $("#foodimage").attr("src",result["data"]["link"]);
                        })
                        .catch(function(error) {
                            alert("Error saving the image.");
                        })
                    });
                })
            </script>
        </div>
        <br>
        <div class="card">
            <div class="card-block">
                <div class="btn-group btn-group-fullwidth" role="group" aria-label="...">
                    <button class="btn btn-success" role="button" id="saveItem">Save</button>
                    <a class="btn btn-danger"  role="button" id="deleteItem" href="deleteitem.php?item=<?php echo $food->item["id"];?>">Delete</a>
                </div>
                <a class="btn btn-primary btn-block" href="item.php?item=<?php echo $food->item["id"];?>" role="button">View</a>
                <script>
                    $(document).ready(function() {
                        $("#saveItem").click(function() {
                            $("#saveItem").html('<span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>')
                            var title = $("#foodTitle").val();
                            var desc = $("#description").val();
                            var expiry = $("#food-expiry-date").val();
                            var lat = $("#lat").text();
                            var long = $("#long").text();
                            var imageurl = $("#foodimage").attr("src");
                            console.log(imageurl)
                            $.post("api/editfood.php",{id:<?php echo $food->item["id"];?>, title: title, desc: desc, expiry: expiry,lat:lat,long:long,imageurl:imageurl})
                            .done(function(data) {
                                if (data.hasOwnProperty("success")) {
                                    if (data.success) {
                                        success()
                                        $("#saveItem").html('Save')
                                    } else {
                                        alert("There was an error, and your item was not saved.");
                                    }
                                }
                            })
                            .fail(function(data) {
                                alert("There was an error, and your item was not saved.");
                            });
                        });
                        $("#deleteItem").click(function(e) {
                            var response = window.confirm("Are you sure you want to delete the item? This is undoable!");
                            if (response == false) {
                                return false;
                            }
                        })
                    });
                </script>
            </div>
        </div>
    </div>
    <div class="col-sm-9">
        <div class="card food-details">
            <div class="card-block">
                <input class="card-title food-title-edit" value="<?php echo $food->item["name"]; ?>" placeholder="<?php echo $food->item["name"]; ?>" id="foodTitle">
                <textarea id="description" class="card-text food-description-edit"><?php echo $food->item["description"]; ?></textarea>
            </div>
            <div class="card-footer text-muted">
                Expires: <input id="food-expiry-date" class="food-expiry-edit" value="<?php echo $food->item["expiry"]; ?>">
                <script>
                $(document).ready(function() {
                    $('.food-expiry-edit').datepicker({
                        dateFormat: 'yy-mm-dd'
                    });
                });
                </script>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card inline-map">
                    <div id="map" style="height: 500px;"></div>
                    <script>
                    $(document).ready(function() {
                        var mapDiv = document.getElementById('map');
                        var map = new google.maps.Map(mapDiv, {
                            center: new google.maps.LatLng(<?php echo $food->item["latitude"] ?>,<?php echo $food->item["longitude"] ?>),
                            zoom: 15,
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        });
                        var marker = new google.maps.Marker({
                            position: new google.maps.LatLng(<?php echo $food->item["latitude"] ?>,<?php echo $food->item["longitude"] ?>),
                            map: map
                        });
                        google.maps.event.addListener(map, 'click', function(event){
                            var newPos = new google.maps.LatLng(event.latLng.lat(),event.latLng.lng());
                            marker.setMap(null)
                            marker = new google.maps.Marker({
                                position: newPos,
                                map: map
                            });
                            map.panTo(newPos);
                            $("#lat").text(event.latLng.lat());
                            $("#long").text(event.latLng.lng());
                        });
                    });
                    </script>
                    <div class="card-footer text-muted">
                        Click map to select location
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        More Details
                    </div>
                    <div class="card-block">
                        <h3>Location</h3>
                        Latitude: <strong id="lat"><?php echo $food->item["latitude"] ?></strong><br>
                        Longitude: <strong id="long"><?php echo $food->item["longitude"] ?></strong>
                        <p class="text-muted">Click the map to select a location</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/editprofile.js"></script>
<?php
    $p->buildFooter();
?>