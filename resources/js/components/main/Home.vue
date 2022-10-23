<template>
    <div class="main-home-page">

<!--        <h1>Home</h1>-->



<!--        <div>-->
<!--            <drop-down-login class="float-right"></drop-down-login>-->

<!--            <div class="float-right">-->
<!--                <router-link to="/student-calendar">-->
<!--                    <b-dropdown-item-button>Calendar</b-dropdown-item-button>-->
<!--                </router-link>-->
<!--            </div>-->

<!--        </div>-->

        <!-- Page Preloder -->
        <div id="preloder">
            <div class="loader"></div>
        </div>

        <!-- Header Begin -->
        <header-menu></header-menu>
        <!-- Header End -->

        <div class="home-content">
            <router-view class="view"></router-view>
        </div>

        <!-- Footer Section Begin -->
        <footer-section></footer-section>
        <!-- Footer Section End -->
    </div>
</template>

<script>
    export default {

    }
</script>
