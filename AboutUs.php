<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>

    <link rel="stylesheet" href="FoodShare.css" type="text/css" />
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>


    <style>
        html,body {
            height: 100%;
        }

        .carousel-caption{
            background: rgba(0,0,0,0.5);
            box-shadow: 0 0 5px 10px rgba(0,0,0,0.5);

        }

        .thumbnail {
            width: 70%;
        }

        #title {
            text-align: center;
        }

        .text {
            float: none;
            margin: 0 auto;
        }

        .col-centered{
            float: none;
            margin: 0 auto;
        }

        @media screen and (max-width: 767px) {
            .carousel-caption {
                font-size: 1em;
            }

        }


    </style>
</head>
<body>
    <header class="header">
        <h1><strong><a href="HTML/foodshare_guest.html">FoodShare</a></strong></h1>
    </header>


    <!-- Carousel -->
    <div class="container">
        <div class="thumbnail">
            <h2 id="title">About Us</h2>
            <div class="row">
                <div class="col-sm-12 col-xs-12">

                    <div id="theCarousel" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            <li data-target="#theCarousel" data-slide-to="0" class="active"></li>
                            <li data-target="#theCarousel" data-slide-to="1"></li>
                            <li data-target="#theCarousel" data-slide-to="2"></li>
                        </ol>

                        <div class="carousel-inner">
                            <div class="item active">
                                <img src="./images/Sharing_Food.jpg">
                                <div class="slide1"></div>
                                <div class="carousel-caption">
                                    <h2>Sharing food</h2>
                                    <p>FoodShare helps make sharing food with your local community easier than ever by allowing you to post rid of your unwanted food and get hold of others' unwanted food swiftly
                                    </p>
                                </div>
                            </div>

                            <div class="item">
                                <img src="./images/Environment.jpg">
                                <div class="slide2"></div>
                                <div class="carousel-caption">
                                    <h2>Saving the environment</h2>
                                    <p>We seek to help save the environment through encouraging food sharing in communities. Millions of tonnes of food is wasted every year and much of it goes into landfills which take up land.
                                    </p>
                                </div>
                            </div>

                            <div class="item">
                                <img src="./images/Food_Image1.jpg">
                                <div class="slide3"></div>
                                <div class="carousel-caption">
                                    <h2>Title</h2>
                                    <p>Bla bla bla bla bla bla bla bla</p>
                                </div>
                            </div>

                        </div>

                        <a class="left carousel-control" href="#theCarousel" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left"></span>
                        </a>

                        <a class="right carousel-control" href="#theCarousel" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right"></span>
                        </a>
                    </div>
                </div>
            </div>

                <div class="row">
                     <div class="col-xs-8 col-sm-8 text">
                         <br>
                         <br>
                         <p>
                             Ut sale voluptatum inciderint eos. Ex menandri incorrupte nam, pri no utinam disputando.
                             Integre equidem dissentiet ei has, at adipisci petentium mei. Ex sea populo erroribus elaboraret.
                             Eum latine periculis eu, ei sed modus paulo nemore, at eam feugiat civibus. Purto zril persius mei et, eruditi legendos ea eam.
                         </p>
                         <br>
                         <p>
                             Prima latine id ius, id quo mundi dicam percipitur. Meis offendit theophrastus eos ad, quo ea nemore virtute erroribus.
                             In semper ullamcorper mei. Vim ea denique accusata, no sea nibh ocurreret euripidis. Usu sonet vocent cu, eos bonorum salutatus pertinacia et.
                             Nec ea justo fierent, sapientem definitiones pri cu. Dolor viderer philosophia ei vis, has te nibh sensibus scripserit, wisi laoreet inermis duo no.
                         </p>
                     </div>
                </div>
            </div>
        </div>
    <footer>
        <nav class="downhere">
            <ul class="footer">
                <li><a href="#">About FoodShare</a></li>
                <li><a href="#">Announcements</a></li>
                <li><a href="#">Community</a></li>
                <li><a href="#">Help & Contact</a></li>
                <li><a href="#">Policies</a></li>
            </ul>
        </nav>
        <p>Property of <a href="https://www.dur.ac.uk/">
                Durham University</a></p>
    </footer>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>