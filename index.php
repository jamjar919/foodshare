<?php
    require 'class/Page.class.php';
    $p = new Page("Home");
    $p->buildHead();
    $p->buildHeader();
?>
    <div class="jumbotron">
        <h1>Hi, We're FlavourTown</h1>
        <p>We're aiming to rid the world of wasted food.</p>
        <p><a class="btn btn-primary btn-lg" href="register.php" role="button">Sign up now!</a></p>
    </div>
    <h2>See what's on offer...</h2>
    <div class="inline-searchbar">
        <div class="input-group">
            <input class="form-control" type="text" placeholder="Search food">
            <div class="input-group-btn">
                <div class="btn-group" role="group">
                    <div class="dropdown dropdown-lg">
                        <button id="search" class="btn btn-custom"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="masonry" id="frontpageitems">
    </div>
    <script>
        $(document).ready(function () {
            $.get("api/food.php?q=&location=54.7786523%2C-1.5614863&distance=30&expiry=Any%20time&time=Any%20time&sort=Alphabetical&num=10&offset=0")
            .then(function(data) {
                printFoodItems(data["food"],"#frontpageitems")
            }) 
        })
    </script>
<?php
    $p->buildFooter();
?>