var messageSettings = {
    sentTo: null,
    from: null,
    container: '#message-container',
    conversationContainer: '#conversations'
}
var endpoint = "api/messages.php";
function sendMessage(message) {
	var formData = new FormData();
	formData.append("conversation_id", 3);
	formData.append("text", message);
	formdata.append("read", 0);
	formData.append("message_type", 1);
	var request = new XMLHttpRequest();
	request.open("POST", endpoint);
	request.send(formData)
	
    // this should ajax the endpoint to update the database
    // but for now it just adds the message to the DOM
    if (messageSettings.sentTo != null) {
        addMessage(message, new Date(), true);
    }
}
function addMessage(text,timestamp,isOwnMessage) {
    isOwnMessage = isOwnMessage || false;
    $(messageSettings.container).append(
        $("<div>")
        .addClass("card message")
        .addClass(isOwnMessage ? "to" : "from")
        .append(
            $("<div>")
            .addClass("card-header")
            .text(isOwnMessage ? messageSettings.from : messageSettings.sentTo)
            .append(
                $("<span>")
                .addClass("converttime message-timestamp")
                .text(timestamp)
            )
        )
        .append(
            $("<div>")
            .addClass("card-block")
            .text(text)
        )
    );
    convertTimes();
    scrollToBottom();
}
function loadConversations() {
    $.get(endpoint)
    .then(function(data) {
        for (var i = 0; i < data.conversations.length; i++) {
            var conversation = data.conversations[i];
            console.log(conversation);
            if (conversation.sender_username == messageSettings.from) { 
                $(messageSettings.conversationContainer).append(
                    $("<li>")
                    .addClass("list-group-item")
                    .append(
                        $("<a>")
                        .attr("href","messages.php?user="+conversation.receiver_username)
                        .addClass(messageSettings.sentTo == conversation.receiver_username ? "current-message-receiver" : "")
                        .text(conversation.receiver_username)
                    )
                )
            }
        }
    });
}
function loadMessages() {
    if (messageSettings.sentTo == null) {
        $(messageSettings.container).text("Select a recipient from the list to the left.")
    }
    $.get(endpoint, {user: messageSettings.sentTo})
    .then(function(data) {
        for (var i = 0; i < data.messages.length; i++) {
            var message = data.messages[i];
            addMessage(message.text, message.time, message.sender_username == messageSettings.from);
        }
        scrollToBottom();
    });
}
function scrollToBottom() {
    $(messageSettings.container).animate({
        scrollTop: $(messageSettings.container).get(0).scrollHeight
    }, 100);
}