<template>
    <v-layout row wrap justify-space-between align-center class="mb-2">
        <v-flex xl3 lg3 sm4 xs12>
            <v-text-field
                placeholder="Search keywords..."
                class="xs-3"
                solo
                v-model="search"
                prepend-inner-icon="search"
                hide-details
                clearable
            />
        </v-flex>
        <v-flex xl2 lg3 sm4 xs12>
            <ItemTypeFilter v-model="filter" />
        </v-flex>
    </v-layout>
</template>

<script>
import ItemTypeFilter from './ItemTypeFilter';
import { debounce } from 'lodash';

const DELAY = 500;

export default {
    components: {
        ItemTypeFilter
    },

    data: () => ({
        search: null,
        filter: null
    }),

    watch: {
        search: debounce(function (value) {
            this.$emit('search', value);
        }, DELAY),

        filter: debounce(function (value) {
            this.$emit('filter', value);
        }, DELAY)
    }
}
</script>