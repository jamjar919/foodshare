/*
 * Prints items as a card into the specified element
 */
function printFoodItems(items, element, isOwner) {
    isOwner = isOwner || false;
    if (items.length > 0) {
        for (var i = 0; i< items.length; i++) {
            item = items[i];
            var currentDate = new Date();
            $(element).append(
                $("<div>")
                .addClass("card food-item")
                .append((item["claimer_username"] != "") ? "<div class=\"card-header\">Claimed by "+((isOwner) ? "<a href=\"messages.php?user="+item["claimer_username"]+"\">"+item["claimer_username"]+"</a>" : item["claimer_username"])+"</div>" : "")
                .append(item["image_url"] ? '<img class="card-img-top" src="'+item["image_url"]+'">' : '')
                .append(
                    $("<div>")
                    .addClass("card-block")
                    .append(
                        $("<h4>").text(decodeEntities(item["name"]))
                    )
                    .append(
                        $("<p>")
                        .addClass("card-text")
                        .text(decodeEntities(item["description"].substring(0,300)))
                    )
                    .append(
                        $("<div>")
                        .addClass("btn-group btn-group-fullwidth")
                        .append(
                            $("<a>")
                            .attr("href","item.php?item="+item["id"])
                            .addClass("btn btn-primary")
                            .text("View")
                        )
                        .append((isOwner) ? "<a href=\"edititem.php?item="+item["id"]+"\" class=\"btn btn-warning\">Edit</a>" : "")
                    )
                )
                .append(
                    $("<div>")
                    .addClass("card-footer text-muted")
                    .text(((moment(item["expiry"]).isAfter(currentDate)) ? "Expires ": "Expired ")+moment(item["expiry"]).fromNow())
                )
            );
        }
    } else {
        $(element).append('<p class="no-items-text">No items to display</p>')
    }
}

function decodeEntities(encodedString) {
    var textArea = document.createElement('textarea');
    textArea.innerHTML = encodedString;
    return textArea.value;
}

/**
 * Claim an item for yourself. Uses a cookie and stuff to verify the claimer 
 **/
function claimItem(id) {
    return new Promise(function(resolve,reject) {
        $.post("api/claim.php", {id:id})
        .done(function(data) {
            if (data.hasOwnProperty("error")) {
                reject(data);
            }
            resolve(data);
        })
        .fail(function(data) {
            reject(data);
        });
    });
}

function unclaim(id) {
    return new Promise(function(resolve,reject) {
        $.post("api/unclaim.php", {id:id})
        .done(function(data) {
            if (data.hasOwnProperty("error")) {
                reject(data);
            }
            resolve(data);
        })
        .fail(function(data) {
            reject(data);
        });
    });
}

/**
 * Mark a food item as gone or not gone
 **/
function markGone(id) {
    return new Promise(function(resolve,reject) {
        $.post("api/gone.php", {id:id, val:true})
        .done(function(data) {
            if (data.hasOwnProperty("error")) {
                reject(data);
            }
            resolve(data);
        })
        .fail(function(data) {
            reject(data);
        });
    });
}

function markNotGone(id) {
    return new Promise(function(resolve,reject) {
        $.post("api/gone.php", {id:id,val:0})
        .done(function(data) {
            if (data.hasOwnProperty("error")) {
                reject(data);
            }
            resolve(data);
        })
        .fail(function(data) {
            reject(data);
        });
    });
}

/**
 * Makes a fancy success checkbox animation.
 */
function success() {
    var id = "success-overlay-"+Math.floor(Math.random() * (10000 - 0)) + 0;
    $(document.body).append(
        $("<div>")
        .attr("id",id)
        .addClass("overlay success-overlay")
        .css("opacity",0)
        .append('<span class="glyphicon glyphicon-ok"></span>')
        .fadeTo(50,1,function() {
            id = "#"+id;
            $(id+" .glyphicon")
            .addClass("pulsetick-anim")
            // time here should be equal to the animation length
            setTimeout(function() {
                $(id)
                .fadeTo(100,0,function() {
                    $(id).remove();
                })
            },750)
        })
    );
}
