/** @flow */

import config from '~/config';
import CacheStore from './store';
import * as helpers from '~/support/helpers';

const getValue = (value: any) => helpers.isFunction(value) ? value() : value;

function init(key: string, store: string) {
    const storage = CacheStore(key, config(`cache.stores.${store}`));

    return Object.create({
        forget(key: string) {
            storage.forget(key);
        },
        clear() {
            storage.clearAll();
        },

        get(key: string, defaultValue: any = null) {
            const cache = storage.get(key);

            if (helpers.isNullOrUndefined(cache)) {
                return defaultValue;
            }
            
            else if (! cache.valid()) {
                this.forget(key);

                return defaultValue;
            }

            return cache.value;
        },

        async remember(key: string, seconds: number, defaultValue: any) {
            let value = this.get(key);

            if (value === null) {
                value = await getValue(defaultValue);
                storage.replaceOrPut(key, seconds, value);
            }

            return value;
        }
    });
}

export default init(config('cache.key'), config('cache.default'));