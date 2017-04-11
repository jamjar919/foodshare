var messageSettings = {
    sentTo: 'defaultto',
    from: 'defaultfrom',
    container: '#message-container'
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
}