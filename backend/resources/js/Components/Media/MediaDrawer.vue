<template>
    <v-navigation-drawer
        ref="mediaListContainer"
        v-model="isShow"
        width="320"
        style="z-index: 6"
        v-bind="$attrs"
    >
        <v-list class="pa-1">
            <v-list-tile>
                <v-list-tile-content>
                    <v-list-tile-title>Media Library</v-list-tile-title>
                </v-list-tile-content>
                <v-list-tile-action>
                    <v-btn
                        icon
                        flat
                        @click="isShow = false"
                    >
                        <v-icon>close</v-icon>
                    </v-btn>
                </v-list-tile-action>
            </v-list-tile>
            <v-divider />
            <v-list-tile
                v-for="(mediaType, key) in mediaTypes"
                :key="key"
                :class="{ 'grey lighten-3': activeType == mediaType.type }"
                @click="fetch(mediaType.type)"
            >
                <v-list-tile-action>
                    <v-btn
                        icon
                        flat
                    >
                        <v-icon :color="mediaType.color">
                            {{ mediaType.icon }}
                        </v-icon>
                    </v-btn>
                </v-list-tile-action>
                <v-list-tile-content>
                    <v-list-tile-title>{{ mediaType.label }}</v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
        <v-list
            class="pt-0"
            dense
        >
            <v-divider />
            <v-list-tile>
                <v-list-tile-content>
                    <v-list-tile-title class="grey--text">
                        Select {{ activeType }}
                    </v-list-tile-title>
                </v-list-tile-content>
                <v-list-tile-action>
                    <MediaFilters
                        top
                        :small-button="true"
                        :media-categories="categories"
                        @filter="filterChanged"
                    />
                </v-list-tile-action>
            </v-list-tile>
            <v-container
                grid-list-sm
                fluid
                pt-0
                mt-4
            >
                <v-layout
                    row
                    wrap
                >
                    <v-alert
                        :value="!medias.length"
                        color="info"
                        icon="info"
                        outline
                    >
                        No documents found on your media.
                    </v-alert>

                    <v-tooltip
                        v-for="media in medias"
                        :key="media.id"
                        color="grey darken-4"
                        top
                    >
                        <template #activator="{ on }">
                            <v-flex
                                xs4
                                d-flex
                                v-on="on"
                            >
                                <v-card
                                    flat
                                    tile
                                    class="d-flex media"
                                    hover
                                    @click="attach(media)"
                                >
                                    <v-img
                                        :src="media.conversions.thumbnail"
                                        :lazy-src="media.conversions.thumbnail"
                                        aspect-ratio="1"
                                        class="grey lighten-2"
                                    >
                                        <template #placeholder>
                                            <v-layout
                                                fill-height
                                                align-center
                                                justify-center
                                                ma-0
                                            >
                                                <v-progress-circular
                                                    indeterminate
                                                    color="grey lighten-5"
                                                />
                                            </v-layout>
                                        </template>
                                        <v-layout
                                            fill-height
                                            align-center
                                            justify-center
                                            class="preview"
                                        >
                                            <v-btn
                                                :color="
                                                    media.file_type == 'video'
                                                        ? 'secondary'
                                                        : 'grey darken-4'
                                                "
                                                flat
                                                icon
                                                @click.stop="
                                                    $emit('preview', media)
                                                "
                                            >
                                                <v-icon>
                                                    {{ mediaFileIcon(media) }}
                                                </v-icon>
                                            </v-btn>
                                        </v-layout>
                                    </v-img>
                                </v-card>
                            </v-flex>
                        </template>
                        <span v-text="media.file_name" />
                    </v-tooltip>
                </v-layout>
            </v-container>
        </v-list>
    </v-navigation-drawer>
</template>
<script>
import axios from 'axios'
import { useInfiniteScroll } from '@vueuse/core'
import MediaFilters from '../MediaFilters.vue'

export default {
    components: { MediaFilters },

    props: {
        url: {
            type: String,
            required: false,
            default: 'media.drive.files.index',
        },
        allowedTypes: {
            type: Array,
            required: false,
            default: () => ['documents', 'images', 'videos'],
        },

        categories: {
            required: false,
            type: Array,
            default: () => [],
        },
    },
    data: () => ({
        activeType: 'images',
        isShow: false,
        isLoading: false,
        medias: [],
        pagination: {
            current_page: 0,
            next_page_url: null,
            isLoading: true,
        },
    }),

    computed: {
        mediaTypes() {
            const allTypes = [
                {
                    label: 'Documents',
                    icon: 'description',
                    type: 'documents',
                    color: 'green',
                },
                {
                    label: 'Images',
                    icon: 'image',
                    type: 'images',
                    color: 'blue',
                },
                {
                    label: 'Videos',
                    icon: 'video_library',
                    type: 'videos',
                    color: 'red',
                },
            ]

            return allTypes.filter(({ type }) =>
                this.allowedTypes.includes(type),
            )
        },
    },

    created() {
        if (!this.allowedTypes.includes('images')) {
            // Type `images` are not included on allowed media types - change default media type
            this.activeType = [...this.allowedTypes].shift()
        }
    },

    mounted() {
        useInfiniteScroll(
            this.$refs.mediaListContainer,
            () => {
                if (this.pagination.isLoading || !this.pagination.next_page_url)
                    return
                this.fetch(this.activeType, this.pagination.current_page + 1)
            },
            { distance: 10 },
        )
    },

    methods: {
        show() {
            this.isShow = true
            this.fetch(this.activeType)
        },

        async fetch(type = 'images', page = 1, count = 21, filters = {}) {
            try {
                this.isLoading = true
                this.activeType = type

                if (page === 1) {
                    this.medias = []
                    this.pagination = {
                        current_page: 0,
                        next_page_url: null,
                        isLoading: true,
                    }
                }

                const payload = 'basic'

                const {
                    data: { data, current_page, next_page_url },
                } = await axios.get(
                    this.$route(this.url, {
                        type,
                        count,
                        page,
                        payload,
                        ...filters,
                    }),
                )

                this.pagination = {
                    current_page,
                    next_page_url,
                }
                this.medias = [...this.medias, ...data]
            } catch (error) {
                console.error(error)
            } finally {
                this.isLoading = false
            }
        },

        attach(media) {
            this.$emit('attach', media)
            this.isShow = false
        },

        preview() {
            console.log('preview')
        },

        mediaFileIcon({ file_type }) {
            return file_type == 'video' ? 'play_arrow' : 'visibility'
        },

        filterChanged(filter) {
            this.fetch(this.activeType, 1, 21, filter)
        },
    },
}
</script>
<style scoped>
.preview {
    visibility: hidden;
}

.media:hover .preview {
    visibility: visible;
}
</style>
