<template>
    <v-dialog
        v-model="uploading"
        content-class="resumable--dialog"
        persistent
        max-width="500"
    >
        <v-card color="primary">
            <v-card-text>
                Uploading file(s) ...
                <v-progress-linear :value="progress" color="white"></v-progress-linear>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
    import { postdata } from '~/utils/request'
    import Resumable from 'resumablejs'
    import { AUTH_GUARDS } from '~/support/constants';
    import { UsesAuthStore } from '~/components/Mixins';

    export default {
        mixins: [UsesAuthStore()],

        data: () => ({
            uploading: false,
            progress: 0,
        }),

        methods: {
            startUpload(files) {
                this.plugin.addFiles(files)
            },

            open() {
                this.uploading = true
            },

            close() {
                this.uploading = false
            },

            onProgress(file) {
                this.progress = Math.floor(this.plugin.progress() * 100)
            },

            onFileAdded(file) {
                this.plugin.upload()
            },

            getRequestHeaders() {
                const headers = {};

                if (this.token()) {
                    headers['Authorization'] = `Bearer ${this.token()}`;

                    if (this.check(AUTH_GUARDS.COMPANY)) {
                        const userProfile = this.profile(AUTH_GUARDS.USER);
                        headers['X-Primary-Id'] = userProfile.id;
                    }
                }

                return headers
            }
        },

        created() {
            this.$uploader.listen('upload', (options) => {
                const { onFileSuccess, method } = options

                const r = new Resumable({
                    target: options.url,
                    // chunkSize: 300000,
                    testChunks: false,
                    forceChunkSize: true,
                    // simultaneousUploads: 4,
                    headers: this.getRequestHeaders(),
                    permanentErrors: [400, 404, 409, 415, 500, 501, 422],
                    setChunkTypeFromFile: true,
                    uploadMethod: method ? method : 'POST',
                    maxFiles: 4,
                })

                r.on('uploadStart', this.open)
                r.on('fileProgress', this.onProgress)

                r.on('complete', options.onCompleted)
                r.on('fileSuccess', onFileSuccess ? onFileSuccess : () => {})
                r.on('fileError', options.onError)

                r.on('fileAdded', this.onFileAdded)

                this.plugin = r

                this.startUpload(options.files)
            })

            this.$uploader.listen('close', this.close)
        }
    }
</script>

<style lang="scss" scoped>
    .resumable--dialog {
        box-shadow: none;
    }
</style>
