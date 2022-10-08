<template>
    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <router-link to="/admin/show-subscription-plans" class="sidebar-brand">
                    <span class="align-middle color-wave">WAVE </span>Admin
                </router-link>

                <ul class="sidebar-nav">
                    <!--SUBSCRIPTION PLANS-->
                    <li class="sidebar-header">
                        Subscription Plans
                    </li>

                    <li @click="selected = 'subscription_plans'" :class="{active:selected === 'subscription_plans'}" class="sidebar-item">
                        <router-link to="/admin/show-subscription-plans" class="nav-link sidebar-link">
                            <font-awesome-icon icon="fa-solid fa-dollar-sign" size="lg"/>
                            <span class="align-middle">Subscription Plans</span>
                        </router-link>
                    </li>

                    <li @click="selected = 'create_subscription_plans'" :class="{active:selected === 'create_subscription_plans'}" class="sidebar-item">
                        <router-link to="/admin/create-subscription-plans" class="nav-link sidebar-link">
                            <font-awesome-icon icon="fa-solid fa-dollar-sign" size="lg"/>
                            <span class="align-middle">Create Subscription Plan</span>
                        </router-link>
                    </li>

                    <!--GYM CLASSES-->
                    <li class="sidebar-header">
                        Classes
                    </li>

                    <li @click="selected = 'gym_classes'" :class="{active:selected === 'gym_classes'}" class="sidebar-item">
                        <router-link to="/admin/show-gym-classes" class="nav-link sidebar-link">
                            <font-awesome-icon icon="fa-solid fa-house-user" size="lg"/>
                            <span class="align-middle">Classes</span>
                        </router-link>
                    </li>

                    <li @click="selected = 'create_gym_classes'" :class="{active:selected === 'create_gym_classes'}" class="sidebar-item">
                        <router-link to="/admin/create-gym-classes" class="nav-link sidebar-link">
                            <font-awesome-icon icon="fa-solid fa-house-user" size="lg"/>
                            <span class="align-middle">Create Class</span>
                        </router-link>
                    </li>

                    <!--SUBSCRIPTIONS-->
                    <li class="sidebar-header">
                        Subscriptions
                    </li>

                    <li @click="selected = 'subscriptions'" :class="{active:selected === 'subscriptions'}" class="sidebar-item">
                        <router-link to="/admin/show-subscriptions" class="nav-link sidebar-link">
                            <font-awesome-icon icon="fa-regular fa-credit-card-alt" size="lg"/>
                            <span class="align-middle">Subscriptions</span>
                        </router-link>
                    </li>

                    <li @click="selected = 'create_subscriptions'" :class="{active:selected === 'create_subscriptions'}" class="sidebar-item">
                        <router-link to="/admin/create-subscriptions" class="nav-link sidebar-link">
                            <font-awesome-icon icon="fa-regular fa-credit-card" size="lg"/>
                            <span class="align-middle">Create Subscription</span>
                        </router-link>
                    </li>

                    <!--GYM CLASSES-->
                    <li class="sidebar-header">
                        Bookings
                    </li>

                    <li @click="selected = 'reservations'" :class="{active:selected === 'reservations'}" class="sidebar-item">
                        <router-link to="/admin/admin-calendar" class="nav-link sidebar-link">
                            <font-awesome-icon icon="fa-regular fa-calendar" size="lg"/>
                            <span class="align-middle">Bookings</span>
                        </router-link>
                    </li>

                </ul>
            </div>
        </nav>

        <div class="main">
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle js-sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>

                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">
                        <li class="nav-item dropdown">
                            <drop-down-login></drop-down-login>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="content">

                <div id="admin-content">

                    <router-view class="view"></router-view>

                </div>
            </main>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0">
                                <strong>Christos Tsiamouras</strong> &copy;
                            </p>
                        </div>
<!--                        <div class="col-6 text-end">-->
<!--                            <ul class="list-inline">-->
<!--                                <li class="list-inline-item">-->
<!--                                    <a class="text-muted" href="https://adminkit.io/" target="_blank">Support</a>-->
<!--                                </li>-->
<!--                                <li class="list-inline-item">-->
<!--                                    <a class="text-muted" href="https://adminkit.io/" target="_blank">Help Center</a>-->
<!--                                </li>-->
<!--                                <li class="list-inline-item">-->
<!--                                    <a class="text-muted" href="https://adminkit.io/" target="_blank">Privacy</a>-->
<!--                                </li>-->
<!--                                <li class="list-inline-item">-->
<!--                                    <a class="text-muted" href="https://adminkit.io/" target="_blank">Terms</a>-->
<!--                                </li>-->
<!--                            </ul>-->
<!--                        </div>-->
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>

<!--<style scoped src="@/resources/css/adminApp.css">-->
<!--</style>-->


<script>
    // import adminApp from '../../adminApp.js';
    // import Vue from "vue";
    // import Auth from '../../auth.js';


    export default {
        data() {
            return {
                selected: 'subscription_plans',
            }
        },
    }
</script>
