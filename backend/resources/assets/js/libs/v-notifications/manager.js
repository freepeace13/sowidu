import Store from 'vuex';
import createStore from './vuex/store';
import Vue from 'vue';
import { watch } from '@libs/core';
import { actions, mutations } from './vuex/constants';

export default class NotificationManager {
    store: Store;
    initialized: boolean = false;
    options: Object = {
        wsEvent: '.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated',
        vuexModule: 'notification',
        authUser: (store) => null,
        httpHeaders: (store) => ({})
    };

    static install = install;

    constructor(options: Object = {}) {
        this.options = { ...this.options, ...options };

        this.initialized = false;
    }

    init(vue, store) {
        if (! install.installed) {
            console.warn(
                `[v-notifications] not installed. Make sure to call \`Vue.use(Notification)\` before init root instance.`
            );
        }

        if (this.initialized) {
            return;
        }

        this.store = store

        this._initStore();
        this._initEcho();

        this.initialized = true;
    }

    _initStore(store) {
        if (! this.store) {
            throw new Error('[v-notifications] Vuex not initialized.');
        }

        if (! this.store._modules.get([this.options.vuexModule])) {
            this.store.registerModule(
                this.options.vuexModule,
                createStore({
                    headers: this.options.httpHeaders(this.store)
                })
            );
        }
    }

    _initEcho() {
        if (! window.Echo) {
            throw new Error('[v-notifications] Echo not initialized.');
        }

        if (window.Echo.connector.pusher.connection.connection === null) {
            window.Echo.connector.pusher.connect();
        }

        window.Echo.connector.pusher.connection.bind('connected', () => {
            const { store, options } = this;

            watch(() => options.authUser(store), (profile) => {
                if (profile && profile.exists()) {
                    const notifiable = profile.notifiable();

                    window.Echo
                        .private(`${notifiable.entity}.${notifiable.id}`)
                        .stopListening(options.wsEvent)
                        .notification(this.insert.bind(this));
                }
            }, true);
        });
    }

    get items() {
        const { vuexModule } = this.options;
        return this.store.state[vuexModule].notifications;
    }

    get toasts() {
        const { vuexModule } = this.options;
        return this.store.state[vuexModule].toasts;
    }

    retrieve() {
        const { vuexModule } = this.options;
        this.store.dispatch(`${vuexModule}/${actions.ALL}`);
    }

    read(id) {
        const { vuexModule } = this.options;
        return this.store.dispatch(`${vuexModule}/${actions.READ}`, id);
    }

    insert(notification) {
        const { vuexModule } = this.options;
        const mutationType = `${vuexModule}/${mutations.INSERT_NOTIFICATION}`;

        this.store.commit(mutationType, notification);
    }

    removeToast(index) {
        const { vuexModule } = this.options;
        const mutationType = `${vuexModule}/${mutations.REMOVE_TOAST}`;

        this.store.commit(mutationType, index);
    }
}

export function install(vue) {
    if (vue && install.installed) {
        console.warn('[v-notifications] already installed.');
        return;
    }

    vue.mixin({
        beforeCreate() {
            const { notification, store, parent } = this.$options;

            let instance = null;

            if (notification) {
                instance = (typeof notification === 'function')
                    ? new notification
                    : notification;

                instance.init(vue, store);
            }
            
            else if (parent && parent.$notification) {
                instance = parent.$notification;
                instance.init(vue, parent.$store);
            }

            if (instance) {
                this.$notification = instance;
            }
        }
    });

    install.installed = true;
}