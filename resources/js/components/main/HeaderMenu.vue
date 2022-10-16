<template>
    <div>
        <!-- Page Preloder -->
        <div id="preloder">
            <div class="loader"></div>
        </div>

        <!-- Offcanvas Menu Section Begin -->
        <div class="offcanvas-menu-overlay"></div>
        <div class="offcanvas-menu-wrapper">
            <div class="canvas-close">
                <i class="fa fa-close"></i>
            </div>
            <div class="canvas-search search-switch">
                <i class="fa fa-search"></i>
            </div>
            <nav class="canvas-menu mobile-menu">
                <ul>
                    <li><a href="./index.html">Home</a></li>
                    <li><a href="./about-us.html">About Us</a></li>
                    <li><a href="./classes.html">Classes</a></li>
                    <li><a href="./services.html">Services</a></li>
                    <li><a href="./team.html">Our Team</a></li>
                    <li><a href="#">Pages</a>
                        <ul class="dropdown">
                            <li><a href="./about-us.html">About us</a></li>
                            <li><a href="./class-timetable.html">Classes timetable</a></li>
                            <li><a href="./bmi-calculator.html">Bmi calculate</a></li>
                            <li><a href="./team.html">Our team</a></li>
                            <li><a href="./gallery.html">Gallery</a></li>
                            <li><a href="./blog.html">Our blog</a></li>
                            <li><a href="./404.html">404</a></li>
                        </ul>
                    </li>
                    <li><a href="./contact.html">Contact</a></li>
                </ul>
            </nav>
            <div id="mobile-menu-wrap"></div>
            <div class="canvas-social">
                <a href="#"><i class="fa fa-facebook"></i></a>
                <a href="#"><i class="fa fa-twitter"></i></a>
                <a href="#"><i class="fa fa-youtube-play"></i></a>
                <a href="#"><i class="fa fa-instagram"></i></a>
            </div>
        </div>
        <!-- Offcanvas Menu Section End -->

        <!-- Header Section Begin -->
        <header class="header-section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="logo">
                            <a href="./index.html">
                                <img src="images/wave_transparent_no_buffer.png" alt="">
                                <!--                                <img src="images/wave_transparent_no_buffer.png" alt="">-->
                                <!--                                <img :src="'/images/logo.png'" alt="">-->
                            </a>
                        </div>
                        <b class="logo-text">
                            <span class="color-wave">WAVE</span>
                            <br>
                            <span class="text-white">FITNESS PROJECT</span>
                        </b>
                    </div>
                    <div class="col-lg-6">
                        <nav class="nav-menu">
                            <ul>
                                <li class="active"><a href="./index.html">Home</a></li>
                                <li><a href="./about-us.html">About Us</a></li>
                                <li><a href="./class-details.html">Classes</a></li>
                                <li><a href="./services.html">Services</a></li>
                                <li><a href="./team.html">Our Team</a></li>
                                <li><a href="#">Pages</a>
                                    <ul class="dropdown">
                                        <li><a href="./about-us.html">About us</a></li>
                                        <li><a href="./class-timetable.html">Classes timetable</a></li>
                                        <li><a href="./bmi-calculator.html">Bmi calculate</a></li>
                                        <li><a href="./team.html">Our team</a></li>
                                        <li><a href="./gallery.html">Gallery</a></li>
                                        <li><a href="./blog.html">Our blog</a></li>
                                        <li><a href="./404.html">404</a></li>
                                    </ul>
                                </li>
                                <li><a href="./contact.html">Contact</a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="col-lg-3">
                        <div class="top-option">
                            <div class="dropdown-login">
                                <drop-down-login></drop-down-login>
                            </div>
                            <div class="to-social">
                                <span class="px-1"> | </span>
                                <a href="#"><i class="fa fa-facebook"></i></a>
                                <a href="#"><i class="fa fa-twitter"></i></a>
                                <a href="#"><i class="fa fa-youtube-play"></i></a>
                                <a href="#"><i class="fa fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="canvas-open">
                    <i class="fa fa-bars"></i>
                </div>
            </div>

            <div class="header-section-background"></div>
            <div class="header-section-gradient"></div>

        </header>
        <!-- Header End -->
    </div>
</template>

<script>
    export default {

    }
</script>
