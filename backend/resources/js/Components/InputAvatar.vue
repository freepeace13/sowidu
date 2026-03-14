<template>
    <!-- <v-avatar color="grey lighten-3" :size="size"> -->
    <v-img
        :src="image"
        class="tw-relative tw-cursor-pointer tw-group"
    >
        <input
            ref="cropper"
            type="file"
            name="slim"
            accept="image/*"
            required
        />
        <span
            class="tw-absolute tw-bottom-0 tw-inset-x-0 tw-bg-primary group-hover:tw-bg-primary/90 tw-text-white"
        >
            Upload Photo
        </span>
    </v-img>
    <!-- </v-avatar> -->
</template>

<script>
// https://pqina.nl/slim/#docs

import Slim from '../Libraries/Slim/slim'

export default {
    props: {
        image: String,

        editOnSelect: {
            type: Boolean,
            default: true,
        },

        width: {
            type: [String, Number],
            default: 100,
        },

        size: {
            type: [String, Number],
            default: 100,
        },
    },

    mounted() {
        this.$slim = new Slim(this.$refs.cropper, {
            ratio: '1:1',
            didSave: () => this.$emit('saved', this.$slim.data),
            didRemove: () => console.log('Slim [remove]: ', this.$slim.data),
            didInit: (data) => console.log('Slim initialized: ', data),
            instantEdit: this.editOnSelect,
            label: null,
            labelLoading: null,
            crop: {
                x: 0,
                y: 0,
                width: 130,
                height: 130,
            },
        })
    },

    destroyed() {
        if (this.$slim) {
            this.$slim.destroy()
        }
    },
}
</script>

<style>
@import '../Libraries/Slim/slim.css';

.slim {
    background: transparent;
    position: relative;
}

.slim .slim-loader {
    top: 0 !important;
    bottom: 0;
    left: 0;
    right: 0 !important;
    margin: auto;
}

.slim .slim-btn {
    height: 30px;
    width: 30px;
    margin: 0 2px;
}

.slim:hover {
    background: transparent !important;
}
</style>
