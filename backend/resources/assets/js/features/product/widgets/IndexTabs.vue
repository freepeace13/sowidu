<template>
    <v-toolbar height="64" card>
        <v-toolbar-items>
            <v-tabs
                height="64"
                slider-color="grey"
                color="transparent"
                active-class="font-weight-bold"
            >
                <v-tab
                    v-for="type in itemTypes"
                    :key="type.id"
                    :to="{ name: 'products', query: { group: type.name } }"
                    exact replace
                >
                    {{ type.name.toUpperCase() }}
                </v-tab>
            </v-tabs>
        </v-toolbar-items>

        <v-spacer></v-spacer>

        <v-btn icon @click="createItem" v-can="allowCreateProduct">
            <v-icon>add</v-icon>
        </v-btn>
    </v-toolbar>
</template>

<script>
import * as ProductEnums from '../enums';
import UsesMiscStore from '~/components/Mixins/UsesMiscStore';
import { showItemModal } from '~/services/events/modal';

export default {
    name: 'IndexTabs',

    mixins: [UsesMiscStore()],

    computed: {
        allowCreateProduct() {
            return ProductEnums.PERMISSIONS.CREATE_PRODUCT;
        }
    },

    methods: {
        createItem() {
            showItemModal();
        },
    }
}
</script>