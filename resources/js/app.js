/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

import Vue from 'vue';
import VueRouter from 'vue-router';
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
import ShowGymClasses from './components/admin/gymClasses/ShowGymClasses.vue';
import CreateGymClasses from './components/admin/gymClasses/CreateGymClasses.vue';

const router = new VueRouter({
    mode: 'history',
    base: __dirname,
    routes: [
        /* MAIN */
        { path: '/', component: MainPage },
        { path: '/login', component: Login },

        /* ADMIN */
        { path: '/admin', component: AdminHome,
            redirect: '/admin/show-subscription-plans',
            children: [
                {
                    // UserProfile will be rendered inside User's <router-view>
                    // when /user/:id/profile is matched
                    path: '/admin/show-subscription-plans',
                    component: ShowSubscriptionPlans,
                },
                {
                    // UserProfile will be rendered inside User's <router-view>
                    // when /user/:id/profile is matched
                    path: '/admin/create-subscription-plans',
                    component: CreateSubscriptionPlans,
                },
                {
                    // UserProfile will be rendered inside User's <router-view>
                    // when /user/:id/profile is matched
                    path: '/admin/show-gym-classes',
                    component: ShowGymClasses,
                },
                {
                    // UserProfile will be rendered inside User's <router-view>
                    // when /user/:id/profile is matched
                    path: '/admin/create-gym-classes',
                    component: CreateGymClasses,
                },
            ]
        },
        { path: '/home', component: Home },
    ]
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
Vue.component('show-gym-classes', require('./components/admin/gymClasses/ShowGymClasses.vue').default);
Vue.component('create-gym-classes', require('./components/admin/gymClasses/CreateGymClasses.vue').default);

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
