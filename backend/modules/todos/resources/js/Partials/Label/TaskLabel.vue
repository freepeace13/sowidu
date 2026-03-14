<template>
    <v-chip
        label
        :color="label.color"
        text-color="white"
        class="w-full ma-0 cursor-pointer task-label hover-grow"
        :selected="selected"
    >
        <span class="name">{{ label.name }}</span>
        <v-icon
            v-show="selected"
            small
            >check_circle</v-icon
        >
    </v-chip>
</template>
<script>
export default {
    props: {
        label: {
            type: Object,
            required: true,
        },

        selected: {
            type: Boolean,
            default: false,
        },
    },
}
</script>
