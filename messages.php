<?php
    define('__ROOT__',dirname(__FILE__));
    require __ROOT__.'/class/Page.class.php';
    $p = new Page("Messages", true);
    $p->buildHead();
    $p->buildHeader();
    $profile = $p->user->getPrivateProfile();
?>
    <div class="row">
        <div class="col-sm-3">
            <div class="card">
                <div class="card-header text-center">
                    Messenger
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">User 1</li>
                    <li class="list-group-item">User 2</li>
                    <li class="list-group-item">User 3</li>
                </ul>
            </div>
        </div>
        <div class="col-sm-9 messages" id="message-container">
        </div>
    </div>
    <script>
        $(document).ready(function() {
            addMessage("Example message from someone", "2014-11-22 12:45:34", false)
            addMessage("Example message to someone", "2014-11-22 12:45:34", true)
        })
    </script>
    <script src="js/messages.js"></script>
<?php
    $p->buildFooter();
?>