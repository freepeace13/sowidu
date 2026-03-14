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
            sources: {
                type: Array,
                default: () => ([]),
                validator(value) {
                    return value.every((item) => {
                        return item.src !== undefined && item.type !== undefined;
                    });
                }
            },
        },

        data: () => ({
            player: null
        }),

        methods: {
            onReady() {
                console.log('ready', this.player)
            }
        },

        mounted() {
            this.player = VideoJS(this.$refs.player, {
                    autoplay: false,
                    controls: true,
                    sources: this.sources
            }, this.onReady);
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
