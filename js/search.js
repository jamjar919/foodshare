var q, storedLocation, radius, expiry, time, sort, resultsPerPage, pageNumber, totalResults;
window.storedLocation= [];
window.radius = 20;
window.expiry = "Any time";
window.time = "Any time";
window.sort = "Best match";
window.resultsPerPage = 15;
window.pageNumber = 0;
window.totalResults = 0;

var mymap;
var markers = [];

$('document').ready(function() {
    $("#radius").bootstrapSlider( {
        formatter: function(value) {
            return 'Current value: ' + value;
        }
    });
    $("#radius").on("slide", function(slideEvt) {
        $("#radiusSliderVal").text(slideEvt.value);
    });
    $("#q1").keyup(function(event){
        if(event.keyCode == 13){
            $("#search").click();
        }
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

//daterangepicker setup for expiry date
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

//datetimerange setup for time posted
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

//advanced search
$('#searchAdvanced').click(function(e){
    e.preventDefault();
    $("#dlDropDown").dropdown("toggle");
    $('#map-container').html('');
    geocode($("#loc").val(), function(pos) {
        q = $('#q2').val();
        storedLocation = pos;
        radius = $('#radius').val();
        expiry = $('#expiry').val();
        time = $('#time').val();
        sort = $('#sort').val();
        resultsPerPage = $('#resultsPerPage').val();
        pageNumber = 0;
        if(expiry != "Any time") {
            expiry = [expiry.slice(0, 10), expiry.slice(13,23)]
        }
        if(time != "Any time") {
            time = [time.slice(0, 16), time.slice(19,35)]
        }

        search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, 0, true);
        //remove pagination
        $('.pagination').html("");
    });

});
//basic search
$('#search').click(function() {
    $('#map-container').html('');
    geocode("durham", function(pos) {
        q = $('#q1').val();
        storedLocation = pos;
        expiry = "Any time";
        time = "Any time";
        radius = 15;
        sort = "Best match";
        resultsPerPage = 4;
        pageNumber = 0;

        search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, 0, true);
        //remove pagination
        $('.pagination').html("");
    });
});

//pagination links
$('.pagination').on('click', '#next', function() {
    pageNumber += 1;
    offset = pageNumber * resultsPerPage;
    search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, false);
});
$('.pagination').on('click', '#link1', function() {
    pageNumber = parseInt($("#link1 a").text()) -1;
    offset = pageNumber * resultsPerPage;
    search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, false);
});
$('.pagination').on('click', '#link2', function() {
    pageNumber = parseInt($("#link2 a").text()) -1;
    offset = pageNumber * resultsPerPage;
    search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, false);
});
$('.pagination').on('click', '#link3', function() {
    pageNumber = parseInt($("#link3 a").text()) -1;
    offset = pageNumber * resultsPerPage;
    search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, false);
});
$('.pagination').on('click', '#prev', function() {
    pageNumber -= 1;
    offset = pageNumber * resultsPerPage;
    search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, false);
});

/**Update the pagination links
 *
 * @param page Page number
 */
function setPageLinks(page) {
    $(".pagination li").removeClass("active");
    $("#link1 a").text((page).toString());
    $("#link2 a").text((page+1).toString());
    $("#link3 a").text((page+2).toString());

    $("#link2").addClass("active");
}

//dynamically add pagination links
function addLinks() {
    //make map button visible
    $('#map-button').css('visibility', 'visible');

    var totalLinks = Math.ceil(totalResults / resultsPerPage);
    var paginationList = "";

    if(totalLinks > 1) {
        paginationList += '<li class="page-item" id="prev"><a class="page-link" href="#">Previous</a></li>';
        if(totalLinks > 3) {
            totalLinks = 3;
        }
        for(i = 0; i < totalLinks; i++) {
            if(i == totalLinks -1) {
                //keep track of final link
                paginationList += '<li class="page-item active final" id="link' + (i+1) + '"><a class="page-link" href="#">'
                    + (i+1) + '</a></li>';
            }
            else {
                paginationList += '<li class="page-item active" id="link' + (i+1) + '"><a class="page-link" href="#">'
                    + (i+1) + '</a></li>';
            }
        }
        paginationList += '<li class="page-item" id="next"><a class="page-link" href="#">Next</a></li>';
    }


    $('.pagination').html(paginationList);
}

