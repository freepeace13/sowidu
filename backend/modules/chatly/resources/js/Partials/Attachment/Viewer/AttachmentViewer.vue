<template>
    <v-dialog
        :value="show"
        hide-overlay
        fullscreen
        content-class="hide-overflow"
        @keydown.esc="show = false"
    >
        <template v-if="viewComponent">
            <component
                :is="viewComponent"
                :key="`view_${attachment.name}`"
            />
        </template>
    </v-dialog>
</template>

<script>
import ImageViewer from './Types/ImageViewer.vue'
import PdfViewer from './Types/PdfViewer.vue'
import VideoPlayer from './Types/VideoPlayer.vue'

export default {
    components: {
        ImageViewer,
        PdfViewer,
        VideoPlayer,
    },

    provide() {
        return {
            attachment: () => this.attachment,
            close: () => (this.show = false),
            download: () => this.download(),
        }
    },

    data: () => ({
        show: false,
        attachment: null,
    }),

    computed: {
        viewComponent() {
            return {
                image: 'ImageViewer',
                video: 'VideoPlayer',
                pdf: 'PdfViewer',
            }[this.type]
        },

        type() {
            return this.attachment?.type
        },
    },

    watch: {
        show(newVal) {
            if (!newVal) this.attachment = null
        },
    },

    methods: {
        view(attachment) {
            this.show = true
            this.attachment = {
                mime_type: attachment.mime_type,
                name: attachment.name,
                original_name: attachment.file_name,
                thumbnail_url:
                    attachment?.conversions?.thumbnail ??
                    attachment.thumbnail_url,
                url: attachment.url,
                media_id: attachment?.id,
                type: attachment?.file_type ?? attachment.type,
            }
        },

        viewMessageAttachment({ data }) {
            this.show = true
            this.attachment = data
        },

        viewMedia(media) {
            this.show = true
            this.attachment = {
                mime_type: media.mime_type,
                name: media.name,
                original_name: media.file_name,
                thumbnail_url: media.conversions.thumbnail,
                url: media.url,
                media_id: media?.id,
                type: media.file_type,
            }
        },

        download() {
            const source = this.attachment.url
            const fileName = source.split('/').pop()
            var el = document.createElement('a')
            el.setAttribute('href', source)
            el.setAttribute('download', fileName)
            document.body.appendChild(el)
            el.click()
            el.remove()
        },
    },
}
</script>
