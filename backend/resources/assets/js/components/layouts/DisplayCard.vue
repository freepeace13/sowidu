<template>
    <v-card
        class="display-card"
        :max-height="inline ? maxHeight : '100%'"
        :min-height="minHeight"
        dark
    >
        <v-layout :row="inline" :column="!inline">
            <v-flex
                :xs4="inline"
                :class="{
                    'd-none': hidePhotos,
                    'pr-0': inline,
                    'pb-0': !inline
                }"
            >
                <div class="photos-container" @click="$emit('click:photo')">
                    <div v-if="!photos.length" class="icon-wrapper">
                        <v-icon
                            class="icon-placeholder"
                            v-html="icon"
                            large
                        />
                    </div>
                    <div v-else class="position-relative">
                        <v-img
                            :lazy-src="photoCover"
                            :src="photoCover"
                            height="100%"
                            :max-height="maxHeight"
                            :min-height="minHeight"
                        />

                        <v-img
                            v-if="logo"
                            class="position-absolute b-1 r-1 elevation-1"
                            :lazy-src="logo.url"
                            :src="logo.url"
                            height="55"
                            width="55"
                        />
                    </div>
                </div>
            </v-flex>
            <v-flex :class="{
                    'pl-0': inline && !hidePhotos,
                    'pt-0': !inline && !hidePhotos
                }"
            >
                <v-container class="pa-3" :fill-height="!hidePhotos && inline">
                    <v-layout column justify-space-between>
                        <v-flex shrink>
                            <v-layout row justify-space-between>
                                <v-flex>
                                    <h3
                                        v-if="title"
                                        class="headline mb-0 cursor-clickable"
                                        v-html="title"
                                        @click="$emit('click:title')"
                                    />
                                        
                                    <div class="d-inline-flex" v-if="hasSlot('subtitle')">
                                        <slot name="subtitle"></slot>
                                    </div>
                                </v-flex>
                                <v-flex shrink align-self-start v-if="hasSlot('menu')">
                                    <v-menu
                                        offset-overflow
                                        transition="slide-x-transition"
                                        bottom left
                                        nudge-bottom="10"
                                        nudge-left="5"
                                        min-width="200px"
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-icon v-on="on">more_vert</v-icon>
                                        </template>
                                        
                                        <slot name="menu"></slot>
                                    </v-menu>
                                </v-flex>
                            </v-layout>
                        </v-flex>

                        <v-flex
                            v-if="$slots.default || $scopedSlots.default"
                            class="content-body"
                        >
                            <slot></slot>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-flex>
        </v-layout>
    </v-card>
</template>

<script>
import { isObject } from '~/support/helpers';

export default {
    name: 'DisplayCard',

    props: {
        photos: {
            type: Array,
            default: () => ([])
        },

        icon: {
            type: String,
            default: 'add_a_photo'
        },

        title: {
            type: String,
            default: null
        },

        maxHeight: {
            type: [String, Number],
            default: '230px'
        },

        minHeight: {
            type: [String, Number],
            default: '230px'
        },

        inline: {
            type: Boolean,
            default: true
        },

        hidePhotos: {
            type: Boolean,
            default: false
        }
    },

    computed: {
        photoCover() {
            return this.photos.length > 0 ?
                isObject(this.photos[0])
                ? this.photos[0].url
                : this.photos[0]
            : null;
        },

        logo() {
            return this.photos[1] || null;
        }
    },

    methods: {
        hasSlot(name = null) {
            return this.$scopedSlots[name] !== undefined
                || this.$slots[name] !== undefined;
        }
    }
}
</script>

<style lang="scss" scoped>
.display-card {
    display: flex;
}

.photos-container {
    background: #303030;
    width: 100%;
    height: 100%;

    .icon-wrapper {
        width: auto;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;

        .icon-placeholder {
            font-size: 8rem !important;
            cursor: pointer;
        }
    }
}

.icon-placeholder,
.headline {
    color: #E0E0E0;

    &:hover {
        color: #FFFFFF;
    }
}

.headline {
    font-weight: bold !important;
    letter-spacing: 1px !important;
    transition: color 0.2s ease-in-out;
}

.content-body {
    // height: 100%;
    flex-direction: column;
    display: flex;
    justify-content: center;
    overflow: unset;
}
</style>