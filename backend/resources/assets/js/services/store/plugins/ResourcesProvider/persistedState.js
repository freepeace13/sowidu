/** @flow */

import Vuex from 'vuex';
import config from '~/config';
import { resolveFromRaw } from '~/services/models';
import createPersistedState from 'vuex-persistedstate';
import matcher from 'matcher';

import * as utils from '~/support/helpers';

type Options = {
    key: string,
    paths: Array<string>,
    storage: string,
    filters: Array<string>,
    restore: Function,
}

const resolve = (state: any) => {
    if (utils.isArray(state)) {
        return state.map(resolve);
    }

    if (utils.isObject(state)) {
        return (typeof state.alias !== 'undefined')
            ? resolveFromRaw(state)
            : state;
    }

    return state;
}

export default (
    options: Options
): Function => (store: Vuex) => {
    options.storage = config(`vuex.persistence.storage.${options.storage}`);
    options.paths = utils.arrwrap(options.paths);

    if (! utils.isFunction(options.restore)) {
        options.restore = resolve;
    }

    return createPersistedState({
        key: options.key,
        paths: options.paths,
        storage: options.storage,
        filter: (mutation) => options.filters.some((v) => {
            return matcher.isMatch(mutation.type, v, { caseSensitive: true });
        }),
        getState: (key, storage) => {
            let value = storage.getItem(key);

            const clonedState = utils.cloneDeep(store.state);

            if (utils.isJson(value)) {
                value = JSON.parse(value);

                options.paths.forEach((path) => {
                    utils.set(clonedState, path, options.restore(
                        utils.get(value, path)
                    ));
                });

                store.replaceState(clonedState);
            }
            
            return undefined;
        }
    })(store);
}