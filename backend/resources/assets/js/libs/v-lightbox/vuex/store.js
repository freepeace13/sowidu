import { mutations, actions } from './constants';
import { helpers } from '@libs/core';
import LightboxItem from '../models/item';
import Media from '~/services/models/media';

export default {
    namespaced: true,

    state: {
        items: [],
        editing: false,
        uploading: false,
        settings: {
            editable: true
        }
    },

    getters: {
        current(state) {
            return state.items.findIndex((v) => v.current === true);
        },

        visible(state, getters) {
            return getters['current'] !== -1;
        },
    },

    actions: {
        [actions.UPLOAD] (context, { client, url, payload }) {
            return new Promise((resolve, reject) => {

                context.commit(mutations.UPLOADING, true);

                client.upload.call(client, url, payload)((response) => {
                    context.commit(mutations.UPLOADING, false);
                    resolve(response);
                }, (errors) => {
                    context.commit(mutations.UPLOADING, false);
                    reject(errors);
                });

            });
        }
    },

    mutations: {
        [mutations.UPDATE_ITEM_URL] (state, payload) {
            const itemIndex = state.items.findIndex((v) => v.identifier === payload.id);

            if (itemIndex !== -1) {
                const item = state.items[itemIndex];

                state.items.splice(itemIndex, 1, item.set({ url: payload.url }));
            }
        },

        [mutations.SET_ITEMS] (state, items) {
            items = helpers.arrwrap(items);

            if (items.every((v) => v instanceof Media)) {
                state.items = items.map((v) => new LightboxItem(v));
            }
        },

        [mutations.SET_CURRENT] (state, payload) {
            const cloneItems = [...state.items];
            const previousIndex = cloneItems.findIndex((v) => v.current === true);

            payload = !helpers.isNumber(payload)
                ? cloneItems.findIndex((v) => v.is(payload))
                : payload;

            if (cloneItems[payload] instanceof LightboxItem) {
                cloneItems.splice(payload, 1, cloneItems[payload].turnOn());
            }
            
            if (cloneItems[previousIndex] instanceof LightboxItem) {
                cloneItems.splice(previousIndex, 1, cloneItems[previousIndex].turnOff());
            }

            state.items = cloneItems;
        },

        [mutations.CLEAR_ITEMS] (state) {
            state.items = [];
            state.locked = false;
            state.saving = false;
        },

        [mutations.EDITING] (state, payload) {
            state.editing = payload;
        },

        [mutations.UPLOADING] (state, payload) {
            state.uploading = payload;
        },

        [mutations.APPLY_SETTINGS] (state, payload) {
            state.settings = Object.assign(state.settings, payload);
            console.log(state.settings)
        }
    }
}