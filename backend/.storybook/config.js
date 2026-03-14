import Vue from 'vue';
import Vuetify from 'vuetify';
import { configure } from "@storybook/vue";
import App from '../resources/assets/js/stories/app';
import { $modal } from '~/services/events/modal';
import ModalLayout from '~/components/layouts/Modal';

import '../resources/assets/js/Filters';

import '../resources/assets/sass/style.scss';
import '../resources/assets/js/libs/slim/slim.min.css';
import '../resources/assets/stylus/app.styl';

// Install Plugins
Vue.use(Vuetify);

// Global Components
Vue.component('application', App);
Vue.component('ModalLayout', ModalLayout);

// Custom Prototypes
Vue.prototype.$modal = $modal;

configure(
    require.context('../resources/assets/js/stories',
    true,
    /\.stories\.js$/),
    module
);