/**Search for food items in db
 *
 * @param q Query
 * @param location
 * @param distance
 * @param expiry Array Expiry date of food range
 * @param time Array Time posted range
 * @param sort Sort method
 * @param results Number of results per page
 * @param page Page number
 * @param firstSearch Boolean
 */
function search(q, location, distance, expiry, time, sort, results, page, firstSearch) {
    //reset the markers list
    clearMarkers();
    var parameters = { q:q,  location: location, distance: distance, expiry: expiry, time: time, sort: sort, num: results, offset: page};
    $('#results').html('<img src="https://upload.wikimedia.org/wikipedia/commons/b/b1/Loading_icon.gif" ' +
        'style="display: block; margin: 0 auto; width: 200px; height: auto;"/>');
    $.getJSON("api/food.php", parameters, function(data) {
        var foodInfo = $('<div></div>').addClass('food');

        if(data.food.length > 0) {
            initMap(location);
            setMapBounds(location, distance);
            $.each(data.food, function (key, element) {
                var address = convertGeocode(element['latitude'], element['longitude']);
                var p = Promise.resolve(address);
                p.then(function(address) {
                    foodInfo.append("<p id='" + element['id'] + "'>Name: " + element['name'] + "</p>" +
                        "<p>Description: " + element['description'] + "</p>" +
                        "<p>Expiry date: " + element['expiry'] + "</p>" +
                        "<p>Submission date: " + element['time'] + "</p>" +
                        "<p>Address: " + address + "</p>" +
                        "<br>"
                    );
                    var myLatlng = new google.maps.LatLng(element['latitude'], element['longitude']);
                    var infowindow = new google.maps.InfoWindow({
                        content: popupDetails(element, address)
                    });

                    var marker = new google.maps.Marker({
                        position: myLatlng,
                        map: mymap,
                        title: element['name']
                    });

                    marker.addListener('click', function() {
                        if( prev_infowindow ) {
                            prev_infowindow.close();
                        }
                        prev_infowindow = infowindow;
                        infowindow.open(mymap, marker);
                    });
                    markers.push(marker);
                });
            });
            //only display the pagination links on initial search
            if(firstSearch) {
                totalResults = data.resultsCount;
                addLinks();
            }
            $('#next').css("visibility", "visible");
            if(pageNumber == 0) {
                $('#prev').css("visibility", "hidden");
                $(".pagination li").removeClass("active");
                $('#link1').addClass('active');
            }
            else {
                $('#prev').css("visibility", "visible");
            }
            if(pageNumber == Math.ceil(totalResults/resultsPerPage)-1) {
                $(".pagination li").removeClass("active");
                $('.final').addClass('active');
                $('#next').css("visibility", "hidden");

            }
            else if(pageNumber > 0) {
                setPageLinks(pageNumber)
            }
        }
        else {
            foodInfo.append('<p style="text-align: center">No food found</p>');
        }
        $('#results').html(foodInfo);
    });
}
/**Converts a geocode to the corresponding address
 *
 * @param latitude
 * @param longitude
 * @returns {Promise}
 */
function convertGeocode(latitude, longitude) {
    //https://maps.googleapis.com/maps/api/geocode/json?region=uk&address=Durham
    return new Promise(function (resolve, reject) {
        var params = {
            "latlng": latitude, longitude,
            "key": "AIzaSyBSS0BvM51P-qtCBr0o8-Yw25VrPBh5qhg"
        };
        $.get("https://maps.googleapis.com/maps/api/geocode/json?latlng=" + latitude + "," + longitude + "&sensor=true").done(function (data) {
            data = data["results"][0]["formatted_address"];
            resolve(data);
        }).fail(function (data) {
            reject(data);
        });
    });
}
/**Converts an address to its geocode
 *
 * @param position Address
 * @param callback
 */
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

