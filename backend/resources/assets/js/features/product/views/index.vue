<template>
    <v-layout row wrap fill-height>
        <v-flex xl3 md4 sm6 xs12 v-for="item in products" :key="item.id">
            <ItemCard
                :item="item"
                @click:title="viewProduct(item.id)"
                @click:photo="viewProduct(item.id)"
                @menu:edit="viewProduct(item.id)"
            />
        </v-flex>
    </v-layout>
</template>

<script>
import { showItemModal } from '~/services/events/modal';
import SearchSortToolbar from '~/components/toolbars/product/SearchSortToolbar';
import ItemCard from '~/components/UI/Cards/ItemCard';
import UsesItemStore from '../mixins/UsesItemStore';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';

export default {
    name: 'IndexView',

    mixins: [
        UsesItemStore(),
        DispatchWhenTokenChanges('product/all')
    ],

    components: {
        SearchSortToolbar,
        ItemCard,
    },

    methods: {
        viewProduct(itemId) {
            this.$router.push({
                name: 'products',
                params: { id: itemId }
            }).catch(() => {
                this.$router.push({ name: 'products' });
            });
        },

        onFilter() {
            //
        },

        onSearch() {
            //
        }
    },

    watch: {
        $route: {
            immediate: true,
            handler(to) {
                this.$nextTick(() => {
                    if (typeof to.params.id !== 'undefined') {
                        showItemModal({
                            itemId: parseInt(to.params.id),
                            afterClose: () => {
                                this.$router.push({ name: 'products' });
                            }
                        });
                    }
                });
            }
        }
    }
}
</script>
