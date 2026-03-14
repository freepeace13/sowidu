<template>
    <div>
        <h4 class="font-weight-bold mb-1 text-uppercase" v-if="label" v-html="label"></h4>
        <v-select
            :items="legalForms"
            :value="value"
            item-text="name"
            item-value="id"
            @change="handleChange"
            :disabled="$attrs.disabled"
            :error="errors.length > 0"
            :readonly="readonly"
            return-object
            :error-messages="errors"
            :placeholder="placeholder"
            solo
        ></v-select>
    </div>
</template>

<script>
import { UsesMiscStore } from '~/components/Mixins';

export default {
    name: 'LegalFormSelector',

    mixins: [UsesMiscStore()],

    props: {
        readonly: {
            type: Boolean,
            default: false
        },

        label: {
            type: String,
            default: ''
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
