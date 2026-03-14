<template>
    <v-select
        :items="units"
        :label="label"
        v-model="selected"
        return-object
        :error="errors.length > 0"
        :error-messages="errors"
        item-text="name"
        item-value="id"
        solo
    />
</template>

<script>
import { UsesMiscStore } from '~/components/Mixins';

export default {
    name: 'UnitSelector',

    mixins: [UsesMiscStore()],

    props: {
        label: {
            type: String,
            default: 'Choose Unit'
        },

        value: {
            required: true
        },

        errors: {
            type: Array,
            default: () => ([])
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
