import FileInputPreviewer from '@components/Fields/File/FileInputPreviewer.vue'

export default {
    components: { FileInputPreviewer },

    data: () => ({
        attachment: {
            preview: null,
            file: null,
            type: null,
        },
    }),

    computed: {
        hasAttachmentOnInput() {
            return this.attachment.preview
        },
    },

    methods: {
        attachingFile(e) {
            const files = e.target.files
            const file = files[0]
            const type = file.type.split('/')[0]
            this.attachment = {
                preview: (window.URL || window.webkitURL).createObjectURL(file),
                type,
                file,
            }
        },

        resetPreview() {
            this.attachment = {
                preview: null,
                files: null,
                type: null,
            }
        },
    },
}
