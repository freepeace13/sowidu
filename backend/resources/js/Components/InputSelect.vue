<template>
    <v-select
        v-bind="$attrs"
        :value="value"
        color="blue-grey darken-2"
        placeholder=" "
        outline
        v-on="inputListeners"
    >
        <template
            v-if="label"
            #label
        >
            <input-label
                :label="label"
                :required="$attrs.required"
            />
        </template>
    </v-select>
</template>

<script>
import InputLabel from './InputLabel.vue'

export default {
    components: { InputLabel },
    inheritAttrs: false,

    props: ['label', 'value'],

    computed: {
        inputListeners() {
            const vm = this

            return Object.assign({}, this.$listeners, {
                change(event) {
                    vm.$emit('change', event)
                },
            })
        },
    },
}
</script>
