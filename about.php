<?php 
/**
 * About page explaining the mission of Flavourtown
 */
?>
<?php
    define('__ROOT__',dirname(__FILE__));
    require __ROOT__.'/class/Page.class.php';
    $p = new Page("About us");
    $p->buildHead();
    $p->buildHeader();
?>
    <div>
        <div class="aboutus-carousel">
            <ul>
                <li>
                    <img src="http://fakeimg.pl/2000x800/0079D8/fff/?text=Flavourtown">
                </li>
                <li>
                    <img src="http://fakeimg.pl/2000x800/DA5930/fff/?text=We Bring">
                </li>
                <li>
                    <img src="http://fakeimg.pl/2000x800/F90/fff/?text=The Flavour">
                </li>
            </ul>
        </div>
    </div>
    <h1>Who are we?</h1>
    <p><strong>We are Flavourtown,</strong> and as the name and the carousel above suggests, we bring the flavour. We are a bunch of students from Durham University and this is part of our Group project. A computer randomly put our names together, so here we are trying to score some decent marks and making the world a better place while we're at it. <a href="help.php">Check out our FAQs</a> for additional help.</p>
    <h2>Why are we doing this?</h2>
    <p>Mainly because we were told to and had no say on the matter. But down to some serious stuff. Every year the UK ends up wasting billions of pounds worth of food, which could have otherwise been put to much better use. Our aim is to help reduce this wastage as much as we possibly can. We are planning on achieving this by making this platform, with which people can give away their unwanted food to those who need it instead of throwing it away.</p>
    <ol>
    <li>UK households wasted 7.3 million tonnes of food in 2015.</li>
    <li>The UK is currently throwing away £13bn of food each year.</li>
    </ol>
    <blockquote><p>The UK is currently throwing away £13bn of food each year.</p></blockquote>
    <h3>How can you help?</h3>
    <p>You can help both, us and the environment easily by using our cutting-edge platform. All you have to do is follow these steps:</p>
    <ul>
    <li>Register to out service.</li>
    <li>Add your unwanted food items and wait for someone to message you to claim it.</li>
    <li>If you find any items you fancy, you can claim it for yourself and message the owner using our service to arrange a pickup.</li>
    </ul>
    <script>
        $(document).ready(function() {
            $('.aboutus-carousel').unslider({
                'autoplay':true,
                'nav':false
            }) 
        });
    </script>
<?php
    $p->buildFooter();
?>