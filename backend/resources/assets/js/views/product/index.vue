<template>
    <RootView title="Products &amp; Equipments" icon="event_note">
        <SearchSortToolbar
            @search="onSearch"
            @filter="onFilter"
        />

        <v-layout row wrap>
            <v-flex xl3 md4 sm6 xs12 v-for="item in products" :key="item.id">
                <ItemCard
                    :item="item"
                    @click:title="viewProduct(item.id)"
                    @click:photo="viewProduct(item.id)"
                    @menu:edit="viewProduct(item.id)"
                />
            </v-flex>
        </v-layout>

        <v-speed-dial
            :fixed="true"
            direction="left"
            :right="true"
            :bottom="true"
            transition="scale"
        >
            <v-btn color="primary" slot="activator" dark fab @click="createItem">
                <v-icon>add</v-icon>
            </v-btn>
        </v-speed-dial>
    </RootView>
</template>

<script>
import { showItemModal } from '~/services/events/modal';
import SearchSortToolbar from '~/components/toolbars/product/SearchSortToolbar';
import ItemCard from '~/components/UI/Cards/ItemCard';
import { UsesItemStore } from '~/components/Mixins';

export default {
    name: 'ProductsList',

    mixins: [UsesItemStore()],

    components: {
        SearchSortToolbar,
        ItemCard,
    },

    methods: {
        createItem() {
            showItemModal();
        },

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
                            itemId: to.params.id,
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
