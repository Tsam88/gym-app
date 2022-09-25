/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

import Vue from 'vue';
import VueRouter from 'vue-router';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
// Import Bootstrap an BootstrapVue CSS files (order is important)
import 'bootstrap/dist/css/bootstrap.css'
import '../css/adminApp.css'
import '../css/app.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);

import Auth from './auth.js'

Vue.prototype.auth = Auth;
Vue.use(VueRouter,axios);

import MainPage from './components/MainPage.vue';
import Home from './components/Home.vue';
import Login from './components/main/auth/Login.vue';
/* Admin */
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

// response message alert handler
Vue.prototype.$alertHandler = new Vue({
    data() {
        return {
            dismissSecs: 5,
            dismissCountDown: 0,
            variant: '',
            responseMessage: '',
        }
    },
    methods: {
        countDownChanged(dismissCountDown) {
            this.dismissCountDown = dismissCountDown
        },
        showAlert(message, variant) {
            this.responseMessage = message;
            this.variant = variant;
            this.dismissCountDown = this.dismissSecs;
        },
    }
});

const router = new VueRouter({
    mode: 'history',
    base: __dirname,
    routes: [
        /* MAIN */
        { path: '/', name: 'MainPage', component: MainPage },
        { path: '/login', name: 'Login', component: Login },

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
                    path: '/admin/calendar', name: 'AdminCalendar', component: AdminCalendar,
                },
            ]
        },
        { path: '/home', name: 'Home', component: Home },
    ]
});

/**
 * Routes without authorization
 */
const routesWithoutAuthorization = [
    'Login',
    'Home',
    'MainPage',
];

/**
 * MIDDLEWARE
 */
router.beforeEach((to, from, next) => {
    // Routes that require Admin role
    if (to.path === '/admin/*') {
        if (Auth.isAdmin === true) {
            next();
        } else {
            next({ name: 'Login' });
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

Vue.component('main-page', require('./components/MainPage.vue').default);
Vue.component('home', require('./components/Home.vue').default);
Vue.component('login', require('./components/main/auth/Login.vue').default);

/* Admin */
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

const app = new Vue({
    router,
}).$mount('#app');

// const adminApp = new Vue({
//     adminRouter,
//     template: '<div><h1>AAAAAAAAAAAAAAAAAA</h1><div id="lol"></div></div>'
// }).$mount('#lol');

// app.use(router);

// app.mount('#app');
