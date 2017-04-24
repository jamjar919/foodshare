var messageSettings = {
    sentTo: null,
    from: null,
    container: '#message-container',
    conversationContainer: '#conversations',
}
var loadedConversations = [];
var loadedMessages = [];
var endpoint = "api/messages.php";
function sendMessage(message) {
	
	
    // this should ajax the endpoint to update the database
    // but for now it just adds the message to the DOM
	console.log(messageSettings.from);
	console.log(messageSettings.sentTo);
    if (messageSettings.sentTo != null) {
		$.post(endpoint, {from: messageSettings.from, sendTo: messageSettings.sentTo, text: message, read: 0, message_type: 1})
    }
}
function addMessage(text,timestamp,isOwnMessage = false) {
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
            if (conversation.sender_username == messageSettings.from) { 
				if(loadedConversations.indexOf(conversation.receiver_username) == -1 ){
					loadedConversations.push(conversation.receiver_username);
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
            }else if(conversation.receiver_username == messageSettings.from) {
				if(loadedConversations.indexOf(conversation.sender_username) == -1 ){
					loadedConversations.push(conversation.sender_username);
					$(messageSettings.conversationContainer).append(
						$("<li>")
						.addClass("list-group-item")
						.append(
							$("<a>")
							.attr("href","messages.php?user="+conversation.sender_username)
							.addClass(messageSettings.sentTo == conversation.sender_username ? "current-message-receiver" : "")
							.text(conversation.sender_username)
						)
					)
				}
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
			if (loadedMessages.indexOf(message.id) == -1){
				addMessage(message.text, message.time, message.sender_username == messageSettings.from);
				loadedMessages.push(message.id);
				scrollToBottom();
			}
        }
    });
}
function scrollToBottom() {
    $(messageSettings.container).animate({
        scrollTop: $(messageSettings.container).get(0).scrollHeight
    }, 100);
}
