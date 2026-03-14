<template>
    <v-select
        :items="itemTypes"
        :label="label"
        v-bind="$attrs"
        v-model="selected"
        return-object
        :error="errors.length > 0"
        :error-messages="errors"
        :readonly="readonly"
        item-text="name"
        item-value="id"
        solo
    />
</template>

<script>
import { UsesMiscStore } from '~/components/Mixins';

export default {
    name: 'ItemTypeSelector',

    mixins: [UsesMiscStore()],

    props: {
        label: {
            type: String,
            default: 'Choose Item Type'
        },

        value: {
            required: true
        },

        errors: {
            type: Array,
            default: () => ([])
        },

        readonly: {
            type: Boolean,
            default: false
        },
    },

    computed: {
        selected: {
            get() {
                return this.value;
            },
            set(value) {
                this.$emit('input', value);
                this.$emit('change', value);
            }
        }
    }
}
</script>
