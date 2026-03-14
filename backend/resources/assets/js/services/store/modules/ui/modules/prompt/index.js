/** @flow */

import * as types from './constants';
import { random } from '~/utils/string';

type PromptType = {
    id: string,
    component: Object
}

export default {
    namespaced: true,

    state: {
        prompts: [],
    },

    actions: {
        create({ commit }: Object, component: Object) {
            let entry: PromptType = { id: random(), component };
            commit(types.PREPEND_PROMPT, entry);
            return entry;
        },

        close({ commit, state }: Object, promptId: string) {
            const index = state.prompts.findIndex(i => i.id === promptId);
            if (index !== -1) {
                commit(types.REMOVE_PROMPT, index);
            }
        },

        clear({ commit }: Object) {
            commit(types.CLEAR_PROMPTS);
        },
    },

    mutations: {
        [types.PREPEND_PROMPT]: (state: Object, prompt: PromptType) => {
            state.prompts.unshift(prompt);
        },

        [types.REMOVE_PROMPT]: (state: Object, prompt: PromptType) => {
            state.prompts.splice(prompt, 1);
        },

        [types.CLEAR_PROMPTS]: (state: Object) => {
            state.prompts = [];
        }
    }
}