/** @flow */

import { mapState, mapGetters, mapActions } from 'vuex';
import { createContext } from '~/support/factories';
import { Item } from '~/services/models';
import ItemService from '../api';

export default () => ({
    computed: {
        ...mapState({
            products: (state) => state.product.items
        }),

        ...mapGetters({
            productsExcept: 'product/except'
        })
    },

    methods: {
        ...mapActions({
            getItems: 'product/all'
        })
    },

    created() {
        this.$items = createContext({
            create(instance: Item) {
                return ItemService.create(instance);
            },
            update(instance: Item) {
                return ItemService.update(instance);
            },
        });
    }
});