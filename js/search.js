var q, storedLocation, radius, expiry, time, sort, resultsPerPage, pageNumber, isNext, isPrev, totalResults;
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
$('#search').click(function() {
    q = $('#q1').val();
    storedLocation = [54.7, -1.56];
    expiry = "Any time";
    time = "Any time";
    radius = 25;
    sort = "Best match";
    resultsPerPage = 4;
    pageNumber = 0;

    search(q, storedLocation, radius, expiry, time, sort, resultsPerPage, 0, true);
    //remove pagination
    $('.pagination').html("");
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

function setPageLinks(page) {
    $(".pagination li").removeClass("active");
    $("#link1 a").text((page).toString());
    $("#link2 a").text((page+1).toString());
    $("#link3 a").text((page+2).toString());

    $("#link2").addClass("active");
}

//dynamically add pagination links
function addLinks() {
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

function search(q, location, distance, expiry, time, sort, results, page, firstSearch) {
    //bind id to json topic
    clearMarkers();
    var parameters = { q:q,  location: location, distance: distance, expiry: expiry, time: time, sort: sort, num: results, offset: page};
    $('#results').html('<img src="https://upload.wikimedia.org/wikipedia/commons/b/b1/Loading_icon.gif" ' +
        'style="display: block; margin: 0 auto; width: 200px; height: auto;"/>');
    $.getJSON("api/food.php", parameters, function(data) {
        var foodInfo = $('<div></div>').addClass('food');

        if(data.food.length > 0) {
            initMap(location);
            $.each(data.food, function (key, element) {
                var address = convertGeocode(element['latitude'], element['longitude']);

                var p = Promise.resolve(address);
                p.then(function(address) {
                    foodInfo.append("<p>Name: " + element['name'] + "</p>" +
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

function initMap(pos) {
    var myLatlng = new google.maps.LatLng(pos[0], pos[1]);
    mymap = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: myLatlng
    });
}

function popupDetails(food, address) {
    return '<div class="food-popup"><h3>'+food["name"]+'</h3>' +
        '<p>'+food["description"]+'</p>' +
        "<p>Expiry date: " + food['expiry'] + "</p>" +
        "<p>Submission date: " + food['time'] + "</p>" +
        "<p>Address: " + address + "</p>" +
        '<button class="btn btn-primary btn-sm" onClick="function(){loadFullFood('+food["id"]+')}">More</button></div>';
}

google.maps.event.addListener(map, 'click', function(event) {
    mapZoom = map.getZoom();
    var pos = mymap.getCenter();
    loadMapFood(q, pos, radius, expiry, time, sort, resultsPerPage, 0, function() {
        console.log("Loaded food at "+e.latlng);
    });

});

function loadMapFood(q, location, distance, expiry, time, sort, results, page) {
    clearMarkers();
    var parameters = { q:q,  location: location, distance: distance, expiry: expiry, time: time, sort: sort, num: results, offset: page};
    $.getJSON("api/food.php", parameters, function(data) {
        if(data.food.length > 0) {
            mymap.on('click', mapOnClick);
            $.each(data.food, function (key, element) {
                var address = convertGeocode(element['latitude'], element['longitude']);

                var p = Promise.resolve(address);
                p.then(function(address) {
                    var infowindow = new google.maps.InfoWindow({
                        content: popupDetails(element, address)
                    });

                    var marker = new google.maps.Marker({
                        position: {lat: element['latitude'], lng: element['longitude']},
                        map: mymap,
                        title: element['name']
                    });

                    marker.addListener('click', function() {
                        infowindow.open(mymap, marker);
                    });
                    markers.push(marker);
                });
            });
        }
    });
}
function clearMarkers() {
    for (var i=0; i<markers.length; i++) {
        markers[i].setMap(null);
    }
    markers = [];
}