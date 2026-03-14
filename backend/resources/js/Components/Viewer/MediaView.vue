<template>
    <v-dialog
        v-model="show"
        lazy
        hide-overlay
        fullscreen
        content-class="hide-overflow"
    >
        <template v-if="viewComponent">
            <component
                :is="viewComponent"
                :key="`view_${media.uuid}`"
            />
        </template>
    </v-dialog>
</template>

<script>
import axios from 'axios'
const ImageView = () =>
    import(/* webpackChunkName: 'image-viewer' */ './ImageView.vue')
const VideoPlayer = () =>
    import(/* webpackChunkName: 'video-player' */ './VideoPlayer.vue')
const PDFView = () =>
    import(/* webpackChunkName: 'pdf-viewer' */ './PDFView.vue')

export default {
    components: {
        ImageView,
        PDFView,
        VideoPlayer,
    },

    provide() {
        return {
            reset: (media) => (this.media = media),
            media: () => this.computedMedia,
            close: () => this.close(),
            download: () => this.download(),
            share: () => console.log('Sharing...'),
        }
    },

    data: () => ({
        show: false,
        media: {
            url: null,
            uuid: null,
            name: null,
            file_name: null,
            file_type: null,
            modified: null,
            folder: {
                name: null,
            },
            owner: {},
            members: [],
            policies: {
                can_share: false,
                can_download: false,
                can_move: false,
                can_rename: false,
            },
        },
    }),

    computed: {
        computedMedia() {
            return this.media
        },

        viewComponent() {
            return {
                image: 'ImageView',
                video: 'VideoPlayer',
                pdf: 'PDFView',
            }[this.media.file_type]
        },
    },

    methods: {
        view(uuid) {
            this.loading = true

            this.$nextTick(async () => {
                try {
                    const {
                        data: { file },
                    } = await axios.get(
                        this.$route('media.show', { media: uuid }),
                    )
                    this.media = file
                    this.show = true
                } catch (error) {
                    this.$root.$emit('flash.error', error)
                } finally {
                    this.loading = false
                    this.show = true
                }
            })
        },

        download() {
            location.href = this.$route('media.files.download', {
                media: this.media.uuid,
            })
        },

        close() {
            this.show = false
            this.$root.$emit('media.viewer.close')
        },
    },
}
</script>

<style lang="scss" scoped>
.viewer-container {
    background: black;
    overflow: hidden;
    height: 100%;
    width: 100%;
}
</style>
