/** @flow */

import * as types from './constants';
import ItemService from '~/services/ItemService';
import { Item } from '~/services/models';
import Cache from '~/services/cache';

export default {
    namespaced: true,

    state: {
        items: [],
    },

    getters: {
        except(state: Object) {
            return (items: Array<Item> = []) => {
                return state.items.filter((v) => !items.includes(v.id));
            }
        }
    },

    actions: {
        async all({ commit, state }: Object) {
            const result: Array<Item> = await ItemService.all();
            commit(types.SET_ITEMS, result);
            return result;
        }
    },

    mutations: {
        [types.SET_ITEMS] (state: Object, items: Array<Item>) {
            state.items = items
        },

        [types.INSERT_ITEM] (state: Object, item: Item) {
            state.items = Item
                .collection(state.items)
                .insert(item)
                .all();
        },

        [types.ITEM_UPDATE] (state: Object, item: Item) {
            state.items = Item
                .collection(state.items)
                .updateOrInsert(item)
                .all();
        }
    }
}