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
                        Προγράμματα Συνδρομής
                    </li>

                    <li @click="selected = 'subscription_plans'" :class="{active:selected === 'subscription_plans'}" class="sidebar-item">
                        <router-link to="/admin/show-subscription-plans" class="nav-link sidebar-link">
                            <i class="align-middle" data-feather="dollar-sign"></i> <span class="align-middle">Προγράμματα Συνδρομής</span>
                        </router-link>
                    </li>

                    <li @click="selected = 'create_subscription_plans'" :class="{active:selected === 'create_subscription_plans'}" class="sidebar-item">
                        <router-link to="/admin/create-subscription-plans" class="nav-link sidebar-link">
                            <i class="align-middle" data-feather="dollar-sign"></i> <span class="align-middle">Δημιουργία Προγράμματος Συνδρομής</span>
                        </router-link>
                    </li>

                    <!--GYM CLASSES-->
                    <li class="sidebar-header">
                        Τμήματα
                    </li>

                    <li @click="selected = 'gym_classes'" :class="{active:selected === 'gym_classes'}" class="sidebar-item">
                        <router-link to="/admin/show-gym-classes" class="nav-link sidebar-link">
                            <i class="align-middle" data-feather="home"></i> <span class="align-middle">Τμήματα</span>
                        </router-link>
                    </li>

                    <li @click="selected = 'create_gym_classes'" :class="{active:selected === 'create_gym_classes'}" class="sidebar-item">
                        <router-link to="/admin/create-gym-classes" class="nav-link sidebar-link">
                            <i class="align-middle" data-feather="home"></i> <span class="align-middle">Δημιουργία Τμήματος</span>
                        </router-link>
                    </li>

                    <!--SUBSCRIPTIONS-->
                    <li class="sidebar-header">
                        Συνδρομές Χρηστών
                    </li>

                    <li @click="selected = 'subscriptions'" :class="{active:selected === 'subscriptions'}" class="sidebar-item">
                        <router-link to="/admin/show-subscriptions" class="nav-link sidebar-link">
                            <i class="align-middle" data-feather="credit-card"></i> <span class="align-middle">Συνδρομές</span>
                        </router-link>
                    </li>

                    <li @click="selected = 'create_subscriptions'" :class="{active:selected === 'create_subscriptions'}" class="sidebar-item">
                        <router-link to="/admin/create-subscriptions" class="nav-link sidebar-link">
                            <i class="align-middle" data-feather="credit-card"></i> <span class="align-middle">Δημιουργία Συνδρομής</span>
                        </router-link>
                    </li>

                    <!--GYM CLASSES-->
                    <li class="sidebar-header">
                        Κρατήσεις
                    </li>

                    <li @click="selected = 'reservations'" :class="{active:selected === 'reservations'}" class="sidebar-item">
                        <router-link to="/admin/calendar" class="nav-link sidebar-link">
                            <i class="align-middle" data-feather="calendar"></i> <span class="align-middle">Κρατήσεις</span>
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
<!--                            <a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">-->
<!--                                <i class="align-middle" data-feather="settings"></i>-->
<!--                            </a>-->

<!--                            <a @click="expandSettingsMenu = !expandSettingsMenu" class="nav-link dropdown-toggle d-none d-sm-inline-block" :class="{show:expandSettingsMenu === true}" href="#" data-bs-toggle="dropdown" :aria-expanded="expandSettingsMenu">-->
<!--                                <i class="align-middle" data-feather="user"></i> <span class="text-dark">{{user.name}} {{user.surname}}</span>-->
<!--                            </a>-->


                            <i class="align-middle" data-feather="user"></i>
                            <b-dropdown id="dropdown-right" right :text="user.name" variant="primary" class="m-2">
                                <b-dropdown-item href="#"><i class="align-middle me-1" data-feather="user"></i> Profile</b-dropdown-item>
                                <b-dropdown-item href="#"><i class="align-middle me-1" data-feather="pie-chart"></i> Analytics</b-dropdown-item>
                                <b-dropdown-item href="#"><i class="align-middle me-1" data-feather="settings"></i> Settings & Privacy</b-dropdown-item>
                                <b-dropdown-item href="#"><i class="align-middle me-1" data-feather="help-circle"></i> Help Center</b-dropdown-item>
                                <b-dropdown-item href="#">Log out</b-dropdown-item>
                            </b-dropdown>

<!--                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block show" href="#" data-bs-toggle="dropdown" aria-expanded="true">-->
<!--                                <i class="align-middle" data-feather="user"></i> <span class="text-dark">{{user.name}} {{user.surname}}</span>-->
<!--                            </a>-->
<!--                            <div class="dropdown-menu dropdown-menu-end">-->
<!--                                <a class="dropdown-item" href="pages-profile.html"><i class="align-middle me-1" data-feather="user"></i> Profile</a>-->
<!--                                <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="pie-chart"></i> Analytics</a>-->
<!--                                <div class="dropdown-divider"></div>-->
<!--                                <a class="dropdown-item" href="index.html"><i class="align-middle me-1" data-feather="settings"></i> Settings & Privacy</a>-->
<!--                                <a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="help-circle"></i> Help Center</a>-->
<!--                                <div class="dropdown-divider"></div>-->
<!--                                <a class="dropdown-item" href="#">Log out</a>-->
<!--                            </div>-->
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
    import Auth from '../../auth.js';

    export default {
        data() {
            return {
                user: Auth.user,
                selected: 'subscription_plans',
            }
        },
        // methods:{
        //     expandSettingsMenu(gymClassId) {
        //         this.$router.push({ name: 'UpdateGymClasses', params: { id: gymClassId } })
        //     }
        // }
    }
</script>
