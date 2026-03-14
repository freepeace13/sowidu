import CookieStorage from '~/services/store/storages/cookie';
import SecureLocalStorage from '~/services/store/storages/secureLS';

export default {
    logger: {
        enable: process.env.MIX_VUEX_LOGGER || false
    },

    persistence: {
        storage: {
            'auth-session': CookieStorage({
                secure: false,
                expires: 8, // days
            }),

            'secure-ls': SecureLocalStorage({
                isCompression: false,
                // expires: 5, // minutes
            })
        },

        modules: [
            {
                key: 'app:auth',
                storage: 'auth-session',
                filters: [
                    'auth/*/SET_TOKEN',
                    'auth/*/LOGOUT'
                ],
                paths: [
                    'auth.user.accessToken',
                    'auth.company.accessToken'
                ]
            },

            {
                key: 'app:resources',
                storage: 'secure-ls',
                filters: [
                    'auth/SET_PERMISSIONS',
                    'auth/user/*_PROFILE',
                    'auth/company/*_PROFILE',
                    'auth/SET_COMPANIES',
                    'auth/INSERT_COMPANY',
                    //'contact/*',
                    //'customer/*',
                    //'delivery/*',
                    //'employee/*',
                    'invitation/*',
                    //'media/*',
                    //'order/*',
                    //'product/*',
                    //'task/*'
                ],
                paths: [
                    'auth.user.profile',
                    'auth.company.profile',
                    'auth.companies',
                    'auth.permissions',
                    //'contact.contacts',
                    //'customer.customers',
                    //'delivery.deliveries',
                    //'employee.employees',
                    'invitation.invitations',
                    //'media.media',
                    //'order.orders',
                    //'product.items',
                    //'task.tasks'
                ]
            }
        ],

        hydrateState: function* (store) {
            // yield store.dispatch('task/all');
            // yield store.dispatch('contact/all');
            // yield store.dispatch('media/all');
            // yield store.dispatch('order/all');
            // yield store.dispatch('delivery/all');
            // yield store.dispatch('product/all');
            // yield store.dispatch('customer/all');
            // yield store.dispatch('auth/fetchCompanies');
            // yield store.dispatch('auth/fetchPermissions');
            // yield store.dispatch('invitation/all');

            // if (store.getters['auth/check']('company')) {
            //     yield store.dispatch('employee/all');
            // }
        },

        resetState: (store) => {
            store.commit('auth/SET_PERMISSIONS', []);
            store.commit('auth/SET_COMPANIES', []);
            store.commit('task/SET_TASKS', []);
            store.commit('contact/SET_CONTACTS', []);
            store.commit('order/SET_ORDERS', []);
            store.commit('delivery/SET_DELIVERIES', []);
            store.commit('product/SET_ITEMS', []);
            store.commit('media/SET_MEDIA', []);
            store.commit('customer/SET_CUSTOMERS', []);
            store.commit('invitation/SET_INVITATIONS', []);
            store.commit('employee/SET_EMPLOYEES', []);
        }
    }
}