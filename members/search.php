<?php
    require "../functions.php";
    include "members_header.php";
    include "members_nav.php";
?>

    <div class="container">
        <div class="row" >
            <div class="col-md-4 col-md-offset-1 col-sm-4 "">
                <h2 class="page-title">Advanced Search</h2>
            </div>
            <div class="col-md-6 col-xs-12 col-sm-8">
            </div>
        </div>
        <div class="row" >
            <div class="col-md-4 col-md-offset-1 hidden-xs col-sm-4 " style="vertical-align: top">
                <div class="panel panel-default">
                    <div class="panel-heading">Panel Heading</div>
                    <div class="panel-body">Panel content</div>
                </div>
            </div>
            <div class="col-md-6 col-xs-12 col-sm-8" style="vertical-align: top">
                <div class="panel panel-default">
                    <div class="panel-heading">Find Food</div>
                    <div class="panel-body">
                        <form>
                            <div class="form-group">
                                <label for="email">Keywords:</label>
                                <input type="email" class="form-control" id="email">
                            </div>
                            <div class="form-group">
                                <label for="pwd">Excluded words:</label>
                                <input type="password" class="form-control" id="pwd">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
    include "../footer.php"
?>
