<template>
    <v-layout fill-height>
        <v-flex xs12>
            <video
                ref="player"
                class="video-js vjs-default-skin vjs-big-play-centered"
                preload="auto"
            >
            </video>
        </v-flex>
    </v-layout>
</template>

<script>
    import VideoJS from 'video.js'

    export default {
        props: {
            media: {
                type: Object,
                default: () => ({})
            },

            thumbnail: {
                type: Boolean,
                default: false
            }
        },

        data: () => ({
            player: null
        }),

        computed: {
            options() {
                const { url, mimetype } = this.media

                return {
                    autoplay: false,
                    controls: !this.thumbnail,
                    sources: [
                        { src: url, type: mimetype }
                    ]
                }
            }
        },

        methods: {
            onReady() {
                console.log('ready', this.player)
            }
        },

        mounted() {
            this.player = VideoJS(this.$refs.player, this.options, this.onReady)
        },

        destroyed() {
            if (this.player) {
                this.player.dispose()
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import '~video.js/dist/video-js.css';

    .video-js {
        position: relative !important;
        width: 100% !important;
        height: 100% !important;
        min-width: 200px;
    }
</style>
