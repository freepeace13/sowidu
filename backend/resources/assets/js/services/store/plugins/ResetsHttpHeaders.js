/** @flow */

import axios from 'axios';
import { AUTH_GUARDS } from '~/support/constants';
import Vuex from 'vuex';

export default function ResetsHttpHeaders() {
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    axios.defaults.headers.common['Accept'] = 'application/json';

    return (store: Vuex) => {
        const { USER, COMPANY } = AUTH_GUARDS;

        // axios.interceptors.request.use(function (config) {
        //     delete config.headers.common['Authorization'];
        //     delete config.headers.common['X-Primary-Id'];

        //     if (store.getters['auth/check']()) {
        //         const accessToken = store.getters['auth/token']();
        //         config.headers.common['Authorization'] = `Bearer ${accessToken}`;

        //         if (store.getters['auth/check'](COMPANY)) {
        //             const profile = store.getters['auth/profile'](USER);
        //             config.headers.common['X-Primary-Id'] = profile.id;
        //         }
        //     }

        //     return config;
        // });
    }
}
