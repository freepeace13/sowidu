<template>
    <v-layout fill-height>
        <v-flex xs12 class="image-container" :class="{ thumbnail: thumbnail }">
            <div class="toolbar">
                <div class="toolbar-button" @click="activate" v-if="!editMode">
                    <v-icon>edit</v-icon>
                </div>

                <div class="toolbar-button" v-if="editMode" @click="save">
                    <v-icon>save</v-icon>
                </div>
                <div class="toolbar-button" v-if="editMode" @click="flipHorizontal">
                    <v-icon>swap_horiz</v-icon>
                </div>
                <div class="toolbar-button" v-if="editMode" @click="flipVertical">
                    <v-icon>swap_vert</v-icon>
                </div>
                <div class="toolbar-button" v-if="editMode" @click="cancel">
                    <v-icon>close</v-icon>
                </div>
            </div>

            <v-layout fill-height>
                <v-flex xs12 class="text-xs-center">
                    <img :src="media.url" ref="image" class="image"/>
                </v-flex>
            </v-layout>
        </v-flex>
    </v-layout>
</template>

<script>
    import Cropper from 'cropperjs'
    import { Base64ToFile } from '~/utils/filesystem'

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
            cropper: null,
            editMode: false
        }),

        methods: {
            flipHorizontal() {
                const data = this.cropper.getData()
                this.cropper.scaleX(data.scaleX === -1 ? 1 : -1)
            },

            flipVertical() {
                const data = this.cropper.getData()
                this.cropper.scaleY(data.scaleY === -1 ? 1 : -1)
            },

            cancel() {
                if (this.cropper) {
                    this.cropper.destroy()
                }

                this.editMode = false
            },

            activate() {
                if (this.thumbnail) return

                if (this.cropper) {
                    this.cropper.destroy()
                }

                this.cropper = new Cropper(this.$refs.image, {
                    aspectRatio: NaN,
                    dragMode: 'crop',
                    background: false,
                    viewMode: 1
                })

                this.editMode = true
            },

            async save() {
                const canvas = this.cropper.getCroppedCanvas({
                    width: 320,
                    height: 180
                })

                const file = Base64ToFile(canvas.toDataURL())

                this.$uploader.upload({
                    url: `/api/media/${this.media.id}`,
                    files: [file],
                    onError: (file, message) => {
                        const response = JSON.parse(message)
                        let error = {}
                        if (response.errors.file)
                            error = response.errors.file[0]
                        else if (response.errors.resumableType)
                            error = response.errors.resumableType[0]
                        else if (response.errors.resumableTotalSize)
                            error = response.errors.resumableTotalSize[0]

                        this.$events.$emit('alert', error)
                    },
                    onFileSuccess: (file, message) => {
                        const response = JSON.parse(message)
                        this.$emit('change', response.data)
                    },
                    onCompleted: () => {
                        this.$uploader.close()
                        this.cancel()
                    }
                })
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import '~cropperjs/dist/cropper.css';

    .image-container {
        position: relative;
        &:not(.thumbnail):hover {
            > .toolbar {
                display: block;
            }
        }

        .toolbar {
            display: none;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            z-index: 999;
            background: rgba(0, 0, 0, .6);
            padding: 15px 20px;

            .toolbar-button {
                cursor: pointer;
                float: left;

                &:not(:last-child) {
                    margin-right: 10px;
                }
            }
        }

        .image {
            max-width: 100%;
            max-height: 100%;
            min-width: 200px;
        }
    }
</style>
