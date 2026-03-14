<script setup>
import { computed } from 'vue'

const props = defineProps({
    toolTip: {
        type: String,
    },
    color: {
        type: String,
        default: 'blue lighten-4',
    },
    textColor: {
        type: String,
    },
    icon: {
        type: String,
        default: 'playlist_add_check',
    },
    title: {
        type: String,
    },
})

const tooltipWithPrefix = computed(() => `Progress: ${props.toolTip}`)
</script>

<template>
    <v-chip
        v-tooltip="tooltipWithPrefix"
        label
        :color="color"
        :text-color="textColor"
        class="order-status-chip"
    >
        <v-icon
            :color="textColor"
            class="mr-1"
        >
            {{ icon }}
        </v-icon>
        <span class="tw-py-2 tw-truncate">
            {{ title }}
        </span>
    </v-chip>
</template>
