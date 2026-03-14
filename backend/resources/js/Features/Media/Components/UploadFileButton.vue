<script>
import usePlatform from '@/Apps/Shared/Composables/usePlatform'
import { renameNativeFile, sanitizeFileName } from '@/Utils/File'
import { nextTick, ref, shallowReactive, toRefs } from 'vue'
import useMediaDevice from '../Composables/useMediaDevice'
import FileUploadMenu from './FileUploadMenu.vue'

export default {
    name: 'UploadFileButton',

    components: {
        FileUploadMenu,
    },

    props: {
        mimes: {
            type: Array,
            required: true,
        },
    },

    setup(props, context) {
        const input = ref()
        const menu = ref()

        const { mimes } = toRefs(props)

        const source = ref('filesystem')

        const inputProps = shallowReactive({
            accept: mimes.value,
            multiple: true,
            capture: false,
        })

        const devices = useMediaDevice()
        const platform = usePlatform()

        const isMobileBrowser = () => platform === 'mobile'
        const hasVideoInputDevice = () =>
            devices.filter((i) => i.kind === 'videoinput')

        function browseFromComputer() {
            source.value = 'filesystem'

            // inputProps.accept = mimes.value
            // inputProps.multiple = true
            // inputProps.capture = false

            nextTick(() => input.value.click())
        }

        function captureFromCamera() {
            source.value = 'camera'

            // inputProps.accept = mimes.value.filter(
            //     (i) => i.includes('image') || i.includes('video'),
            // )
            // inputProps.multiple = false
            // inputProps.capture = 'environment'

            nextTick(() => input.value.click())
        }

        function chooseFilesToUpload() {
            if (isMobileBrowser && hasVideoInputDevice) {
                menu.value.isOpen = true
            } else {
                browseFromComputer()
            }
        }

        function onInputFilesChanged(files) {
            const targetFiles = Array.from(new Set(files))

            const uploadFiles = targetFiles.map((file) => {
                const lastModified = new Date(file.lastModified).toISOString()

                return renameNativeFile(
                    file,
                    `${lastModified}_${sanitizeFileName(file.name)}`,
                )
            })

            context.emit('upload', {
                files: uploadFiles,
                source: source.value,
            })
        }

        return {
            menu,
            input,
            source,
            inputProps,
            isMobileBrowser,
            hasVideoInputDevice,
            browseFromComputer,
            captureFromCamera,
            chooseFilesToUpload,
            onInputFilesChanged,
        }
    },

    // methods: {
    //     onBrowse() {
    //         this.multiple = true
    //         this.capture = false

    //         this.$nextTick(() => this.$refs.input.click())
    //     },

    //     onCamera() {
    //         navigator.mediaDevices
    //             .enumerateDevices()
    //             .then((devices) => {
    //                 if (devices.find((i) => i.kind === 'videoinput')) {
    //                     this.multiple = false
    //                     this.capture = true

    //                     this.$nextTick(() => this.$refs.input.click())
    //                 } else {
    //                     this.onBrowse()
    //                 }
    //             })
    //             .catch((err) => {
    //                 console.error(`${err.name}: ${err.message}`)
    //             })
    //     },

    //     onUpload(files) {
    //         const source = this.capture ? 'camera' : 'filesystem'
    //         this.$emit('select', { files, source })
    //     },

    //     onSelectFiles() {
    //         if (this.isMobileBrowser) {
    //             this.$refs.fileUploadMenu.isOpen = true
    //         } else {
    //             this.onBrowse()
    //         }
    //     },
    // },
}
</script>

<template>
    <div>
        <FileUploadMenu
            ref="menu"
            @browse="browseFromComputer"
            @camera="captureFromCamera"
        />

        <v-btn
            color="primary"
            dark
            fixed
            bottom
            right
            icon
            large
            @click="browseFromComputer"
        >
            <v-icon>add</v-icon>
        </v-btn>

        <input
            ref="input"
            type="file"
            :accept="mimes"
            multiple
            class="d-none"
            @change="onInputFilesChanged($event.target.files)"
        />
    </div>
</template>
