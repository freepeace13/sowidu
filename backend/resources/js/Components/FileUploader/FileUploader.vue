<template>
    <upload-progress-details
        :show="show"
        :files="files"
        :expand.sync="expand"
        @retry="retry"
        @close="close"
        @complete="$emit('complete')"
    />
</template>

<script>
import Resumable from 'resumablejs'
import UploadProgressDetails from './UploadProgressDetails.vue'
import { getIndex, createNativeFileFromResumableFile } from '@/utils'

export default {
    components: {
        UploadProgressDetails,
    },
    props: {
        maxFiles: {
            type: Number,
            default: 100, // TODO: Not advisable
        },

        timeout: {
            type: Number,
            required: false,
            default: 0,
        },
    },

    data: () => ({
        silent: false,
        files: [],
        expand: true,
        show: false,
    }),

    created() {
        //
        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]',
        ).content

        this.___instance = new Resumable({
            chunkSize: 1 * 1024 * 1024,
            testChunks: false,
            throttleProgressCallbacks: 1,
            simultaneousUploads: 1,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            maxChunkRetries: 3,
            chunkRetryInterval: 300,
            permanentErrors: [400, 404, 409, 415, 500, 501, 422],
            maxFiles: this.maxFiles,
            maxFilesErrorCallback: () => {
                this.show = false
                this.$root.$emit(
                    'flash.error',
                    `Please ${this.maxFiles} file(s) at a time.`,
                )
            },
        })

        if (!this.___instance.support) {
            throw new Error(
                'Your browser, unfortunately, is not supported by Resumable.js',
            )
        }
    },

    mounted() {
        const r = this.___instance

        r.on('complete', () => {
            r.files.forEach((f) => r.removeFile(f))
            this.$emit('complete')
        })

        r.on('filesAdded', (files) => this.replicateFiles(files))

        r.on('fileProgress', (f) => this.synchronizeFile(f))

        r.on('fileError', (f, errors) => {
            this.synchronizeFile(f, (f) => ({ ...f, isFail: true, errors }))
        })

        r.on('error', (message, file) => {
            const fileName = r.getFromUniqueIdentifier(
                file.uniqueIdentifier,
            )?.fileName
            this.$root.$emit(
                'flash.error',
                `${fileName} ${JSON.stringify(message)}`,
            )
        })

        r.on('fileSuccess', (f, payload) => {
            this.$emit('success', payload) // Emit response
            if (this.timeout != 0) {
                setTimeout(() => {
                    this.show = false
                }, this.timeout)
            }

            this.synchronizeFile(f, (f) => ({ ...f, isSuccess: true }))
        })
    },

    methods: {
        upload(target, files, options = {}) {
            const r = this.___instance
            this.show = true
            this.expand = true
            this.silent = { silent: false, ...options }.silent

            r.opts.target = target
            r.opts.query = { query: {}, ...options }.query
            r.opts.headers = { ...r.opts.headers, ...options.headers }

            if (this.files.every((f) => !f.isUploading)) {
                r.addFiles(files)

                this.$nextTick(() => {
                    setTimeout(() => r.upload(), 200)
                })
            }
        },

        retry(file) {
            const r = this.___instance

            if (this.files.every((f) => !f.isUploading)) {
                r.removeFile(r.getFromUniqueIdentifier(file.uniqueIdentifier))
                r.addFiles([file.file])

                this.$nextTick(() => {
                    setTimeout(() => r.upload(), 200)
                })
            }
        },

        close(event) {
            const r = this.___instance

            if (event.cancel) {
                r.cancel()
            }

            this.expand = true
            this.silent = false
            this.files = []

            r.files.forEach((f) => r.removeFile(f))

            this.show = false
        },

        replicateFiles(files) {
            files.reverse().forEach((file) => {
                let offset = getIndex(this.files, file)
                let nativeFile = createNativeFileFromResumableFile(file)

                if (offset === -1) {
                    this.files = [nativeFile, ...this.files]
                } else {
                    this.files.splice(offset, 1, nativeFile)
                }
            })
        },

        synchronizeFile(file, beforePersistCallback = null) {
            const r = this.___instance
            let offset = getIndex(this.files, file)
            let resumableFile = r.getFromUniqueIdentifier(file.uniqueIdentifier)

            let nativeFile = {
                ...this.files[offset],
                ...createNativeFileFromResumableFile(resumableFile),
            }

            if (typeof beforePersistCallback === 'function') {
                nativeFile = beforePersistCallback(nativeFile)
            }

            this.files.splice(offset, 1, nativeFile)

            return nativeFile
        },
    },
}
</script>
