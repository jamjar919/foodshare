var q, storedLocation, radius, expiry, time, sort, resultsPerPage, pageNumber, totalResults, address;
window.storedLocation= [];
window.radius = 20;
window.expiry = "Any time";
window.time = "Any time";
window.sort = "Best match";
window.resultsPerPage = 15;
window.pageNumber = 0;
window.totalResults = 0;
window.address = "";

var mymap;
var markers = [];
var user;
var memberSearch;

//get user's location from browser (non-members only)
var initialPosition = null;
var initialAddress = "";

$('document').ready(function() {
    if(window.location.hash) {
        addContainers();
        configureBootstrap();

        q = getAllUrlParams().q;
        storedLocation = getAllUrlParams().location;
        radius = getAllUrlParams().distance;
        expiry = getAllUrlParams().expiry;
        time = getAllUrlParams().time;
        sort = getAllUrlParams().sort;
        resultsPerPage = getAllUrlParams().num;
        offset = getAllUrlParams().offset;
        pageNumber = (offset/resultsPerPage);

        newaddress = convertGeocode(storedLocation.split(',')[0],storedLocation.split(',')[1] );
        var p = Promise.resolve(newaddress);
        p.then(function(newaddress) {
            address = newaddress;
            updateSideBar(q, newaddress, radius, expiry, time, sort, resultsPerPage);
            saveState(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset,newaddress);
            search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, pageNumber)
        });

    }


    $("#q1").keyup(function(event){
        if(event.keyCode == 13){
            $("#search").click();
        }
    });
    userData = getUser();
    var p = Promise.resolve(userData);
    p.then(function(userData) {
        user = userData;
        memberSearch = true;
        loadSearch();
    },function() {
        memberSearch = false;
        if (navigator.geolocation && !memberSearch){
            navigator.geolocation.getCurrentPosition(
                function(position){
                    initialPosition = position.coords.latitude + "," +position.coords.longitude;
                    console.log("Got initial position as: "+initialPosition);
                    var address = convertGeocode(initialPosition.split(',')[0], initialPosition.split(',')[1]);
                    var p = Promise.resolve(address);
                    p.then(function(address) {
                        initialAddress = address;
                        loadSearch();
                    });
                },
                function(error){
                    // If we don't find the initial position just go L O N D O N
                    initialPosition = 51.5 + "," -0.09;
                    console.log("Error getting pos: "+error);
                    var address = convertGeocode(initialPosition.split(',')[0], initialPosition.split(',')[1]);
                    var p = Promise.resolve(address);
                    p.then(function(address) {
                        initialAddress = address;
                        loadSearch();
                    });
                }
            )
        }
    })

});

function loadSearch() {
    //basic search
    $('#search').click(function() {
        addContainers();
        configureBootstrap();
        $('#map-container').html('');

        if(memberSearch) {
            storedLocation = user['latitude'] + "," +  user['longitude'];
            address = user['postcode'];
        }
        else {
            storedLocation = initialPosition;
            address = initialAddress;
        }
        q = $('#q1').val();
        expiry = "Any time";
        time = "Any time";
        radius = 10;
        sort = "Closest";
        resultsPerPage = 8;
        pageNumber = 0;

        //remove pagination
        $('.pagination').html("");

        updateSideBar(q, address, radius, expiry, time, sort, resultsPerPage);
        saveState(q, storedLocation, radius, expiry, time, sort, resultsPerPage, 0, address);
        search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, 0, 0);


    });
}

var today = new Date();
var monthNames = ["January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];



$('#advanced').click(function() {
    $('.content').html(addSideSearchbar(6));
    configureBootstrap();
    $('.advancedSearchbar').addClass('col-centered');
    if(memberSearch) {
        storedLocation = user['latitude'] + "," +  user['longitude'];
        address = user['postcode'];
    }
    else {
        storedLocation = initialPosition;
        address = initialAddress;
    }
    q = $('#q1').val();
    expiry = "Any time";
    time = "Any time";
    radius = 15;
    sort = "Best match";
    resultsPerPage = 4;
    pageNumber = 0;
    updateSideBar(q, address, radius, expiry, time, sort, resultsPerPage);
    history.pushState('advancedSearch', "", '');

});

//advanced search
$(document.body).on('click', '#advancedSearch', function(e) {
    e.preventDefault();
    var address;
    if($('#loc').val() === "") {
        address = "-"
    }
    else {
        address = $('#loc').val();
    }
    q = $('#q2').val();
    radius = $('#radius').val();
    expiry = $('#expiry').val();
    time = $('#time').val();
    sort = $('#sort').val();
    resultsPerPage = $('#resultsPerPage').val();
    addContainers();
    configureBootstrap();
    $('#map-container').html('');


    geocode(address, function(pos) {
        storedLocation = pos;
        pageNumber = 0;
        if(expiry != "Any time") {
            expiry = expiry.slice(0, 10) + "," + expiry.slice(13,23)
        }
        if(time != "Any time") {
            time = time.slice(0, 16) + "," + time.slice(19,35)
        }

        //remove pagination
        $('.pagination').html("");

        updateSideBar(q, address, radius,expiry, time, sort, resultsPerPage);
        saveState(q, storedLocation, radius, expiry, time, sort, resultsPerPage, 0, address);
        search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, 0, 0);
    });
});



