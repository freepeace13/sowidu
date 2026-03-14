<template>
    <v-layout row justify-space-between class="mb-2">
        <v-flex grow>
            <v-text-field
                placeholder="Find person, company or employees..."
                class="xs-3"
                solo
                v-model="search"
                prepend-inner-icon="search"
                hide-details
                clearable
            />
        </v-flex>
        <v-spacer></v-spacer>
        <v-flex shrink align-self-center>
            <FilterGroup
                :filters="filters"
                :value="filter"
                @change="filter = $event"
                class="mb-2"
            />
        </v-flex>
    </v-layout>
</template>

<script>
import { debounce } from 'lodash';
import FilterGroup from '~/components/UI/Buttons/FilterGroup';

const DELAY = 500;

export default {
    props: {
        filters: {
            type: Array,
            default: () => ([])
        },
    },

    components: {
        FilterGroup
    },

    data: () => ({
        search: null,
        filter: []
    }),

    watch: {
        search: debounce(function (value) {
            this.$emit('update:search', value);
        }, DELAY),

        filter: debounce(function (value) {
            this.$emit('update:filter', value);
        }, DELAY)
    }
}
</script>