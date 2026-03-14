import config from '~/config';
import createLogger from 'vuex/dist/logger';

// TODO: Controls should be configured
const DEBUG = false;

const actionLoggerPlugin = (store) => {
    store.subscribeAction((context, state) => {
        console.log('%c action', 'color: #03A9F4; font-weight: bold', context)
    })
}

export default function VuexLogger() {
    return (store) => {
        if (config('vuex.logger.enable')) {
            createLogger()(store);

            store.subscribeAction((context, state) => {
                console.log('%c action', 'color: #03A9F4; font-weight: bold', context);
            });
        }
    }
}