//pagination links
$(document.body).on('click', '#next', function(e) {
    e.preventDefault();
    pageNumber += 1;
    offset = pageNumber * resultsPerPage;
    saveState(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, address);
    search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, pageNumber);
});
$(document.body).on('click', '#link1', function(e) {
    e.preventDefault();
    pageNumber = parseInt($("#link1 a").text()) -1;
    offset = pageNumber * resultsPerPage;
    saveState(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, address);
    search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, pageNumber);
});
$(document.body).on('click', '#link2', function(e) {
    e.preventDefault();
    pageNumber = parseInt($("#link2 a").text()) -1;
    offset = pageNumber * resultsPerPage;
    saveState(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, address);
    search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, pageNumber);
});
$(document.body).on('click', '#link3', function(e) {
    e.preventDefault();
    pageNumber = parseInt($("#link3 a").text()) -1;
    offset = pageNumber * resultsPerPage;
    saveState(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, address);
    search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, pageNumber);
});
$(document.body).on('click', '#prev', function(e) {
    e.preventDefault();
    pageNumber -= 1;
    offset = pageNumber * resultsPerPage;
    saveState(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset,address);
    search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, offset, pageNumber);
});

function addContainers() {
    if(!$(".page-content")[0]) {
        var container = $('<div>')
            .addClass("row page-content")
            .append(
                addSideSearchbar(4)
            )
            .append(
                '<div class="col-md-7">' +
                '<button id="map-button" class="btn btn-custom btn-block active">Close map</button>' +
                '<div id="map-container">' +
                '</div>' +
                '<div id="results">' +
                '</div>' +
                '<div class="text-center">' +
                '<ul class="pagination" ></ul>' +
                '</div>' +
                '</div>' +
                '</div>'
            );
        $('.content').html(container);
    }
}

//create search bar
function addSideSearchbar(colwidth) {
    return   $("<div>")
        .addClass("col-md-" + colwidth + " advancedSearchbar")
        .append(
            $("<div>")
                .addClass("card")
                .append(
                    $("<div>")
                        .addClass("card-block")
                        .append(
                            $("<h3>").text("Advanced search")
                        )
                        .append(
                            $("<form>")
                                .addClass("form-horizontal")
                                .append(
                                    $("<div>")
                                        .addClass("form-group")
                                        .append(
                                            "<label for='q2'>Key words</label>"
                                        )
                                        .append(
                                            "<input class='form-control' type='text' id='q2'/>"
                                        )
                                )
                                .append(
                                    $("<div>")
                                        .addClass("form-group")
                                        .append(
                                            "<label for='sort'>Sort by</label>"
                                        )
                                        .append(
                                            "<select class='form-control' id='sort' >" +
                                                "<option>Alphabetical</option>" +
                                                "<option>Best match</option>" +
                                                "<option>Time: newest first</option>" +
                                                "<option>Time: oldest first</option>" +
                                                "<option>Expiry: earliest</option>" +
                                                "<option>Expiry: latest</option>" +
                                                "<option>Closest</option>" +
                                            "</select>"
                                        )
                                )
                                .append(
                                    $("<div>")
                                        .addClass("form-group")
                                        .append(
                                            "<label for='loc'>Location</label>"
                                        )
                                        .append(
                                            "<input class='form-control' type='text' id='loc'/>"
                                        )
                                )
                                .append(
                                    $("<div>")
                                        .addClass("form-group")
                                        .append(
                                            "<label for='radius' class='col-2 col-form-label'>Radius (km)</label>"
                                        )
                                        .append(
                                            "<div class='col- " + colwidth + " col-centered'>" +
                                            "<b>0</b> <input style='width: 85%' id='radius' data-slider-id='radiusSlider' type='text' data-slider-min='0' " +
                                            "data-slider-max='30' data-slider-step='1' data-slider-value='10'/>" +
                                            "<b>30</b>" +
                                            "<p id='radiusCurrentSliderValLabel'>Radius: <span id='radiusSliderVal'>10</span></p>" +
                                            "</div>"
                                        )
                                )
                                .append(
                                    $("<div>")
                                        .addClass("form-group")
                                        .append(
                                            "<label for='expiry'>Expiry date</label>"
                                        )
                                        .append(
                                            "<div class='col-4'>" +
                                            "<input class='form-control' id='expiry' name='daterange' type = 'text' value='Any time' style='width: 100%'>" +
                                            "</div>"
                                        )
                                )
                                .append(
                                    $("<div>")
                                        .addClass("form-group")
                                        .append(
                                            "<label for='time'>Time posted</label>"
                                        )
                                        .append(
                                            "<div class='col-4'>" +
                                            "<input class='form-control' id='time' name='daterange' type = 'text' value='Any time' style='width: 100%'>" +
                                            "</div>"
                                        )
                                )
                                .append(
                                    $("<div>")
                                        .addClass("form-group")
                                        .append(
                                            "<label for='resultsPerPage'>Results per page</label>"
                                        )
                                        .append(
                                            "<input class='form-control' type='text' id='resultsPerPage'/>"
                                        )
                                )
                                .append(
                                    "<a type='button' class='btn btn-custom btn-block' id='advancedSearch'>Search <span class='glyphicon glyphicon-search' " +
                                    "aria-hidden='true'></span></a>"
                                )
                        )
                )

        )

}

