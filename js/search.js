$('document').ready(function() {
    $("#radius").slider( {
        formatter: function(value) {
            return 'Current value: ' + value;
        }
    });
    $("#radius").on("slide", function(slideEvt) {
        $("#radiusSliderVal").text(slideEvt.value);
    });
});

$('#search').click(function(){
    var location = [$('#lat').val(), $('#lng').val()];
    search($('#q').val(), location, $('#sort').val(), $('#radius').val(), $('#resultsPerPage').val(), $('#pageNumber').val());
});

function search(q, location, sort, distance, results, page) {
    //bind id to json topic
    var parameters = { q:q,  location: location, sort: sort, distance: distance, num: results, offset: page};
    console.log(parameters);
    $.getJSON("api/food.php", parameters, function(data) {
        console.log(data);
        var i = 0;
        var foodInfo = $('<div></div>').addClass('food');
        if(data.food.length > 0) {
            $.each(data.food, function (key, element) {
                var address = convertGeocode(element['latitude'], element['longitude']);
                var p = Promise.resolve(address);
                p.then(function(address) {
                    foodInfo.append("<p>Name: " + element['name'] + "</p>" +
                        "<p>Description: " + element['description'] + "</p>" +
                        "<p>Expiry date: " + element['expiry'] + "</p>" +
                        "<p>Submition date: " + element['time'] + "</p>" +
                        "<p>Latitude: " + address + "</p>" +
                        "<br>"
                    );
                });

                i += 1;
            });
        }
        else {
            foodInfo.append('<p style="text-align: center">No food found</p>');
        }
        $('#results').html(foodInfo);
    });
}

function convertGeocode(latitude, longitude) {
    //https://maps.googleapis.com/maps/api/geocode/json?region=uk&address=Durham
    return new Promise(function (resolve, reject) {
        var params = {
            "latlng": latitude, longitude,
            "key": "AIzaSyBSS0BvM51P-qtCBr0o8-Yw25VrPBh5qhg"
        };
        $.get("https://maps.googleapis.com/maps/api/geocode/json?latlng=" + latitude + "," + longitude + "&sensor=true").done(function (data) {
            console.log(data);
            data = data["results"][0]["formatted_address"];
            resolve(data);
        }).fail(function (data) {
            reject(data);
        });
    });
}
