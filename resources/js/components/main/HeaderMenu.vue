<template>
    <div>
        <!-- Offcanvas Menu Section Begin -->
        <div class="offcanvas-menu-overlay"></div>
        <div class="offcanvas-menu-wrapper">
            <div class="canvas-close">
                <i class="fa fa-close"></i>
            </div>

            <div id="mobile-menu-wrap" @click="setActiveMobileMenuItem">
                <nav class="canvas-menu mobile-menu">
                    <ul>
                        <li>
                            <a id="home-mobile-menu-item" :class="{'color-wave':activeMenuItemUrlPath === '/'}" href="/">Home</a>
                        </li>
                        <li>
                            <a id="classes-mobile-menu-item" :class="{'color-wave':activeMenuItemUrlPath === '/#classes'}" href="/#classes">Classes</a>
                        </li>
                        <li>
                            <a id="pricing-plans-mobile-menu-item" :class="{'color-wave':activeMenuItemUrlPath === '/#pricing-plans'}" href="/#pricing-plans">Pricing</a>
                        </li>
                        <li>
                            <a id="weekly-program-mobile-menu-item" :class="{'color-wave':activeMenuItemUrlPath === '/#weekly-program'}" href="/#weekly-program">Weekly Program</a>
                        </li>
                        <li>
                            <a id="contact-mobile-menu-item" :class="{'color-wave':activeMenuItemUrlPath === '/#contact'}" href="/#contact">Contact</a>
                        </li>
                        <li>
                            <a id="student-calendar-mobile-menu-item" :class="{'color-wave':activeMenuItemUrlPath === '/student-calendar'}" href="/student-calendar">Bookings</a>
                        </li>

                        <hr class="text-white">

                        <li v-if="!auth.user">
                            <a id="sign-in-mobile-menu-item" :class="{'color-wave':activeMenuItemUrlPath === '/sign-in'}" href="/sign-in">Sign in</a>
                        </li>
                        <li v-if="!auth.user">
                            <a id="sign-up-mobile-menu-item" :class="{'color-wave':activeMenuItemUrlPath === '/sign-up'}" href="/sign-up">Sign up</a>
                        </li>
                        <li v-if="!auth.user">
                            <a id="forgot-password-mobile-menu-item" :class="{'color-wave':activeMenuItemUrlPath === '/forgot-password'}" href="/forgot-password">Forgot password</a>
                        </li>
                        <li v-if="auth.user">
                            <a id="profile-mobile-menu-item" :class="{'color-wave':activeMenuItemUrlPath === '/profile'}" href="/profile">Profile</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <span v-if="auth.user" @click="logout" class="sign-out-mobile-menu-item">
                    Sign out
                </span>

            <hr class="text-white">

            <div class="canvas-social">
<!--                <a href="#"><i class="fa fa-facebook"></i></a>-->
<!--                <a href="#"><i class="fa fa-twitter"></i></a>-->
<!--                <a href="#"><i class="fa fa-youtube-play"></i></a>-->
                <a href="https://www.instagram.com/wave_fitness_project/" target="_blank"><i class="fa fa-instagram"></i></a>
            </div>
        </div>
        <!-- Offcanvas Menu Section End -->

        <!-- Header Section Begin -->
        <header class="header-section">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-3 col-xl-2">
                        <div class="logo">
                            <a href="/">
                                <img src="images/wave_transparent_no_buffer.png" alt="">
                            </a>
                        </div>
                        <b class="logo-text">
                            <span class="color-wave">WAVE</span>
                            <br>
                            <span class="text-white">FITNESS PROJECT</span>
                        </b>
                    </div>

                    <div class="col-lg-6 col-xl-8">
                        <nav class="nav-menu">
                            <ul>
                                <li>
                                    <a id="home-menu-item" @click="setActiveMenuItem"
                                       :class="{'color-wave':activeMenuItemUrlPath === '/'}" href="/">Home</a>
                                </li>
                                <li>
                                    <a id="classes-menu-item" @click="setActiveMenuItem"
                                       :class="{'color-wave':activeMenuItemUrlPath === '/#classes'}" href="/#classes">Classes</a>
                                </li>
                                <li>
                                    <a id="pricing-plans-menu-item" @click="setActiveMenuItem"
                                       :class="{'color-wave':activeMenuItemUrlPath === '/#pricing-plans'}" href="/#pricing-plans">Pricing</a>
                                </li>
                                <li>
                                    <a id="weekly-program-menu-item" @click="setActiveMenuItem"
                                       :class="{'color-wave':activeMenuItemUrlPath === '/#weekly-program'}" href="/#weekly-program">Weekly Program</a>
                                </li>
                                <li>
                                    <a id="contact-menu-item" @click="setActiveMenuItem"
                                       :class="{'color-wave':activeMenuItemUrlPath === '/#contact'}" href="/#contact">Contact</a>
                                </li>
                                <li>
                                    <a id="student-calendar-menu-item" @click="setActiveMenuItem"
                                       :class="{'color-wave':activeMenuItemUrlPath === '/student-calendar'}" href="/student-calendar">Bookings</a>
                                </li>
                            </ul>
                        </nav>
                    </div>

                    <div class="col-lg-3 col-xl-2">
                        <div class="top-option">
                            <div class="dropdown-login">
                                <drop-down-login></drop-down-login>
                            </div>
                            <div class="to-social">
                                <span class="px-1"> | </span>
<!--                                <a href="#"><i class="fa fa-facebook"></i></a>-->
<!--                                <a href="#"><i class="fa fa-twitter"></i></a>-->
<!--                                <a href="#"><i class="fa fa-youtube-play"></i></a>-->
                                <a href="https://www.instagram.com/wave_fitness_project/" target="_blank"><i class="fa fa-instagram"></i></a>
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
        data() {
            return {
                user: this.auth.user,
                activeMenuItemUrlPath: null,
            };
        },
        mounted() {
            this.activeMenuItemUrlPath = this.$router.currentRoute.fullPath;
        },
        methods: {
            logout() {
                if (this.auth.isAuthorized()) {
                    axios.post('/users/logout')
                        .then(({data}) => {
                            this.auth.logout();
                            this.user = this.auth.user;
                            window.location.replace("/");
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                }
            },
            setActiveMenuItem(event) {
                if(event.target.tagName.toLowerCase() === 'a') {
                    var baseUrl = window.location.origin;
                    this.activeMenuItemUrlPath = event.target.href.split(baseUrl).pop();
                }
            },
            setActiveMobileMenuItem(event) {
                if(event.target.tagName.toLowerCase() === 'a') {
                    $('.slicknav_nav  > ul > li > a').removeClass('color-wave');
                    $(event.target).addClass('color-wave');

                    var baseUrl = window.location.origin;
                    this.activeMenuItemUrlPath = event.target.href.split(baseUrl).pop();
                }
            },
        }
    }
</script>
