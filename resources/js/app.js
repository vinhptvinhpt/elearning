/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

import router from './router.js';
// import utils from './helpers/utilities'

require('./bootstrap');

import Lang from 'lang.js';
import messages from './messages.json';

Vue.prototype.trans = new Lang({
    messages: messages
});


const pagination_property = {};
pagination_property.install = function () {
    Object.defineProperty(Vue.prototype, '$pagination', {
        get() {
            return {
                labels: {
                    first: '<span>' + this.trans.get("keys.trang_dau") + '</span><i class="fa fa-angle-left first_page"></i>',
                    prev: '<span>' + this.trans.get("keys.trang_truoc") + '</span><i class="fa fa-angle-left"></i>',
                    next: '<span>' + this.trans.get("keys.trang_ke_tiep") + '</span><i class="fa fa-angle-right"></i>',
                    last: '<span>' + this.trans.get("keys.trang_cuoi") + '</span><i class="fa fa-angle-right last_page"></i>'
                },
                classes: {
                    ul: 'pagination',
                    li: 'page-item',
                    liActive: 'active',
                    liDisable: 'disabled',
                    button: 'page-link'
                }
            };
        }
    });
};
Vue.use(pagination_property);

// Vue.prototype.$utils = utils;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
$.holdReady(true);
const app = new Vue({
    el: '#app',
    data: {},
    methods: {},
    router: router,
    mounted() {
        $.holdReady(false);
    }
});
