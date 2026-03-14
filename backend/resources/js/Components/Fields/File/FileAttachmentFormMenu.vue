<template>
    <div>
        <v-menu
            v-model="isShow"
            :position-x="x"
            :position-y="y"
            absolute
            :close-on-content-click="true"
        >
            <v-card class="tw-z-10">
                <v-list
                    subheader
                    dense
                >
                    <v-subheader class="text-right">
                        Add file from...
                    </v-subheader>
                    <v-divider />
                    <v-list-tile @click="$refs.uploadFile.browseFromComputer()">
                        <v-list-tile-action>
                            <v-icon>computer</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>
                                Your computer
                            </v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                    <v-list-tile
                        v-if="isMobileBrowser"
                        @click="$refs.uploadFile.captureFromCamera()"
                    >
                        <v-list-tile-action>
                            <v-icon>photo_camera</v-icon>
                        </v-list-tile-action>

                        <v-list-tile-content>
                            <v-list-tile-title>
                                {{ $t('Capture Photo') }}
                            </v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>

                    <v-list-tile @click="$emit('attach:from-media')">
                        <v-list-tile-action>
                            <v-icon>perm_media</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>Your media</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </v-list>
            </v-card>

            <UploadFileButton
                ref="uploadFile"
                :mimes="allowedTypes"
                @upload="filesSelected"
            />
        </v-menu>
        <FileUploader
            ref="fileUploader"
            :timeout="1000"
            @success="uploadFileCompleted"
            @error="(payload) => uploadError(payload)"
        />
    </div>
</template>
<script>
import IsDynamicMenu from '@/Mixins/IsDynamicMenu'
import UploadFileButton from '@/Features/Media/Components/UploadFileButton.vue'
import FileUploader from '@/Components/FileUploader/FileUploader.vue'
import usePlatform from '@/Apps/Shared/Composables/usePlatform'
import useMediaDevice from '@/Features/Media/Composables/useMediaDevice'

export default {
    components: {
        UploadFileButton,
        FileUploader,
    },
    mixins: [IsDynamicMenu],

    props: {
        allowedTypes: {
            type: Array,
            default: () => ['image/*', '.pdf', 'video/*'],
        },
    },

    data: () => ({
        isMobileBrowser: false,
        hasVideoInputDevice: false,
    }),

    mounted() {
        const devices = useMediaDevice()
        const platform = usePlatform()

        this.isMobileBrowser = platform === 'mobile'
        this.hasVideoInputDevice = devices.value.filter(
            (i) => i.kind === 'videoinput',
        )
    },

    methods: {
        uploadFile({ files, source }) {
            const target = this.$route('media.files.upload')

            this.$refs.fileUploader.upload(target, files, {
                query: {
                    source,
                    folder: null,
                },
            })
        },

        filesSelected({ files, source }) {
            this.uploadFile({ files, source })
        },

        emitSelectedFiles(payload) {
            this.$emit('input-file:changed', payload)
        },

        uploadFileCompleted(payload) {
            const { file } = JSON.parse(payload)
            this.$emit('attach:from-file', file)
        },

        uploadError(payload) {
            try {
                const { errors } = JSON.parse(payload)
                Object.values(errors).forEach((bag) => {
                    bag.forEach((err) => this.$root.$emit('flash.error', err))
                })
            } catch (error) {
                console.error(error)
            }
        },
    },
}
</script>
