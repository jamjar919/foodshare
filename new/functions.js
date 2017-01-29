var mymap;
// Get the current lat/long with navigator object
var initialPosition = null;
if (navigator.geolocation){
    navigator.geolocation.getCurrentPosition(
        function(position){
            initialPosition = [position.coords.latitude,position.coords.longitude];
            console.log("Got initial position as: "+initialPosition);
        },
        function(error){
            // If we don't find the initial position just go L O N D O N
            initialPosition = [51.5, -0.09];
            console.log("Error getting pos: "+error);
        }
    );
}
// Abstractor for the initial position
function getInitialPosition() {
    if (initialPosition == null) {
        return [51.5, -0.09]; // L O N D O N
    }
    return initialPosition;
}

function scrollToSearch() {
    startLoad();
    $("#top-tab").hide();
    $( "#front-page" ).slideDown( 1000, function() {
        stopLoad();
        $("#map").hide();
    });
}

function scrollToMap() {
    startLoad();
    $( "#front-page" ).slideUp( 1000, function() {
        $("#top-tab").slideDown(1000);
        $("#map").show();
        mymap.invalidateSize();
        stopLoad();
    });
}

function initMap() {
    mymap = L.map('map').setView(getInitialPosition(), 13);
    L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v10/tiles/256/{z}/{x}/{y}?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
        maxZoom: 18,
        accessToken: 'pk.eyJ1IjoiamFtamFyOTE5IiwiYSI6ImNpdXFzZ2hjOTAwMGoyb3Bzb2FhbGI2aWQifQ.-piiQpUIG34TEBw64YN_gA'
    }).addTo(mymap);
}

function setMap(pos = null, zoom = 14) {
    if (pos == null) {
        pos = getInitialPosition();        
    }
    console.log("Scrolling to: "+pos);
    mymap.setView(pos, zoom);
}

function geocode(position, callback) {
    //https://maps.googleapis.com/maps/api/geocode/json?region=uk&address=Durham
    var params = {
        "region": "uk",
        "address": position
    }
    $.get( "https://maps.googleapis.com/maps/api/geocode/json", params).done(function(data) {
        data = data["results"][0]["geometry"]["location"];
        callback([data["lat"],data["lng"]]);
    });
}

function putFoodOnMap(positionString, callback) {
    geocode(positionString, function(pos) {
        console.log(pos);
        callback(pos);
    })
    
}

LOADID = "loader";
function startLoad() {
    if (! $("#"+LOADID).length) {
        $("body").append("<div id=\""+LOADID+"\" class=\"loadingOverlay\" style=\"pointer-events:none;\"></div>")
    }
    $("#"+LOADID).show();
}
function stopLoad() {
    $("#"+LOADID).hide();
}

$(document).ready(function() {
    initMap();
});

/*
 * Bind events
 */

$("#searchbutton").click(function() {
    putFoodOnMap($("#searchbox").val(), function(pos) {
        setMap(pos);
        scrollToMap();
    });
});

$( "#bottom-tab" ).click(function() {
    scrollToMap();
    setMap();
})

$( "#top-tab" ).click(function() {
    scrollToSearch();
})