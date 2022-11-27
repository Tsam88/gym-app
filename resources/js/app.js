/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import $ from 'jquery';
window.$ = window.jQuery = $;

require('./bootstrap');

window.Vue = require('vue').default;

import Vue from 'vue';
import VueRouter from 'vue-router';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';

// autocomplete select
import vSelect from 'vue-select';
Vue.component('v-select', vSelect);
Vue.component('v-select', VueSelect.VueSelect);

// Import Bootstrap an BootstrapVue CSS files (order is important)
// import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/css/bootstrap-grid.css'
import 'bootstrap/dist/css/bootstrap-reboot.css'
import '../css/adminApp.css'
import '../css/app.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import '../css/font-awesome.min.css'
import '../css/flaticon.css'
import '../css/owl.carousel.min.css'
import '../css/barfiller.css'
import '../css/slicknav.min.css'
import '../css/style.css'
import 'vue-select/dist/vue-select.css';

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);

import adminApp from './adminApp.js'
import Auth from './auth.js'

Vue.prototype.auth = Auth;
Vue.use(VueRouter,axios);

/* FONT AWESOME START */
/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* import specific icons */
import { faUser as faUserSolid, faHouse, faHouseUser, faDollarSign, faMobileScreenButton, faPhone, faLock, faEnvelope, faCircleCheck, faCircleXmark } from '@fortawesome/free-solid-svg-icons'
import { faCreditCard, faUser, faCalendar, faCircle, faCalendarXmark, faTrashCan } from '@fortawesome/free-regular-svg-icons'
import { faTwitter, faFacebook, faStackOverflow, faGithub } from '@fortawesome/free-brands-svg-icons'

/* add icons to the library */
library.add( faUserSolid, faHouse, faHouseUser, faDollarSign, faMobileScreenButton, faPhone, faLock, faEnvelope, faCircleCheck, faCircleXmark, faCreditCard, faUser, faCalendar, faCircle, faCalendarXmark, faTrashCan);

/* add font awesome icon component */
Vue.component('font-awesome-icon', FontAwesomeIcon);

Vue.config.productionTip = false;
/* FONT AWESOME END */


/* IMPORT ALL VUE COMPONENTS */
/* MAIN PAGE */
import MainPage from './components/MainPage.vue';
import Home from './components/main/Home.vue';
import HeaderMenu from './components/main/HeaderMenu.vue';
import FooterSection from './components/main/FooterSection.vue';
import HomeContent from './components/main/HomeContent.vue';
import WeekCalendar from './components/main/weekCalendar/WeekCalendar.vue';
import Login from './components/main/auth/Login.vue';
import Register from './components/main/auth/Registration.vue';
import EmailVerificationCompleted from './components/main/emailVerification/EmailVerificationCompleted.vue';
import EmailVerificationRequired from './components/main/emailVerification/EmailVerificationRequired.vue';
import ForgotPassword from './components/main/users/ForgotPassword.vue';
import ResetPassword from './components/main/users/ResetPassword.vue';
import ChangePassword from './components/main/users/ChangePassword.vue';
import ChangeEmail from './components/main/users/ChangeEmail.vue';
import Profile from './components/main/users/Profile.vue';
import StudentCalendar from './components/main/reservations/StudentCalendar.vue';
import Error404 from './components/main/404.vue';
/* ADMIN */
import AdminHome from './components/admin/Home.vue';
import ShowSubscriptionPlans from './components/admin/subscriptionPlans/ShowSubscriptionPlans.vue';
import CreateSubscriptionPlans from './components/admin/subscriptionPlans/CreateSubscriptionPlans.vue';
import UpdateSubscriptionPlans from './components/admin/subscriptionPlans/UpdateSubscriptionPlans.vue';
import ShowGymClasses from './components/admin/gymClasses/ShowGymClasses.vue';
import CreateGymClasses from './components/admin/gymClasses/CreateGymClasses.vue';
import UpdateGymClasses from './components/admin/gymClasses/UpdateGymClasses.vue';
import ShowSubscriptions from './components/admin/subscriptions/ShowSubscriptions.vue';
import CreateSubscriptions from './components/admin/subscriptions/CreateSubscriptions.vue';
import UpdateSubscriptions from './components/admin/subscriptions/UpdateSubscriptions.vue';
import AdminCalendar from './components/admin/reservations/AdminCalendar.vue';
import ShowExcludedCalendarDates from './components/admin/excludedCalendarDates/ShowExcludedCalendarDates.vue';
import CreateExcludedCalendarDates from './components/admin/excludedCalendarDates/CreateExcludedCalendarDates.vue';
import UpdateExcludedCalendarDates from './components/admin/excludedCalendarDates/UpdateExcludedCalendarDates.vue';
/* GENERAL COMPONENTS */
import DropDownLogin from './components/generalComponents/DropDownLogin.vue';

