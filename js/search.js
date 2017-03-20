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

var today = new Date();
var dd = today.getDate();
var mm = today.getMonth() + 1;
var yyyy = today.getFullYear();
if(dd < 10) {
    dd = "0" + dd
}
if(mm < 10) {
    mm = "0" + mm
}
today = yyyy + "-" + mm + "-" + dd;


//daterangepicker for expiry date and time posted
$(function() {

    $('input[name="daterange"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        },
        ranges: {
            'Today': [moment(), moment()],
            'This Week': [moment(), moment().add(6, 'days')],
            'This Month': [moment(), moment().endOf('month')],
            'This Year': [moment(), moment().endOf('year')],
        },
        autoclose: false
    }).on('click', function () {
        $('.daterangepicker').click(function (e) {
            e.stopPropagation(); // prevent clicks on datepicker from collapsing 'parent'
        });
    });

    $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    });

    $('input[name="daterange"]').on('cancel.daterangepicker', function() {
        $(this).val('Any time');
    });

});

$(function() {
    $('input[name="datetimerange"]').daterangepicker({
        timePicker: true,
        timePicker24Hour: true,
        timePickerIncrement: 30,
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        },
        ranges: {
            'Today': [moment(), moment()],
            'Last week': [moment().subtract(6, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment()],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        },
        autoclose: false
    }).on('click', function () {
        $('.daterangepicker').click(function (e) {
            e.stopPropagation(); // prevent clicks on datepicker from collapsing 'parent'
        });
    });

    $('input[name="datetimerange"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY HH:mm') + ' - ' + picker.endDate.format('DD/MM/YYYY HH:mm'));
    });

    $('input[name="datetimerange"]').on('cancel.daterangepicker', function() {
        $(this).val('Any time');
    });

});


//submitting information in search
$('#searchAdvanced').click(function(e){
    e.preventDefault();
    $("#dlDropDown").dropdown("toggle");
    geocode($("#loc").val(), function(pos) {
        console.log("here");
        console.log(pos);
        var expiry = $('#expiry').val();
        var time = $('#time').val();
        if(expiry != "Any time") {
            expiry = [expiry.slice(0, 10), expiry.slice(13,23)]
        }
        if(time != "Any time") {
            time = [time.slice(0, 16), time.slice(19,35)]
        }
        search($('#q2').val(), pos,  $('#radius').val(), expiry, time, $('#sort').val(),
            $('#resultsPerPage').val(), $('#pageNumber').val());
    });

});
$('#search').click(function() {
    var location = [54.7, -1.56];
    var sort = "Best match";
    var radius = 15;
    var resultsPerPage = 20;
    var pageNumber = 0;
    search($('#q2').val(), location, radius, "Any time", "Any time", sort, resultsPerPage, pageNumber);
});

function search(q, location, distance, expiry, time, sort, results, page) {
    //bind id to json topic
    var parameters = { q:q,  location: location, distance: distance, expiry: expiry, time: time, sort: sort, num: results, offset: page};
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

function geocode(position, callback) {
    //https://maps.googleapis.com/maps/api/geocode/json?region=uk&address=Durham
    var params = {
        "region": "uk",
        "address": position
    };
    $.get( "https://maps.googleapis.com/maps/api/geocode/json", params).done(function(data) {
        data = data["results"][0]["geometry"]["location"];
        callback([data["lat"],data["lng"]]);
    });
}

function tagSearch(q) {

}
