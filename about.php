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
    <h1>HTML Ipsum Presents</h1>
    <p><strong>Pellentesque habitant morbi tristique</strong> senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. <em>Aenean ultricies mi vitae est.</em> Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra. Vestibulum erat wisi, condimentum sed, <code>commodo vitae</code>, ornare sit amet, wisi. Aenean fermentum, elit eget tincidunt condimentum, eros ipsum rutrum orci, sagittis tempus lacus enim ac dui. <a href="#">Donec non enim</a> in turpis pulvinar facilisis. Ut felis.</p>
    <h2>Header Level 2</h2>
    <ol>
    <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
    <li>Aliquam tincidunt mauris eu risus.</li>
    </ol>
    <blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote>
    <h3>Header Level 3</h3>
    <ul>
    <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
    <li>Aliquam tincidunt mauris eu risus.</li>
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