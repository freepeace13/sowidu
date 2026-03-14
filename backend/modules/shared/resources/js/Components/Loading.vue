<script setup>
defineProps({
    message: {
        type: String,
        default: 'Loading...',
    },
})
</script>

<template>
    <v-card
        flat
        tile
        color="blue-grey darken-2"
        class="white--text"
    >
        <v-card-text>
            <v-layout
                row
                align-center
                justify-center
                style="gap: 8px"
            >
                <v-flex shrink>
                    <v-progress-circular
                        size="20"
                        indeterminate
                    />
                </v-flex>
                <v-flex shrink>
                    {{ message }}
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
</template>
