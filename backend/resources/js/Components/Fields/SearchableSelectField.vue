<template functional>
    <v-autocomplete
        v-bind="data.attrs"
        :value="props.value"
        :items="props.items"
        :disabled="isDisabled"
        @change="props.onSelect"
    />
</template>

<script>
export default {
    functional: true,

    props: {
        items: {
            type: Array,
            required: true,
        },

        value: {
            type: String,
            default: null,
        },

        onSelect: {
            type: Function,
            required: true,
        },

        isDisabled: {
            type: Boolean,
            default: false,
        },
    },
}
</script>