// response message alert handler
Vue.prototype.$alertHandler = new Vue({
    data() {
        return {
            dismissSecs: 5,
            dismissCountDown: 0,
            variant: '',
            successCodes: [201,204],
            warningCodes: [451],
            dangerCodes: [404,409,412,417,422],
            responseMessage: '',
        }
    },
    methods: {
        countDownChanged(dismissCountDown) {
            this.dismissCountDown = dismissCountDown
        },
        showAlert(message, code) {
            if (this.successCodes.includes(code)) {
                this.variant = 'success';
            } else if (this.dangerCodes.includes(code)) {
                this.variant = 'danger';
            } else if (this.warningCodes.includes(code)) {
                this.variant = 'warning';
            } else {
                return;
            }

            this.responseMessage = message;
            this.dismissCountDown = this.dismissSecs;
        },
    }
});

const router = new VueRouter({
    mode: 'history',
    base: __dirname,
    routes: [
        /* MAIN */
        { path: '', component: MainPage, children: [
                /* MAIN PAGE */
                { path: '/', component: Home, children: [
                        { path: '/', name: 'HomeContent', component: HomeContent },
                        { path: '/sign-in', name: 'Login', component: Login },
                        { path: '/sign-up', name: 'Register', component: Register },
                        { path: '/email-verification-completed', name: 'EmailVerificationCompleted', component: EmailVerificationCompleted },
                        { path: '/email-verification-required', name: 'EmailVerificationRequired', component: EmailVerificationRequired },
                        { path: '/forgot-password', name: 'ForgotPassword', component: ForgotPassword },
                        { path: '/reset-password', name: 'ResetPassword', component: ResetPassword },
                        { path: '/change-password', name: 'ChangePassword', component: ChangePassword },
                        { path: '/change-email', name: 'ChangeEmail', component: ChangeEmail },
                        { path: '/profile', name: 'Profile', component: Profile },
                        { path: '/student-calendar', name: 'StudentCalendar', component: StudentCalendar },
                    ]
                },
                /* ADMIN */
                { path: '/admin', name: 'AdminHome', component: AdminHome,
                    redirect: '/admin/show-subscription-plans',
                    children: [
                        // Subscription Plans
                        {
                            // UserProfile will be rendered inside User's <router-view>
                            // when /user/:id/profile is matched
                            path: '/admin/show-subscription-plans', name: 'ShowSubscriptionPlans', component: ShowSubscriptionPlans,
                        },
                        {
                            path: '/admin/create-subscription-plans', name: 'CreateSubscriptionPlans', component: CreateSubscriptionPlans,
                        },
                        {
                            path: '/admin/update-subscription-plans/:id', name: 'UpdateSubscriptionPlans', component: UpdateSubscriptionPlans, props: { default: true, sidebar: false }
                        },
                        // Gym Classes
                        {
                            path: '/admin/show-gym-classes', name: 'ShowGymClasses', component: ShowGymClasses,
                        },
                        {
                            path: '/admin/create-gym-classes', name: 'CreateGymClasses', component: CreateGymClasses,
                        },
                        {
                            path: '/admin/update-gym-classes/:id', name: 'UpdateGymClasses', component: UpdateGymClasses, props: { default: true, sidebar: false }
                        },
                        // Subscriptions
                        {
                            path: '/admin/show-subscriptions', name: 'ShowSubscriptions', component: ShowSubscriptions,
                        },
                        {
                            path: '/admin/create-subscriptions', name: 'CreateSubscriptions', component: CreateSubscriptions,
                        },
                        {
                            path: '/admin/update-subscriptions/:id', name: 'UpdateSubscriptions', component: UpdateSubscriptions, props: { default: true, sidebar: false }
                        },
                        // Reservations
                        {
                            path: '/admin/admin-calendar', name: 'AdminCalendar', component: AdminCalendar,
                        },
                        // Excluded calendar dates
                        {
                            path: '/admin/show-excluded-calendar-dates', name: 'ShowExcludedCalendarDates', component: ShowExcludedCalendarDates,
                        },
                        {
                            path: '/admin/create-excluded-calendar-dates', name: 'CreateExcludedCalendarDates', component: CreateExcludedCalendarDates,
                        },
                        {
                            path: '/admin/update-excluded-calendar-dates/:id', name: 'UpdateExcludedCalendarDates', component: UpdateExcludedCalendarDates, props: { default: true, sidebar: false }
                        },
                    ]
                },
                { path: '*', name:'404',  component: Error404 },
            ]
        },
    ]
});

/**
 * Routes without authorization
 */
const routesWithoutAuthorization = [
    'Login',
    'Register',
    'Home',
    'HomeContent',
    'MainPage',
    'ForgotPassword',
    'ResetPassword',
];
/**
 * Routes that require verified user
 */
const routesRequireVerifiedUser = [
    'StudentCalendar',
];

/**
 * MIDDLEWARE
 */
