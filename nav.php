    <nav>
        <ul>
            <li><a href="about.php">About</a></li>
            <?php
            if (!isLoggedIn($_SESSION)) {
            ?>
            <li><a href="#" data-toggle="modal" data-target="#modal-1">Log In</a></li>
            <div class="modal fade" tabindex="-1" role="dialog" id="modal-1">
                <div class="vertical-alignment-helper">
                    <div class = "modal-dialog vertical-alignment-center">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h3 class="modal-title text-center">Login</h3>
                            </div>
                            <div class="modal-body">
                                <form class="login-form" action="login.php" method="POST">
                                    <div class="form-group row">
                                        <!-- <label for="fname" class="col-xs-2 col-form-label">First Name</label> -->
                                        <div class="col-xs-12 col-sm-8 col-centered">
                                            <input class="form-control" type="text" name="username" placeholder="Username">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <!-- <label for="password" class="col-xs-2 col-form-label">Password</label> -->
                                        <div class="col-xs-12 col-sm-8 col-centered">
                                            <input class="form-control" type="password" name="password" placeholder="Password">
                                        </div>
                                    </div>
                                    <div class="row loginform-bottom">
                                            <div class="checkbox loginform-bottom-element">
                                                <label><input type="checkbox"> Remember me</label>
                                            </div>
                                            <div class="loginform-bottom-element">
                                                &nbsp;<a href="#">Forgot Password</a>
                                            </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="text-center col-xs-12 col-sm-8 col-centered">
                                            <button style="width: 100%" type="submit" class="btn btn-custom " name="submit" value="Login">Log in</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <li><a href="register.php">Sign Up</a></li>
            <?php
            } else {
                // We are logged in, go to members page
                // header("Location: members.php");
                ?> 
                <li><a href="logout.php">Logout</a></li>
                <?php
            }
    ?>
        </ul>
    </nav>