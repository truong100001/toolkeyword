
require('./bootstrap');

window.Vue = require('vue');

import Vue from 'vue';
import Vuetify from 'vuetify';
Vue.use(Vuetify);


import Swal from 'sweetalert2';
window.Swal = Swal;

Vue.component('main-component', require('./components/MainComponent.vue').default);


const app = new Vue({
    vuetify: new Vuetify(),
    el: '#app'
});
