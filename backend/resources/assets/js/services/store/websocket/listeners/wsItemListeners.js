/** @flow */

import Vuex from 'vuex';
import Item from '~/services/models/item';

export default (store: Vuex) => ({
    ProductUpdate: (event: any) => {
        store.commit('product/ITEM_UPDATE', Item.create(event));
    },

    InsertItem: (event: any) => {
        store.commit('product/INSERT_ITEM', Item.create(event));
    },
});