<?php
        session_start();
        require "functions.php";
        include "header.php";
        include "nav.php";
    ?>
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
<?php 
include "footer.php";
?>
