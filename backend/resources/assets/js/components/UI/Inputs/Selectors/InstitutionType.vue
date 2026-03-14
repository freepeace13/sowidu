<template>
    <div>
        <h4 class="font-weight-bold mb-1 text-uppercase" v-if="label" v-html="label"></h4>
        <v-select
            :items="institutionTypes"
            :value="value"
            item-text="name"
            item-value="id"
            :readonly="readonly"
            :disabled="$attrs.disabled"
            @change="handleChange"
            :error="errors.length > 0"
            :error-messages="errors"
            return-object
            :placeholder="placeholder"
            solo
        ></v-select>
    </div>
</template>

<script>
import { UsesMiscStore } from '~/components/Mixins';

export default {
    name: 'InstitutionTypeSelector',

    mixins: [UsesMiscStore()],

    props: {
        label: {
            type: String,
            default: ''
        },

        readonly: {
            type: Boolean,
            default: false
        },

        placeholder: {
            type: String,
            default: ''
        },

        value: {
            required: true
        },

        errors: {
            type: Array,
            default: () => ([])
        },
    },

    methods: {
        handleChange(value) {
            this.$emit('input', value)
            this.$emit('change', value)
        }
    }
}
</script>

<style lang="scss" scoped>
    /deep/ .v-text-field__details {
        padding: 0px !important;
    }
</style>
