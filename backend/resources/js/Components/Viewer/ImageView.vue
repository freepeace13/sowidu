<template>
    <view-container>
        <v-img
            :src="computedMedia.url"
            :lazy-src="computedMedia.url"
            :srcset="computedMedia.responsive_images.srcset"
            :aspect-ratio="16 / 9"
            height="100%"
            width="100%"
            contain
        />
    </view-container>
</template>

<script>
import View from './View.vue'

export default {
    extends: View,
}
</script>
