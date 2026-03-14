import Vue from 'vue';
import Vuex from 'vuex';
import vResumableComponent from './components/v-resumable';
import vuexStore from './vuex/store';
import { nodeIsDebug, bytesSum } from './utils';

export default class VueResumable {
    store: Object;
    initialized: boolean = false;
    options: Object = {};

    static install = install;

    constructor(options: Object = {}) {
        const defaults = {
            accessorName: '$resumable',
            vuexModuleName: 'resumable',
            httpHeaders: (store: Vuex) => ({
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            })
        };

        this.options = { ...defaults, ...options };

        this.initialized = false;
    }

    init(Vue: Vue, store: Vuex) {
        if (nodeIsDebug() && !install.installed) {
            console.warn(
                `[vue-resumable] not installed. Make sure to call \`Vue.use(VueResumable)\` before init root instance.`
            );
        }

        if (this.initialized) {
            return;
        }

        if (! store) {
            throw new Error('[vue-resumable] Vuex not initialized.');
        }

        this.store = store;

        if (! store._modules.get([this.options.vuexModuleName])) {
            store.registerModule(this.options.vuexModuleName, vuexStore);
        }

        this.initialized = true;
    }

    get show() {
        return this.files.length > 0;
    }

    get uploaded() {
        return this.files.filter((v) => v.isSuccess());
    }

    get completed() {
        const pendings = this.files.filter((v) => v.isPending());
        return pendings.length === 0 && !this.uploading;
    }

    get minimized() {
        const { vuexModuleName } = this.options;
        return this.store.state[vuexModuleName].minimized;
    }

    get uploading() {
        const { vuexModuleName } = this.options;
        return this.store.getters[`${vuexModuleName}/uploading`]();
    }

    get progress() {
        const { vuexModuleName } = this.options;
        return this.store.getters[`${vuexModuleName}/progress`]();
    }

    get files() {
        const { vuexModuleName } = this.options;
        return this.store.state[vuexModuleName].files;
    }

    minimize() {
        const { vuexModuleName } = this.options;
        this.store.commit(`${vuexModuleName}/minimize`);
    }

    maximize() {
        const { vuexModuleName } = this.options;
        this.store.commit(`${vuexModuleName}/maximize`);
    }

    close() {
        const { vuexModuleName } = this.options;
        this.store.dispatch(`${vuexModuleName}/close`);
    }

    upload(uri: string, files: any) {
        const { vuexModuleName, httpHeaders } = this.options;

        return (successCallback: Function, errorCallback: Function) => {
            if (typeof successCallback !== 'function') {
                successCallback = () => {};
            }

            if (typeof errorCallback === 'function') {
                errorCallback = () => {};
            }

            this.store.dispatch(`${vuexModuleName}/clear`);

            this.store.dispatch(`${vuexModuleName}/upload`, {
                target: uri,
                files: files,
                headers: httpHeaders(this.store),
                successCallback,
                errorCallback
            });
        }
    }
}

export function install(Vue: Vue) {
    if (install.installed && Vue) {
        if (nodeIsDebug()) {
            console.warn('[vue-resumable] already installed.');
        }

        return;
    }

    Vue.mixin({
        beforeCreate() {
            const { resumable, store, parent } = this.$options;

            let instance = null;

            if (resumable) {
                instance = (typeof resumable === 'function')
                    ? new resumable()
                    : resumable;

                instance.init(Vue, store);
            } else if (parent && parent.__$resumableInstance) {
                instance = parent.__$resumableInstance;
                instance.init(Vue, parent.$store);
            }

            if (instance) {
                this.__$resumableInstance = instance;
                this[instance.options.accessorName] = instance;
            }
        }
    });

    install.installed = true;
}