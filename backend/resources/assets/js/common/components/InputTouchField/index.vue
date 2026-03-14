<template>
    <v-text-field
        :value="value"
        @input="$emit('input', $event)"
        :label="value || $attrs.placeholder"
        solo
        :readonly="!editable"
        :flat="!editable"
        :background-color="editable ? null: 'transparent'"
        single-line
        hide-details
        ref="input"
        @focus="editable = true"
        @blur="editable = false, $emit('save')"
        @click="$refs.input.focus()"
    />
</template>

<script>
export default {
    props: {
        value: {
            type: String,
            default: null
        }
    },

    data: () => ({
        editable: false
    }),
}
</script>