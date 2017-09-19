import _ from 'lodash';
import Vue from 'vue';
import VueResource from 'vue-resource';

window._ = _;
window.Vue = Vue;
window.eventBus = new Vue();

Vue.use(VueResource);

Vue.http.interceptors.push((request, next) => {
    request.headers.set('X-CSRF-TOKEN', Laravel.csrfToken);

    next();
});