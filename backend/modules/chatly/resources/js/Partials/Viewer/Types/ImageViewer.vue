<template>
    <view-container>
        <v-img
            :src="computedAttachment.url"
            :lazy-src="computedAttachment.thumbnail_url"
            :aspect-ratio="16 / 9"
            min-height="100%"
            min-width="100%"
            height="100%"
            width="100%"
            :alt="computedAttachment.name"
            transition="fade-transition"
            contain
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
                        color="grey lighten-5"
                    />
                </v-layout>
            </template>
        </v-img>
    </view-container>
</template>

<script>
import View from '../View.vue'

export default {
    extends: View,
}
</script>
