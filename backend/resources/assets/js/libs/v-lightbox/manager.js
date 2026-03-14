import { mutations, actions } from './vuex/constants';
import vuexStore from './vuex/store';

export default class LightboxManager {
    store: Store;

    resumable: VueResumable;

    initialized: boolean = false;

    options: Object = {
        vuexModule: 'lightbox',
        url: (file) => null
    };

    static install = install;

    constructor(options: Object = {}) {
        this.options = { ...this.options, ...options };

        this.initialized = false;
    }

    init(vue, store, resumable) {
        if (! install.installed) {
            console.warn(
                `[v-notifications] not installed. Make sure to call \`Vue.use(Notification)\` before init root instance.`
            );
        }

        if (this.initialized) {
            return;
        }

        if (! store) {
            throw new Error('[v-lightbox] Vuex not initialized.');
        }

        this.store = store;

        if (! this.store._modules.get([this.options.vuexModule])) {
            this.store.registerModule(this.options.vuexModule, vuexStore);
        }

        if (! resumable) {
            throw new Error('[v-lightbox] Resumablejs not initialized.');
        }

        this.resumable = resumable;

        this.initialized = true;
    }

    __vuexNamespace(prop) {
        return `${this.options.vuexModule}/${prop}`;
    }

    get current() {
        return this.store.getters[this.__vuexNamespace('current')];
    }

    set current(value) {
        this.store.commit(this.__vuexNamespace(mutations.SET_CURRENT), value);
    }

    set items(value) {
        this.store.commit(this.__vuexNamespace(mutations.SET_ITEMS), value);
    }

    get items() {
        return this.store.state[this.options.vuexModule].items;
    }

    get visible() {
        return this.store.getters[this.__vuexNamespace('visible')];
    }

    get uploading() {
        return this.store.state[this.options.vuexModule].uploading;
    }

    set uploading(value) {
        this.store.commit(this.__vuexNamespace(mutations.UPLOADING), value);
    }

    get editing() {
        return this.store.state[this.options.vuexModule].editing;
    }

    set editing(value) {
        this.store.commit(this.__vuexNamespace(mutations.EDITING), value);
    }

    set settings(value) {
        this.store.commit(
            this.__vuexNamespace(mutations.APPLY_SETTINGS),
            Object.assign({ editable: true }, value)
        );
    }

    get settings() {
        return this.store.state[this.options.vuexModule].settings;
    }

    apply(file) {
        return this.store.dispatch(this.__vuexNamespace(actions.UPLOAD), {
            url: this.options.url(file),
            client: this.resumable,
            payload: [file]
        }).then((response) => {

            this.editing = false;

            this.store.commit(this.__vuexNamespace(mutations.UPDATE_ITEM_URL), {
                id: response.data.id,
                url: response.data.url
            });

            return response;
        });
    }

    close() {
        this.store.commit(this.__vuexNamespace(mutations.CLEAR_ITEMS));
    }

    open(items, current, settings = {}) {
        this.settings = settings;
        this.items = items;
        this.current = current;
    }
}

export function install(vue) {
    if (vue && install.installed) {
        console.warn('[v-lightbox] already installed.');
        return;
    }

    vue.mixin({
        beforeCreate() {
            const { store, parent, lightbox, resumable } = this.$options;

            let instance;

            if (lightbox) {
                instance = (typeof lightbox === 'function')
                    ? new lightbox
                    : lightbox;

                instance.init(vue, store, resumable);
            }

            else if (parent && parent.$lightbox) {
                instance = parent.$lightbox;
                instance.init(vue, parent.$store, parent.$resumable);
            }

            if (instance) {
                this.$lightbox = instance;
            }
        }
    });

    install.installed = true;
}