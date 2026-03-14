/** @flow */

import Vuex from 'vuex';
import config from '~/config';
import Cache from '~/services/cache';
import PersistedState from './persistedState';
import * as helpers from '~/support/helpers';
import resolveMisc from './resolveMisc'
import GuestLayout from '~/components/LayoutGuest';
import createMutationPolicy from './mutationPolicy';
import registerWSAuthorizableChannel from '../../websocket/wsAuthorizableChannel';
import registerWSAuthenticatableChannel from '../../websocket/wsAuthenticatableChannel';
import WorkerCall from '@common/utils/WorkerCall';

let iterator;
let job;

const generator = async function* (store) {
    // yield { key: 'media', handler: () => store.dispatch('media/all') };
    // yield { key: 'delivery', handler: () => store.dispatch('delivery/all') };
    // yield { key: 'permissions', handler: () => store.dispatch('auth/fetchPermissions') };
    // yield { key: 'invitation', handler: () => store.dispatch('invitation/all') };
    //yield { key: 'customer', handler: () => store.dispatch('customer/all') };

    if (store.getters['auth/check']('company')) {
       // yield { key: 'employee', handler: () => store.dispatch('employee/all') };
    }
}

const stateHydrator = (store: Vuex) => {
    helpers.isGenerator(iterator) && iterator.return();

    iterator = generator(store);

    (async () => {
        for await (const item of iterator) {
            WorkerCall(item.handler)((result) => {
                console.log(`Module data for [${item.key}] has been loaded.`, result);
            }, (error) => {
                console.error(`Error occured while processing module data [${item.key}]`, error);
            });
        }
    })()
}

export default function () {
    const $app = document.getElementById('app');

    return function (store: Vuex) {
        const policies = createMutationPolicy(store);

        const fetchProfile = () => store.dispatch('auth/fetchProfile');
        const isAuthenticated = () => store.getters['auth/isAuth'];
        const isProfileExists = () => store.getters['auth/isProperlyAuthenticated'];

        if (typeof $app === 'object' && $app !== null) {
            if (helpers.isJson($app.dataset.misc)) {
                const miscState = resolveMisc(JSON.parse($app.dataset.misc));

                if (miscState !== null) {
                    store.replaceState({ ...store.state, misc: miscState });
                }
            }
        }

        config('vuex.persistence.modules').forEach((module) => {
            PersistedState(module)(store);
        });

        if (isAuthenticated()) {
            setTimeout(() => {
                if (! isProfileExists()) {
                    fetchProfile();
                } else {
                    // $FlowFixMe
                    Echo.connector.pusher.connection.bind('connected', () => {
                        registerWSAuthorizableChannel(Echo, store);
                        registerWSAuthenticatableChannel(Echo, store);

                        stateHydrator(store);
                    });
                }
            }, 0);
        }

        store.subscribe((mutation: Object, state: Object) => {
            if (policies.isProfileReloadAllowed(mutation)) {
                fetchProfile();
            }

            if (policies.isModulesReloadAllowed(mutation)) {
                window.location.reload();

                // $FlowFixMe
                registerWSAuthorizableChannel(Echo, store);
                registerWSAuthenticatableChannel(Echo, store);

                stateHydrator(store);
            }

            if (policies.isResetStateAllowed(mutation)) {
                store.commit('ui/SET_LAYOUT', GuestLayout.name);
                window.location.reload();
            }
        });
    }   
}