router.beforeEach((to, from, next) => {
    // let resolved = router.resolve(to.path);
    // if(resolved.route.name === 'Error404') {
    //     next({ name: 'Error404' });
    // }

    // Routes that require Admin role
    if (to.path.startsWith("/admin")) {
        if (Auth.isAdmin() === true) {
            next();
        } else {
            next({ name: 'Login' });
        }
    }

    // Routes that require verified user
    if (!routesRequireVerifiedUser.includes(to.name)) {
        next();
    } else {
        if (Auth.isVerified() === true) {
            next();
        } else {
            // display error message
            next({ name: 'EmailVerificationRequired' });
        }
    }

    // Routes that require authorization
    if (routesWithoutAuthorization.includes(to.name)) {
        next();
    } else {
        if (Auth.isAuthorized() === true) {
            next();
        }
        else {
            next({ name: 'Login' })
        }
    }
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *w
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

/* MAIN PAGE */
Vue.component('main-page', require('./components/MainPage.vue').default);
Vue.component('home', require('./components/main/Home.vue').default);
Vue.component('header-menu', require('./components/main/HeaderMenu.vue').default);
Vue.component('footer-section', require('./components/main/FooterSection.vue').default);
Vue.component('home-content', require('./components/main/HomeContent.vue').default);
Vue.component('week-calendar', require('./components/main/weekCalendar/WeekCalendar.vue').default);
Vue.component('login', require('./components/main/auth/Login.vue').default);
Vue.component('register', require('./components/main/auth/Registration.vue').default);
Vue.component('email-verification-completed', require('./components/main/emailVerification/EmailVerificationCompleted.vue').default);
Vue.component('email-verification-required', require('./components/main/emailVerification/EmailVerificationRequired.vue').default);
Vue.component('forgot-password', require('./components/main/users/ForgotPassword.vue').default);
Vue.component('reset-password', require('./components/main/users/ResetPassword.vue').default);
Vue.component('change-password', require('./components/main/users/ChangePassword.vue').default);
Vue.component('change-email', require('./components/main/users/ChangeEmail.vue').default);
Vue.component('profile', require('./components/main/users/Profile.vue').default);
Vue.component('student-calendar', require('./components/main/reservations/StudentCalendar.vue').default);
Vue.component('Error-404', require('./components/main/404.vue').default);
/* ADMIN */
Vue.component('admin-home', require('./components/admin/Home.vue').default);
Vue.component('show-subscription-plans', require('./components/admin/subscriptionPlans/ShowSubscriptionPlans.vue').default);
Vue.component('create-subscription-plans', require('./components/admin/subscriptionPlans/CreateSubscriptionPlans.vue').default);
Vue.component('update-subscription-plans', require('./components/admin/subscriptionPlans/UpdateSubscriptionPlans.vue').default);
Vue.component('show-gym-classes', require('./components/admin/gymClasses/ShowGymClasses.vue').default);
Vue.component('create-gym-classes', require('./components/admin/gymClasses/CreateGymClasses.vue').default);
Vue.component('update-gym-classes', require('./components/admin/gymClasses/UpdateGymClasses.vue').default);
Vue.component('show-subscriptions', require('./components/admin/subscriptions/ShowSubscriptions.vue').default);
Vue.component('create-subscriptions', require('./components/admin/subscriptions/CreateSubscriptions.vue').default);
Vue.component('update-subscriptions', require('./components/admin/subscriptions/UpdateSubscriptions.vue').default);
Vue.component('admin-calendar', require('./components/admin/reservations/AdminCalendar.vue').default);
Vue.component('show-excluded-calendar-dates', require('./components/admin/excludedCalendarDates/ShowExcludedCalendarDates.vue').default);
Vue.component('create-excluded-calendar-dates', require('./components/admin/excludedCalendarDates/CreateExcludedCalendarDates.vue').default);
Vue.component('update-excluded-calendar-dates', require('./components/admin/excludedCalendarDates/UpdateExcludedCalendarDates.vue').default);
/* GENERAL COMPONENTS */
Vue.component('drop-down-login', require('./components/generalComponents/DropDownLogin.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// chrome.runtime.onMessage.addListener(function(rq, sender, sendResponse) {
//     setTimeout(function() {
//         sendResponse({status: true});
//     }, 1);
//     return true;  // Return true to fix the error
// });

// import { reactive } from 'vue'
// export const loggedInUser = Auth.user ? reactive(Auth.user) : reactive({});
// Object.assign(loggedInUser, this.auth.user);

const app = new Vue({
    router,
}).$mount('#app');


// function for validating forms in app
// (function () {
//     'use strict';
//     const forms = document.querySelectorAll('.requires-validation');
//     Array.from(forms)
//         .forEach(function (form) {
//             form.addEventListener('submit', function (event) {
//                 if (!form.checkValidity()) {
//                     event.preventDefault();
//                     event.stopPropagation();
//                 }
//
//                 form.classList.add('was-validated');
//             }, false);
//         });
// })();

// const adminApp = new Vue({
//     adminRouter,
//     template: '<div><h1>AAAAAAAAAAAAAAAAAA</h1><div id="lol"></div></div>'
// }).$mount('#lol');

// app.use(router);

// app.mount('#app');