function updateSideBar(q, location, distance, expiry, time, sort, resultsPerPage) {
    $('#q2').val(q);
    $('#loc').val(location);
    $('#radius').val(distance);
    $('#expiry').val(expiry);
    $('#time').val(time);
    $('#sort').val(sort);
    $('#resultsPerPage').val(resultsPerPage);
}

function configureBootstrap() {
    $("#radius").bootstrapSlider({
        formatter: function (value) {
            return 'Current value: ' + value;
        }
    });
    $("#radius").on("slide", function (slideEvt) {
        $("#radiusSliderVal").text(slideEvt.value);
    });

    //daterangepicker setup for expiry date

    $(function () {
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
        $('input[name="daterange"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        });
        $('input[name="daterange"]').on('cancel.daterangepicker', function () {
            $(this).val('Any time');
        });
    });

//datetimerange setup for time posted
    $(function () {
        $('input[name="datetimerange"]').daterangepicker({
            timePicker: true,
            timePicker24Hour: true,
            timePickerIncrement: 30,
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            },
            ranges: {
                'Today': [moment().startOf('day'), moment().endOf('day')],
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
        $('input[name="datetimerange"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY HH:mm') + ' - ' + picker.endDate.format('DD/MM/YYYY HH:mm'));
        });
        $('input[name="datetimerange"]').on('cancel.daterangepicker', function () {
            $(this).val('Any time');
        });
    });
    //configure autocomplete
    $("#q2").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "api/getTag.php",
                dataType: "json",
                data: {
                    q: request.term
                },
                success: function (data) {
                    return response(data.tags)
                }
            });
        },
        minLength: 3
    });
}

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


    var totalLinks = Math.ceil(totalResults / resultsPerPage);
    var paginationList = "";

    if(totalLinks > 1) {
        paginationList += '<li class="page-item" id="prev"><a class="page-link btn-custom" href="#">Previous</a></li>';
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
        paginationList += '<li class="page-item btn-custom" id="next"><a class="page-link " href="#">Next</a></li>';
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
 * @param resultsPerPage Number of results per page
 * @param pageNumber Page number
 * @param firstSearch Boolean
 */
function search(q, location, distance, expiry, time, sort, resultsPerPage, offset, pageNumber) {
    //reset the markers list
    $('#map-button').css("visibility", "hidden");
    clearMarkers();
    $('#map-container').html("");
    var parameters = { q:q,  location: location, distance: distance, expiry: expiry, time: time, sort: sort, num: resultsPerPage, offset: offset};
    $('#results').html('<img src="https://upload.wikimedia.org/wikipedia/commons/b/b1/Loading_icon.gif" ' +
        'style="display: block; margin: 0 auto; width: 200px; height: auto;"/>');
    $.getJSON("api/food.php", parameters, function(data) {
        var foodInfo = $('<div></div>').addClass('food');
        var currentDate = new Date();
        if(data.hasOwnProperty('error')) {

            foodInfo.append('<p style="text-align: center">No food found</p>');
        }
        else if(data.food.length > 0) {
            $('#map-button').css("visibility", "visible");
            initMap(location.split(","));
            setMapBounds(location.split(","), distance);
            $.each(data.food, function (key, element) {
                var address = convertGeocode(element['latitude'], element['longitude']);
                var p = Promise.resolve(address);
                p.then(function(address) {
                    foodInfo.append("<div class='card' id='" + element['id'] + "'>" +
                    "<div class='row'>" +
                        "<div class='col-md-8 col-sm-8 col-xs-7'>" +
                            "<div class='card-block'>" +
                                "<h4 class='card-title'>" + element['name'] + "</h4>" +
                                "<p class='card-text card-time' style='font-style=italic '> Posted " +moment(element["time"]).fromNow() + "</p>" +
                                "<p>" + ((moment(element["expiry"]).isAfter(currentDate)) ? "Expires ": "Expired ")+moment(element["expiry"]).fromNow()+"</p>" +
                                "<div class='btn-group buttons'>" +
                                "<a href='item.php?item="+element['id'] + "' class='btn btn-custom'>More</a>" +
                        "</div></div></div>");
                    if(memberSearch) {
                        var claimerButton = document.createElement('button');
                        claimerButton.textContent = "Claim";
                        claimerButton.className = "btn btn-custom";
                        claimerButton.addEventListener('click', function() {
                            document.location = "claim.php?item="+element['id'];
                        }, false);
                        $('#' + element['id'] + ' .buttons').append(
                            claimerButton
                        );
                        $('#' + element['id'] + ' .card-time').text("Posted by " + element['user_username'] + " "
                            + moment(element["time"]).fromNow());
                    }
                    $('#' + element['id'] + ' .row').append("<div class='col-md-4 col-sm-4 col-xs-5 search-image-wrap'>" +
                        "<img class='center' src='"+ element['image_url'] + "'>" +
                        "</div>"
                    );

                    //Create markers and info windows
                    var myLatlng = new google.maps.LatLng(element['latitude'], element['longitude']);
                    var infowindow = new google.maps.InfoWindow({
                        content: popupDetails(element, address)
                    });

                    var marker = new google.maps.Marker({
                        position: myLatlng,
                        map: mymap,
                        title: element['name'],
                        icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
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
            if(!$('.page-item')[0]) {
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
            $('#map-button').css("visibility", "hidden");
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
    return new Promise(function (resolve, reject) {
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
    var params = {
        "region": "uk",
        "address": position
    };
    $.get( "https://maps.googleapis.com/maps/api/geocode/json", params).done(function(data) {
        if(data['status'] === "ZERO_RESULTS") {
            callback("")
        }
        else {
            data = data["results"][0]["geometry"]["location"];
            callback(data["lat"] + "," + data["lng"]);
        }
    })

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
        minLength: 3
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
        scrollwheel: false
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
        '<p>Posted ' +moment(food["time"]).fromNow() + '</p>' +
        '<p>Expires '+moment(food["expiry"]).fromNow()+ '</p>' +
        '<p>Address: ' + address + '</p>' +
        '<button class="btn btn-custom btn-sm" onClick=loadFullFood('+food["id"]+',this)>More</button></div>';
}

/**Convert date time to nice formatting
 *
 * @param dateString Datetime string
 * @returns {string}
 */
function timePosted(dateString) {
    var timePosted = new Date(dateString);
    var timePostedString;
    //today
    if(timePosted.getDate() === today.getDate()) {
        timePostedString = "Today";
    }
    //yesterday
    else if(timePosted.getDate() === today.getDate(today.setDate(today.getDate()-1))) {
        timePostedString = "Yesterday";
    }
    else {
        timePostedString = timePosted.getDate() + " " + monthNames[timePosted.getMonth()];
    }
    timePostedString += " at " + timePosted.getHours() + ":";
    if(parseInt(timePosted.getMinutes()) < 10 ) {
        timePostedString += 0;
    }
    timePostedString += timePosted.getMinutes();
    today = new Date();
    return timePostedString
}

function clearMarkers() {
    for (var i=0; i<markers.length; i++) {
        markers[i].setMap(null);
    }
    markers = [];
}

//click event for showing and hiding the map
$(document.body).on('click', '#map-button', function () {
    if($("#map").is(':visible')) {
        $('#map').hide('blind',{direction:'up'}, 1000, function() {
            $('#map-button').toggleClass('active')
                .css('border-radius', '5px')
                .text('Open map')
        });
    }
    else {
        $('#map').show('blind', {direction: 'up'}, 1000);
        $('#map-button').toggleClass('active')
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
    };
    var myCircle = new google.maps.Circle(circleOptions);
    mymap.fitBounds(myCircle.getBounds());
    zoomChangeBoundsListener =
        google.maps.event.addListenerOnce(mymap, 'bounds_changed', function(event) {
            var currentZoomLevel = mymap.getZoom();
            mymap.setZoom(currentZoomLevel+1);
        });

}
/**Scroll to the specified food
 *
 * @param id ID of food
 * @param button Button linked to specified food
 */
function loadFullFood(id) {
    $('html, body').animate({
            scrollTop: $("#" + id).offset().top
        }, 1000);
}

var page = $("html, body");

//stop animation when user scrolls

$( '.page-content' ).click(function(e) {
    page.on("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove", function(){
        page.stop();
    });
    return false;
});

function getUser() {
    return new Promise(function(resolve,reject) {
        $.getJSON('api/profile/private.php')
            .done(function(data) {
                if (data.hasOwnProperty("error")) {
                    reject(data);
                }
                else {
                    resolve(data);
                }
            })
            .fail(function(data) {
                reject(data);
            });
    })
}


//history stuff

var pushed = false;

//save the state containing the ajax content
function saveState(q, location, distance, expiry, time, sort, resultsPerPage, offset, address) {
    var url = "#q=" + q.replace(/\s+/g, '+') + "&location=" +  location + "&distance="
        + distance + "&expiry=" + expiry.replace(/\s+/g, '+') + "&time=" + time.replace(/\s+/g, '+') + "&sort="
        + sort.replace(/\s+/g, '+') + "&num=" + resultsPerPage + "&offset=" + offset;
    var parameters = {
        q: q,
        location: location,
        distance: distance,
        expiry: expiry,
        time: time,
        sort: sort,
        num: resultsPerPage,
        offset: offset,
        address: address
    };
    history.pushState(parameters, '', url);


}


$(window).bind('popstate', function(event) {

    var parameters = event.originalEvent.state;
    //if state exists then reload page with search
    if(parameters === "advancedSearch") {
        $('.content').html(addSideSearchbar(6));
        $('.advancedSearchbar').addClass('col-centered');
        configureBootstrap();
    }
    else if(parameters !== null) {
        addContainers();
        configureBootstrap();
        pageNumber = (parameters['offset']/parameters['num']);
        updateSideBar(parameters['q'],parameters['address'] , parameters['distance'], parameters['expiry'], parameters['time'],
            parameters['sort'], parameters['num']);
        search(parameters['q'], parameters['location'], parameters['distance'], parameters['expiry'], parameters['time'],
            parameters['sort'], parameters['num'], parameters['offset'], pageNumber)

    }
    else {
        location.reload();
    }

});


history.replaceState(null, '', window.location.href);

function getAllUrlParams(url) {

    // get query string from url (optional) or window
    var queryString = url ? url.split('?')[1].split('+').join(' ') : window.location.hash.slice(1).split('+').join(' ');

    // we'll store the parameters here
    var obj = {};

    // if query string exists
    if (queryString) {

        // stuff after # is not part of query string, so get rid of it
        queryString = queryString.split('#')[0];

        // split our query string into its component parts
        var arr = queryString.split('&');

        for (var i=0; i<arr.length; i++) {
            // separate the keys and the values
            var a = arr[i].split('=');

            // in case params look like: list[]=thing1&list[]=thing2
            var paramNum = undefined;
            var paramName = a[0].replace(/\[\d*\]/, function(v) {
                paramNum = v.slice(1,-1);
                return '';
            });

            // set parameter value (use 'true' if empty)
            var paramValue = typeof(a[1])==='undefined' ? true : a[1];

            // (optional) keep case consistent
            paramName = paramName.toLowerCase();
            // if parameter name already exists
            if (obj[paramName]) {
                // convert value to array (if still string)
                if (typeof obj[paramName] === 'string') {
                    obj[paramName] = [obj[paramName]];
                }
                // if no array index number specified...
                if (typeof paramNum === 'undefined') {
                    // put the value on the end of the array
                    obj[paramName].push(paramValue);
                }
                // if array index number specified...
                else {
                    // put the value at that index number
                    obj[paramName][paramNum] = paramValue;
                }
            }
            // if param name doesn't exist yet, set it
            else {
                obj[paramName] = paramValue;
            }
        }
    }
    return obj;
}
