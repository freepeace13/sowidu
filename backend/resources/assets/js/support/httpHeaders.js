/** @flow */

import Vuex from 'vuex';

export default function createHttpHeadersFromVuex(store: Vuex) {
    const defaults: { [key: string]: string } = {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    };

    if (store.getters['auth/check']()) {
        defaults['Authorization'] = `Bearer ${store.getters['auth/token']()}`;

        if (store.getters['auth/check']('company')) {
            defaults['X-Primary-Id'] = store.getters['auth/profile']('user').id;
        }
    }

    return defaults;
}