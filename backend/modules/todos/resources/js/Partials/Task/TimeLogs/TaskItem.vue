<template>
    <div
        class="primary--text font-weight-bold tw-flex tw-items-center tw-gap-x-2"
    >
        <v-icon
            color="primary"
            style="vertical-align: middle"
        >
            check_box
        </v-icon>
        {{ title }}
    </div>
</template>
<script>
export default {
    props: {
        title: {
            type: String,
            default: null,
            required: false,
        },
    },
}
</script>
