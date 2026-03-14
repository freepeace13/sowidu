import Vue from 'vue';
import Vuex from 'vuex';
import VuexLogger from './plugins/VuexLogger';
import QueueProcessor from './plugins/QueueProcessor';
import ActionLoadingState from './plugins/ActionLoadingState';
import ResetsHttpHeaders from './plugins/ResetsHttpHeaders';
import ResourcesProvider from './plugins/ResourcesProvider';
import AuthStore from '@features/auth/store';
import TaskStore from '@features/task/store';
import ProductStore from '@features/product/store';
import ContactStore from '@features/contact/store';
import OrderStore from '@features/ordering/store';
import DeliveryStore from '@features/delivery/store';
import EmployeeStore from '@features/employee/store';

Vue.use(Vuex);

const context = require.context('./modules', true, /^(?!.*modules).*\/index.js$/);

const modules = context.keys()
    .map(file => ([file.replace(/(^.\/)|(\.js$)|(\/index)/g, ''), context(file)]))
    .reduce((modules, [name, module]) => {
        if (module.default) {
            modules[name] = module.default
        }
        return modules
    }, {});

const Store = new Vuex.Store({
    strict: process.env.NODE_ENV !== 'production',
    modules: {
        ...modules,
        auth: AuthStore,
        task: TaskStore,
        product: ProductStore,
        contact: ContactStore,
        order: OrderStore,
        delivery: DeliveryStore,
        employee: EmployeeStore
    },
    plugins: [
        ResourcesProvider(),
        ResetsHttpHeaders(),
        VuexLogger(),
        QueueProcessor({
            sleep: 0,
            tries: 3,
            logger: false
        }),
        ActionLoadingState(
            'order/sketch/update',
            'delivery/all'
        )
    ],
});

export default Store;
