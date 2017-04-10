<?php
    define('__ROOT__',dirname(__FILE__));
    require __ROOT__.'/class/Page.class.php';
    $p = new Page("Help and FAQ's");
    $p->buildHead();
    $p->buildHeader();
?>
    <div class="row">
        <div id="collapsible-panels" class="col-md-6">
                <h1>FAQ's</h1>
            <h2><a href="#">Question</a></h2>
            <div class="card card-block">
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
            <h2><a href="#">Question</a></h2>
            <div class="card card-block">
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
            <h2><a href="#">Question</a></h2>
            <div class="card card-block">
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
            <h2><a href="#">Question</a></h2>
            <div class="card card-block">
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="jumbotron">
                <h2>Still more questions?</h2>
                <p>Email us at <a href="mailto:gofuckyourself@flavourtown.com">gofuckyourself@flavourtown.com</a></p>
            </div>
        </div>
    </div>
    <script>
       /* $(function () {
            $.getScript('js/collapse.js');
        });*/
    </script>
<?php
    $p->buildFooter();
?>