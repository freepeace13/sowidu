<template>
    <view-container>
        <v-responsive
            :aspect-ratio="1"
            width="100%"
            height="100%"
        >
            <video
                ref="videoPlayer"
                class="video-js vjs-default-skin vjs-big-play-centered"
                preload="auto"
            />
        </v-responsive>
    </view-container>
</template>

<script>
import View from '../View.vue'
import videojs from 'video.js'

export default {
    extends: View,

    data: () => ({
        player: null,
    }),

    mounted() {
        let mimeType = this.computedAttachment.mime_type.replace('-', '/')

        if (mimeType === 'video/quicktime') {
            mimeType = 'video/mp4'
        }

        this.player = videojs(this.$refs.videoPlayer, {
            autoplay: true,
            controls: true,
            resizeManager: false,
            sources: [
                {
                    src: this.computedAttachment.url,
                    type: mimeType,
                },
            ],
        })
    },

    beforeDestroy() {
        if (this.player) {
            this.player.dispose()
        }
    },
}
</script>

<style lang="scss" scoped>
.video-js {
    position: relative !important;
    width: 100%;
    height: 100%;
}
</style>
