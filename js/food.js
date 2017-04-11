/*
 * Prints items as a card into the specified element
 */
function printFoodItems(items, element) {
    for (var i = 0; i< items.length; i++) {
        item = items[i];
        $(element).append(
            $("<div>")
            .addClass("card food-item")
            .append(item["image_url"] ? '<img class="card-img-top" src="'+item["image_url"]+'">' : '')
            .append(
                $("<div>")
                .addClass("card-block")
                .append(
                    $("<h4>").text(item["name"])
                )
                .append(
                    $("<p>")
                    .addClass("card-text")
                    .text(item["description"].substring(0,300))
                )
                .append(
                    $("<div>")
                    .addClass("btn-group")
                    .append(
                        $("<a>")
                        .attr("href","item.php?item="+item["id"])
                        .addClass("btn btn-primary")
                        .text("View")
                    )
                    .append(
                        $("<a>")
                        .attr("href","edititem.php?item="+item["id"])
                        .addClass("btn btn-primary")
                        .text("Edit")
                    )
                )
            )
            .append(
                $("<div>")
                .addClass("card-footer text-muted")
                .text("Expires "+moment(item["expiry"]).fromNow())
            )
        );
    }
}