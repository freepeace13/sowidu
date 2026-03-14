<template>
    <div class="image-editor" :class="{ 'in-progress': inProgress }">
        <img :src="source" class="canvas" ref="canvas" />

        <slot v-bind="{ src: source , start }" v-if="!inProgress"></slot>

        <div class="toolbar">
            <v-btn color="secondary" small class="ma-0" @click="abort">
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

            <v-btn color="primary" small class="ma-0" @click="confirm">
                Save
            </v-btn>
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import Cropper from 'cropperjs';
import { base64ToFile } from '~/support/helpers';

const options = {
    aspectRatio: NaN,
    dragMode: 'crop',
    background: false,
    viewMode: 1,
    responsive: true
}

export default {
    props: {
        src: {
            type: String,
            required: true
        }
    },

    data: () => ({
        inProgress: false,
        dataUrl: null
    }),

    computed: {
        source() {
            return this.dataUrl || this.src;
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

        abort(shouldEmit = true) {
            if (this.$cropper) {
                this.$cropper.destroy();
            }

            if (shouldEmit) {
                this.$emit('aborted');
            }

            this.dataUrl = null;
            this.inProgress = false;
        },

        confirm() {
            this.dataUrl = this.$cropper.getCroppedCanvas({
                width: 320, height: 180
            }).toDataURL();

            this.$emit('confirmed', base64ToFile(this.dataUrl));
            this.abort(false);
        },

        start() {
            this.dataUrl = null;
            this.inProgress = true;

            this.$emit('started');

            this.$nextTick(() => this.reinitialize());
        },

        reinitialize() {
            if (this.$cropper) {
                this.$cropper.destroy();
            }

            this.$cropper = new Cropper(this.$refs.canvas, options);
        }
    },

    beforeDestroy() {
        this.abort();
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