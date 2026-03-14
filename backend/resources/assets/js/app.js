import './Bootstrap';
import Vue from 'vue';
import Vuetify from 'vuetify';
import VueWait from 'vue-wait';
import router from './routes';
import mixin from './Mixin';
import store from '~/services/store';
import Spy from './utils/spy';
import { $modal } from '~/services/events/modal';
import { $uploader } from '~/services/events/chunkUploader';
import { $confirm } from '~/services/events/confirmDialog';
import VueResumable from '@libs/vue-resumable';
import VueNotification from '@libs/v-notifications';
import i18n from '@libs/localization';
import VueLightbox from '@libs/v-lightbox';
import VueAsyncManager from 'vue-async-manager';
import App from './components/App';
import Cache from '~/services/cache';
import createHttpHeadersFromVuex from './support/httpHeaders';
import * as helpers from './support/helpers';

import { AUTH_GUARDS } from  './support/constants';

import '../sass/app.scss';

import './app/bootstrap';

import './Filters';
import './Components';
import './Directives';

Vue.use(Vuetify)
Vue.use(VueWait)
Vue.use(VueResumable);
Vue.use(VueLightbox);
Vue.use(VueNotification);
Vue.use(VueAsyncManager, { mode: 'hidden' });

Vue.prototype.$modal = $modal;
Vue.prototype.$uploader = $uploader;
Vue.prototype.$confirm = $confirm;
Vue.prototype.$events = require('./services/events').default;

Vue.prototype.$guards = {
    user: AUTH_GUARDS.USER,
    company: AUTH_GUARDS.COMPANY
}

window.AppEvent = new Spy();
window.Cache = Cache;

Vue.config.productionTip = false;
Vue.config.debug = true;
Vue.config.strict = true;

Vue.mixin(mixin);

const httpHeaders = (store) => createHttpHeadersFromVuex(store);
const authUser = (store) => store.getters['auth/profile']();

new Vue({
    el: '#app',
    i18n,
    router,
    store,
    provide() {
        const container = document.getElementById('app');

        return {
            errors: helpers.isJson(container.dataset.errors)
                ? JSON.parse(container.dataset.errors)
                : {}
        };
    },
    lightbox: new VueLightbox({
        url: (file) => `/api/media/${file.identifier}/upload`
    }),
    notification: new VueNotification({
        authUser,
        httpHeaders,
    }),
    resumable: new VueResumable({
        httpHeaders
    }),
    wait: new VueWait({
        useVuex: true
    }),
    ...App
});

