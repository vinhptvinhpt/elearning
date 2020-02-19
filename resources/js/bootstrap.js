import VueRouter from 'vue-router';
import Ls from './services/ls';


/**
 * Global CSS imports
 */
import 'vue-tabs-component/docs/resources/tabs-component.css';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import 'vue2-dropzone/dist/vue2Dropzone.css';
import 'vue-select/dist/vue-select.css';

import {BootstrapVue} from 'bootstrap-vue';
import VSwitch from 'v-switch-case';
import vSelect from 'vue-select';

/**
 * Global plugins
 */
global.notie = require('notie');
global.toastr = require('toastr');
global._ = require('lodash');

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {
}

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

global.Vue = require('vue');

/**
 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */

global.axios = require('axios');

global.axios.defaults.headers.common = {
    'X-Requested-With': 'XMLHttpRequest'
};

/**
 * Global Axios Request Interceptor
 */

global.axios.interceptors.request.use(function (config) {
    // Do something before request is sent
    const AUTH_TOKEN = Ls.get('auth.token');

    if (AUTH_TOKEN) {
        config.headers.common['Authorization'] = `Bearer ${AUTH_TOKEN}`;
    }

    return config;
}, function (error) {
    // Do something with request error
    return Promise.reject(error);
});

/**
 * Custom Directives
 */
// require('./helpers/directives');


Vue.use(VueRouter);
Vue.use(BootstrapVue);
Vue.use(VSwitch);
// Vue.use(IconsPlugin);

Vue.component('v-pagination', require('vue-plain-pagination'));
Vue.component('v-select', vSelect);

