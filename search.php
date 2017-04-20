<?php 
/**
 * Old search page.
 *
 */
?>
<?php
define('__ROOT__',dirname(__FILE__));
require_once __ROOT__."/class/User.class.php";
require_once __ROOT__."/class/UserTools.class.php";
$errors = array();

// Check we aren't already logged in via cookie
if (isset($_COOKIE["username"]) && isset($_COOKIE["token"])) {
    $user = new User($_COOKIE["username"],$_COOKIE["token"]);
    if ($user->isLoggedIn()) {
        header("Location: membersSearch.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Test search page</title>

    <!-- Include jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

    <!-- Include Bootstrap 3 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >

    <!-- Include Bootstrap 4 cards -->
    <link rel="stylesheet" href="css/cards.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.2/css/bootstrap-slider.css">

    <!-- Include moment and Bootstrap slider -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.2/bootstrap-slider.js"></script>

    <!-- Include Date Range Picker -->
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

    <!-- Include jQuery ui for autocomplete -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0-rc.2/jquery-ui.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0-rc.2/jquery-ui.min.css" rel="stylesheet" type="text/css"/>

    <!-- Include Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyApspzkH9nU8Imd1KffUjhlEo0iMg9D9Sg"></script>

    <script src="js/cookie.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css?family=Lobster|Raleway');

        body {
            font-family: 'Raleway', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
            padding-top: 50px;
        }

        h1,h2,h3,h4,h5,h6 {
            font-family: 'Lobster', 'Garamond',serif;
        }

        .col-centered{
            float: none;
            margin: 0 auto;
        }
        #map-button {
            margin-top: 20px;
            text-align: center;
            visibility: hidden;
            border-radius: 5px 5px 0 0;
        }
        #map {
            height: 80vh;
            display: block;

        }
        #results {
            margin-top: 20px;
        }

        #radius .slider-selection {
            background: #BABABA;
        }
        .dropdown.dropdown-lg .dropdown-menu {
            margin-top: -1px;
            padding: 6px 20px;
        }
        .input-group-btn .btn-group {
            display: flex !important;
        }
        .btn-group .btn {
            border-radius: 0;
            margin-left: -1px;
        }
        .btn-group .btn:last-child {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }
        .btn-group .form-horizontal .btn[type="submit"] {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }
        .form-horizontal .form-group {
            margin-left: 0;
            margin-right: 0;
        }
        .form-group .form-control:last-child {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        @media screen and (min-width: 768px) {
            #adv-search {
                width: 500px;
                margin: 0 auto;
            }
            .dropdown.dropdown-lg {
                position: static !important;
            }
            .dropdown.dropdown-lg .dropdown-menu {
                min-width: 500px;
            }
        }

        .card {
            margin-bottom: 10px;
        }

        .card-title {
            font-weight: bold;
        }
        .card .row {
            width: 100%;
            margin: 0;
        }
        .food-display-panel {
            margin-top: 3em;
            margin-bottom: 3em;
        }

        .card .row {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display:         flex;
            flex-wrap: wrap;
        }
        .card .row > [class*='col-'] {
            display: flex;
            flex-direction: column;
        }

        .card .row img {
            height: 25vh;
        }

        .center {
            display: inline-block;
            margin: auto;
            vertical-align: middle;
        }

        .card-block {
            border-right: 1px solid rgba(0,0,0,.125);
        }

        .btn-custom {
            background-color: #212a3f;
            border-color: #212a3f;
            color: white;
        }
        .btn-custom:hover,
        .btn-custom:focus,
        .btn-custom:active,
        .btn-custom.active {
            background-color: #181f2e;
            border-color: #0f141e;
            color: white;
        }
        .btn-custom.disabled:hover,
        .btn-custom.disabled:focus,
        .btn-custom.disabled:active,
        .btn-custom.disabled.active,
        .btn-custom[disabled]:hover,
        .btn-custom[disabled]:focus,
        .btn-custom[disabled]:active,
        .btn-custom[disabled].active,
        fieldset[disabled] .btn-custom:hover,
        fieldset[disabled] .btn-custom:focus,
        fieldset[disabled] .btn-custom:active,
        fieldset[disabled] .btn-custom.active {
            background-color: #212a3f;
            border-color: #212a3f;
            color: white;
        }

        .pagination a {
            color: #212a3f;
        }
        .pagination a.active {
            background-color: #212a3f;
            color: white;
        }


    </style>

</head>
<body>
<div class="container">
    <h2 style="text-align: center">FlavourTown</h2>
        <div class="row">
            <div class="col-md-12 col-centered">
                <div class="input-group" id="adv-search">
                    <input class="form-control" type="text" placeholder="Search food" id="q1">
                    <div class="input-group-btn">
                        <div class="btn-group" role="group">
                            <div class="dropdown dropdown-lg">
                                <button id="dlDropDown" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Advanced <span class="caret"></span></button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu">
                                    <form class="form-horizontal">
                                        <div class="form-group">
                                            <label for="q2">Key words</label>
                                            <input class="form-control" type="text" id="q2"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="sort">Sort by</label>
                                            <select class="form-control" id="sort">
                                                <option>Alphabetical</option>
                                                <option>Best match</option>
                                                <option>Most recent</option>
                                                <option>Expiry</option>
                                                <option>Closest</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="loc">Location</label>
                                            <input class="form-control" type="text" id="loc"/>
                                        </div>
                                        <div class="form-group row">
                                            <label for="radius" class="col-2 col-form-label">Radius</label>
                                            <div class="col-4">
                                                <b>0 km</b> <input id="radius" data-slider-id="radiusSlider" type="text" data-slider-min="0" data-slider-max="30" data-slider-step="1" data-slider-value="15"/>
                                                <b>30 km</b>
                                                <span id="radiusCurrentSliderValLabel">Radius: <span id="radiusSliderVal">15</span> km</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="expiry"  class="col-2 col-form-label">Expiry date</label>
                                            <div class="col-4">
                                                <input class="form-control" id="expiry" name="daterange" type = "text" value="Any time" style="width: 100%">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="time"  class="col-2 col-form-label">Time posted</label>
                                            <div class="col-4">
                                                <input class="form-control" id="time" name="datetimerange" type = "text" value="Any time" style="width: 100%">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="resultsPerPage">Results per page</label>
                                            <input class="form-control" type="text" value="15" id="resultsPerPage">
                                        </div>
                                        <button id="searchAdvanced" class="btn btn-custom rajax">Search <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                                    </form>
                                </div>
                            </div>
                            <button id="search" class="btn btn-custom rajax"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div class="row page-content">
        <div class="col-md-8 col-centered ">
            <button id="map-button" class="btn btn-custom btn-block active">Close map</button>
            <div id="map-container">

            </div>
            <div id="results">
            </div>
            <div class="text-center">
                <ul class="pagination" ></ul>
            </div>
        </div>
    </div>
</body>
<script>
    var memberSearch = false;
</script>
<script src="js/search.js" type="application/javascript"></script>

</html>