/** @flow */

import gallery from './modules/gallery';
import prompt from './modules/prompt';
import * as types from './constants';

export default {
    namespaced: true,

    state: {
        layout: 'auth',
        screenLocked: false,
        shortcut: false,
    },

    mutations: {
        [types.SET_LAYOUT] (state: Object, type: ?string) {
            state.layout = (type || 'auth').toLowerCase();
        },

        [types.TOGGLE_SHORTCUT] (state: Object, visiblity?: ?boolean) {
            state.shortcut = (visiblity === undefined)
                ? !state.shortcut
                : visiblity;
        },

        [types.TOGGLE_LOCKSCREEN] (state: Object, value?: ?boolean) {
            state.screenLocked = (value === undefined)
                ? !state.screenLocked
                : value;
        }
    },

    modules: {
        gallery,
        prompt,
    }
}