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
    <div class="masonry">
        <div class="card">
            <img class="card-img-top" src="http://lorempixel.com/400/200/food/">
            <div class="card-block">
                <h4 class="card-title">Card title</h4>
                <p class="card-text">Some quick example text to build on the card title.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
        <div class="card">
            <img class="card-img-top" src="http://lorempixel.com/400/200/food/">
            <div class="card-block">
                <h4 class="card-title">Card title really really really really really really really really really really long</h4>
                <p class="card-text">This defines the alignment along the main axis. It helps distribute extra free space left over when either all the flex items on a line are inflexible, or are flexible but have reached their maximum size. It also exerts some control over the alignment of items when they overflow the line..</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
        <div class="card">
            <img class="card-img-top" src="http://lorempixel.com/400/200/food/">
            <div class="card-block">
                <h4 class="card-title">Card title</h4>
                <p class="card-text">This allows the default alignment (or the one specified by align-items) to be overridden for individual flex items.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
        <div class="card">
            <img class="card-img-top" src="http://lorempixel.com/400/200/food/">
            <div class="card-block">
                <h4 class="card-title">Card title</h4>
                <p class="card-text">Note that visually the spaces aren't equal, since all the items have equal space on both sides. The first item will have one unit of space against the container edge, but two units of space between the next item because that next item has its own spacing that applies.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
        <div class="card">
            <div class="card-block">
                <h4 class="card-title">Card without an image header</h4>
                <p class="card-text">This defines the default behaviour for how flex items are laid out along the cross axis on the current line. Think of it as the justify-content version for the cross-axis (perpendicular to the main-axis).</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
        <div class="card">
            <img class="card-img-top" src="http://lorempixel.com/400/200/food/">
            <div class="card-block">
                <h4 class="card-title">Card title</h4>
                <p class="card-text">stretch (default): stretch to fill the container (still respect min-width/max-width)</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
    </div>
<?php
    $p->buildFooter();
?>