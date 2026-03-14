<template>
    <div class="image-editor" :class="{ 'in-progress': $lightbox.editing }">
        <img :src="source" class="canvas" ref="canvas" />

        <slot v-bind="{ url: source, start }" v-if="!$lightbox.editing"></slot>

        <div class="toolbar">
            <v-btn
                :disabled="$lightbox.uploading"
                color="secondary"
                small
                class="ma-0"
                @click="cancel"
            >
                Cancel
            </v-btn>

            <div>
                <v-btn small flat class="ma-0" @click="flipVertical">
                    <v-icon class="mr-1">swap_vert</v-icon>
                    Flip Vertical
                </v-btn>
                <v-btn small flat class="ma-0" @click="flipHorizontal">
                    <v-icon class="mr-1">swap_horiz</v-icon>
                    Flip Horizontal
                </v-btn>
            </div>

            <v-btn
                :disabled="$lightbox.uploading"
                color="primary"
                small
                class="ma-0"
                @click="confirm"
            >
                Save
            </v-btn>
        </div>
    </div>
</template>

<script>
import Cropper from 'cropperjs';
import LightboxItem from '../models/item';
import { helpers } from '@libs/core';

export default {
    name: 'v-image-editor',

    props: {
        item: {
            type: LightboxItem,
            required: true
        }
    },

    data: () => ({
        dataUrl: null
    }),

    computed: {
        source() {
            return this.dataUrl || this.item.url;
        }
    },

    methods: {
        flipVertical() {
            const data = this.$cropper.getData();
            this.$cropper.scaleY(data.scaleY === -1 ? 1 : -1);
        },

        flipHorizontal() {
            const data = this.$cropper.getData();
            this.$cropper.scaleX(data.scaleX === -1 ? 1 : -1);
        },

        cancel(shouldEmit = true) {
            if (this.$cropper) {
                this.$cropper.destroy();
            }

            if (shouldEmit) {
                this.$emit('cancel');
            }

            this.dataUrl = null;
        },

        confirm() {
            this.dataUrl = this.$cropper.getCroppedCanvas({
                width: 320, height: 180
            }).toDataURL();

            const file = helpers.base64ToFile(this.dataUrl);

            file.identifier = this.item.identifier;

            this.$emit('apply', file);
            this.cancel(false);
        },

        start() {
            this.dataUrl = null;

            this.$emit('start');

            this.$nextTick(() => this.reinitialize());
        },

        reinitialize() {
            if (this.$cropper) {
                this.$cropper.destroy();
            }

            this.$cropper = new Cropper(this.$refs.canvas, {
                aspectRatio: NaN,
                dragMode: 'crop',
                background: false,
                viewMode: 1,
                responsive: true
            });
        }
    },

    beforeDestroy() {
        this.cancel();
    }
}
</script>

<style scoped>
@import '~cropperjs/dist/cropper.css';
.image-editor,
.image-editor > .v-image {
    height: 100%;
    position: relative;
}

.toolbar {
    background: rgba(0, 0, 0, .7);
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    display: none;
    padding: 15px;
}

.image-editor.in-progress .toolbar {
    display: flex;
    justify-content: space-between;
}

.canvas {
    visibility: hidden;
    position: absolute;
}
</style>