<template>
    <v-progress-circular
        :size="size"
        :color="color"
        indeterminate
    ></v-progress-circular>
</template>

<script>
export default {
    name: 'Spinner',

    props: {
        size: {
            type: Number,
            default: 50
        },

        color: {
            type: String,
            default: 'primary'
        }
    }
}
</script>