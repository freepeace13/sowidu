<script setup>
defineProps({
    label: {
        required: true,
        type: String,
    },
    value: {
        required: false,
        type: [String, Number],
        default: null,
    },
})
</script>
<template>
    <v-list-tile-content class="tw-flex tw-flex-row">
        <div class="tw-flex tw-items-start tw-space-x-5 ticket-detail">
            <v-label> {{ label }}: </v-label>
            <div
                v-bind="$attrs"
                class="tw-text-lg tw-text-black"
            >
                {{ value }}
            </div>
        </div>
    </v-list-tile-content>
</template>
<style>
.ticket-detail {
    label {
        @apply tw-self-start tw-mt-1;
    }
}
</style>
