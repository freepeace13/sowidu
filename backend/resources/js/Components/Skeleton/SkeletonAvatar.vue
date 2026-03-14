<template>
    <v-avatar v-bind="$attrs">
        <v-icon light :size="size">{{ icon }}</v-icon>
    </v-avatar>
</template>
<script>
export default {
    props: {
        size: {
            type: [Number, String],
            default: 40,
        },
        icon: {
            required: false,
            type: String,
            default: 'account_box',
        },
    },
}
</script>
