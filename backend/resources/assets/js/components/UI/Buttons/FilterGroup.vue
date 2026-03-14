<template>
    <v-layout row class="btn-filter-group">
        <v-flex shrink align-self-center d-flex align-items-center>
            <v-icon class="mr-2">filter_list</v-icon> Filters
        </v-flex>
        <v-flex>
            <v-btn-toggle
                :value="value"
                class="transparent"
                @change="$emit('change', $event)"
                block
            >
                <v-btn
                    class="mx-2"
                    v-for="filter in filters"
                    :key="filter.key"
                    :value="filter.key"
                >
                    {{ filter.text }}
                </v-btn>
            </v-btn-toggle>
        </v-flex>
    </v-layout>
</template>

<script>
export default {
    props: {
        filters: {
            type: Array,
            required: true
        },

        value: {
            required: true,
            validator(prop) {
                return typeof prop === typeof []
                    || typeof prop === typeof ""
                    || prop === undefined;
            }
        }
    }
}
</script>