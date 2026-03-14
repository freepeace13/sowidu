<template>
    <v-select
        :label="label"
        :items="specializations"
        v-model="selected"
        clearable
        return-object
        item-text="name"
        item-value="id"
        :readonly="readonly"
        :disabled="$attrs.disabled"
        :error="errors.length > 0"
        :error-messages="errors"
        :placeholder="placeholder"
        solo
    ></v-select>
</template>

<script>
import { UsesMiscStore } from '~/components/Mixins';

export default {
    name: 'SpecializationSelector',

    mixins: [UsesMiscStore()],

    props: {
        readonly: {
            type: Boolean,
            default: false
        },

        label: {
            type: String,
            default: 'Choose Specialization'
        },

        placeholder: {
            type: String,
            default: 'Choose Specialization'
        },

        value: {
            type: Object,
            required: true,
            validator(prop) {
                return typeof(prop.id) === 'number' || prop.id === null;
            }
        },

        errors: {
            type: Array,
            default: () => ([])
        },
    },

    computed: {
        selected: {
            set(value) {
                this.$emit('input', value);
                this.$emit('change', value);
            },
            get() {
                return this.value;
            }
        }
    }
}
</script>
