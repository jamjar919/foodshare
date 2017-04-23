<?php 
/**
 * Displays messages. Use the parameter ?user=val to view messages between the currently logged in user and val.
 */
?>
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
                <ul class="list-group list-group-flush" id="conversations">
                </ul>
            </div>
        </div>
        <div class="col-sm-9">
            <div class="messages" id="message-container">
            </div>
            <div class="message-dialog">
                <input type="text" class="form-control" placeholder="Type a message..." id="messageBox">
                <input type="submit" class="btn btn-primary form-control" value="Send" id="sendMessage">
            </div>
        </div>
    </div>

    <script>
        function sendMessageWrapper() {
            var message = $('#messageBox').val();
            $('#messageBox').val("")
            sendMessage(message)
        }
        $(document).ready(function() {
            <?php if (isset($_GET["user"])) { ?>
                messageSettings.sentTo = "<?php echo $_GET["user"]; ?>";
            <?php } ?>
            messageSettings.from = "<?php echo $profile['username']; ?>";
            // Bind keypress event
            $('#messageBox').keyup(function(e){
                if(e.keyCode == 13) {
                    sendMessageWrapper();
                }
            });
            $('#sendMessage').click(function() {
                sendMessageWrapper();
            });
            // Load conversations
            loadConversations();
            // Load messages
            loadMessages();
			setInterval(function(){
				loadConversations();
			loadMessages();}
				,1000);
			
        })
    </script>
    <script src="js/messages.js"></script>
<?php
    $p->buildFooter();
?>