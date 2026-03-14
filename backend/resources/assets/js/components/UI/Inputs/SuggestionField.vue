<template>
    <div>
        <h4 class="font-weight-bold mb-1 text-uppercase" v-if="label" v-html="label"></h4>

        <v-autocomplete
            solo
            v-model="selected"
            :clearable="selected !== original"
            :search-input.sync="search"
            v-bind="$attrs"
            hide-no-data
            :menu-props="{ closeOnContentClick: true }"
            :error="errors.length > 0"
            :error-messages="errors"
            :items="$options.filters.compact([
                ...this.items, this.value, this.search
            ])"
        />
    </div>
</template>

<script>
import _ from 'lodash';
import { isString, isNullOrUndefined } from '@libs/core/utils/helpers';

export default {
    name: 'SuggestionField',

    filters: {
        compact: (array) => _.compact(array)
    },

    props: {
        label: {
            type: String,
            default: null
        },

        value: {
            required: true,
            default: null
        },

        items: {
            type: Array,
            default: () => ([])
        },

        errors: {
            type: Array,
            default: () => ([])
        }
    },

    data: function() {
        return {
            search: null,
            original: null
        }
    },

    computed: {
        selected: {
            get() {
                return this.value;
            },
            set(value) {
                this.$emit('input', value || this.original || null);
            }
        }
    },

    created() {
        this.$watch('value', _.once((value) => this.original = value));
    }
}
</script>