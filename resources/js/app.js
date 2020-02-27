
import router from './router.js';

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
