<template>
    <v-autocomplete
        :items="items"
        :loading="isLoading"
        :search-input.sync="search"
        @change="selectCustomer"
        chips
        clearable
        flat
        hide-details
        item-text="name"
        return-object
        label="Search for a customer..."
        solo
    >
        <template v-slot:no-data>
            <v-list-tile>
                <v-list-tile-title>
                    Search for your <strong>customer</strong>
                </v-list-tile-title>
            </v-list-tile>
        </template>

        <template v-slot:selection="{ item, selected }">
            <v-chip
                :selected="selected"
                color="blue-grey"
                class="white--text"
            >
                <v-icon left>mdi-coin</v-icon>
                <span v-text="item.name"></span>
            </v-chip>
        </template>

        <template v-slot:item="{ item }">
            <v-list-tile-avatar>
                <v-avatar flat size="40">
                    <v-img :src="item.photo" :alt="item.name"/>
                </v-avatar>
            </v-list-tile-avatar>
            <v-list-tile-content>
                <v-list-tile-title v-text="item.name"></v-list-tile-title>
                <v-list-tile-sub-title v-text="item.organizationName"></v-list-tile-sub-title>
            </v-list-tile-content>
        </template>
    </v-autocomplete>
</template>

<script>
import { resolveFromRaw } from '~/services/models';
import Customer from '@common/models/Customer';

export default {
    name: 'CustomerSelector',

    data: () => ({
        isLoading: false,
        items: [],
        search: null
    }),

    methods: {
        selectCustomer(value) {
            resolveFromRaw(value.client);

            this.$emit('input', resolveFromRaw(value.client));
            this.$emit('change', resolveFromRaw(value.client));
        },

        fetchCustomers() {
            Customer.lists()
                .then((items) => {
                    this.items = items;
                })
                .catch((error) => {
                    console.error('[Customer Selector] Fetching customers error: ', error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    },

    created() {
        this.fetchCustomers();
    }
}
</script>
