<?php 
/**
 * Frequently asked questions page.
 */
?>
<?php
    define('__ROOT__',dirname(__FILE__));
    require __ROOT__.'/class/Page.class.php';
    $p = new Page("Help and FAQ's");
    $p->buildHead();
    $p->buildHeader();
?>
    <div class="row">
        <div id="collapsible-panels" class="col-md-6">
                <h1>FAQs</h1>
            <h2><a href="#">How do I add a new item?</a></h2>
            <div class="card card-block">
                <p class="card-text">To add a new food item, go to your profile page and simply click on the add item button and fill out the required details.</p>
            </div>
            <h2><a href="#">How do I claim a food item?</a></h2>
            <div class="card card-block">
                <p class="card-text">To claim a food item, find an item of your choice and simply click on the claim button.</p>
            </div>
            <h2><a href="#">How do I arrange for a pickup?</a></h2>
            <div class="card card-block">
                <p class="card-text">Once you have claimed a food item, you will be directed to the messages area where you can send messages to the item's owner and arrange for a pickup date and time.</p>
            </div>
            <h2><a href="#">Can I change my profile details?</a></h2>
            <div class="card card-block">
                <p class="card-text">Yes you can! Just click on the edit profile button and make the necessary changes.</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="jumbotron">
                <h2>Have a question which is not answered above?</h2>
                <p>Email us at <a href="mailto:cs-seg1@durham.ac.uk">help@flavourtown.co.uk</a></p>
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