//autocomplete for searching keywords
$(function() {
    $( "#q1" ).autocomplete({
        source: function( request, response ) {
            $.ajax({
                url: "api/getTag.php",
                dataType: "json",
                data: {
                    q: request.term
                },
                success: function( data ) {
                    return response(data.tags)
                }
            });
        },
        minLength: 3,
    });
});

//map stuff

var prev_infowindow;

/**Initialize the map centered at specified location
 *
 * @param pos Center position
 */
function initMap(pos) {
    var mapContent = '<div id="map"> </div>';
    $('#map-container').html(mapContent);
    $('#map').show('blind', {direction: 'up'}, 1000);
    $('#map-button').addClass('active')
        .css('border-radius', '5px 5px 0 0');
    var myLatlng = new google.maps.LatLng(pos[0], pos[1]);
    mymap = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: myLatlng,
        scrollwheel: false,
        disableDoubleClickZoom: true,
    });
    google.maps.event.addListener(mymap, 'click', function() {
        var pos = mymap.getCenter();
        console.log("ere");
        loadMapFood(q, pos, radius, expiry, time, sort, resultsPerPage, 0, function() {
            console.log("Loaded food at "+e.latlng);
        });
    });
}
/**Create details for pop up of a food item
 *
 * @param food Food data
 * @param address Address of food item
 * @returns {string}
 */
function popupDetails(food, address) {
    return '<div class="food-popup"><h3>'+food["name"]+'</h3>' +
        '<p>'+food["description"]+'</p>' +
        "<p>Expiry date: " + food['expiry'] + "</p>" +
        "<p>Submission date: " + food['time'] + "</p>" +
        "<p>Address: " + address + "</p>" +
        '<button class="btn btn-primary btn-sm" onClick=loadFullFood('+food["id"]+',this)>More</button></div>';
}

function clearMarkers() {
    for (var i=0; i<markers.length; i++) {
        markers[i].setMap(null);
    }
    markers = [];
}

//click event for showing and hiding the map
$("#map-button").on('click', function () {
    if($( "#map").is(':visible')) {
        $('#map').hide('blind',{direction:'up'}, 1000, function() {
            $('#map-button').removeClass('active')
                .css('border-radius', '5px')
                .text('Open map')
        });
    }
    else {
        $('#map').show('blind', {direction: 'up'}, 1000);
        $('#map-button').addClass('active')
            .css('border-radius', '5px 5px 0 0')
            .text('Close map')
    }
});
/**Set the map zoom to match the radius specified
 *
 * @param location Central location
 * @param radius Radius of search
 */
function setMapBounds(location, radius) {
    var myLatLng = new google.maps.LatLng(location[0],location[1]);
    var circleOptions = {
        center: myLatLng,
        fillOpacity: 0,
        strokeOpacity: 0,
        map: mymap,
        radius: (radius*1000) /* 20 miles */
    }
    var myCircle = new google.maps.Circle(circleOptions);
    mymap.fitBounds(myCircle.getBounds());
    zoomChangeBoundsListener =
        google.maps.event.addListenerOnce(mymap, 'bounds_changed', function(event) {
            var currentZoomLevel = mymap.getZoom();
            console.log("Current zoom level: " + currentZoomLevel);
            mymap.setZoom(currentZoomLevel+1);
        });

}
/**Scroll to the specified food
 *
 * @param id ID of food
 * @param button Button linked to specified food
 */
function loadFullFood(id, button) {
    $(button).click(function() {
        $('html, body').animate({
            scrollTop: $("#" + id).offset().top
        }, 1000);
    });
}
var page = $("html, body");

//stop animation when user scrolls

$( '.page-content' ).click(function(e) {
    page.on("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove", function(){
        page.stop();
    });
    return false;
});