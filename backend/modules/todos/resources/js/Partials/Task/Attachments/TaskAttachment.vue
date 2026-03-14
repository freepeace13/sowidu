<template>
    <v-card
        hover
        flat
        tile
        color="grey lighten-4"
    >
        <v-img
            :src="attachment.properties.thumbnail_url"
            :lazy-src="attachment.properties.thumbnail_url"
            class="tw-w-full"
            height="80"
            @click="$emit('view', attachment)"
        >
            <template #placeholder>
                <v-layout
                    fill-height
                    align-center
                    justify-center
                    ma-0
                >
                    <v-progress-circular
                        indeterminate
                        color="grey darken-3"
                    />
                </v-layout>
            </template>
        </v-img>
        <v-card-actions :class="{ 'pa-0': attachment.is_mine }">
            <div
                class="caption primary--text text-truncate ml-1"
                v-text="attachment.properties.original_name"
            />
            <v-spacer />
            <v-btn
                v-if="attachment.is_mine"
                icon
                @click="$emit('click:option', { e: $event, attachment })"
            >
                <v-icon small>more_horiz</v-icon>
            </v-btn>
        </v-card-actions>
    </v-card>
</template>
<script>
export default {
    props: {
        attachment: {
            required: false,
            type: Object,
            default: () => ({}),
        },
    },
}
</script>
