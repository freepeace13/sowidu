<script setup>
import { useStrBefore } from '@/Composables/useUtils'

defineProps({
    amount: {
        required: false,
        type: String,
        default: '',
    },
    label: {
        required: true,
        type: String,
    },
    color: {
        required: false,
        type: String,
        default: 'purple lighten-4',
    },
    isLoading: {
        required: false,
        type: Boolean,
        default: true,
    },
})
</script>
<template>
    <v-card
        raised
        :color="color"
    >
        <v-card-title
            class="display-1 font-weight-bold teal--text text--darken-4 tw-justify-center"
        >
            <span v-show="!isLoading"> {{ amount }} </span>
            <v-progress-circular
                v-show="isLoading"
                :size="40"
                :width="7"
                :color="useStrBefore(color)"
                indeterminate
            />
        </v-card-title>
        <v-card-actions class="!tw-text-lg tw-text-primary">
            {{ label }}
        </v-card-actions>
    </v-card>
</template>
