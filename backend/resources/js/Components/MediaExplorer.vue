<template>
    <v-dialog :value="show"
              :fullscreen="$vuetify.breakpoint.xsOnly"
              transition="dialog-bottom-transition"
              full-width
              scrollable
              persistent
              content-class="fill-height"
    >
        <v-card class="overflow-hidden" style="position: relative;" tile>
            <v-toolbar color="transparent" card>
                <v-toolbar-title class="mr-auto">
                    {{ title }}
                </v-toolbar-title>

                <v-spacer />

                <v-btn icon @click="$emit('close')">
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>

            <v-divider />

            <v-card-text class="pa-0 fill-height">
                <v-container fluid grid-list-md>
                    <v-layout row wrap align-start justify-start fill-height>
                        <v-flex v-for="media in mediaFiles" :key="media.id" xs6 md4 lg3 xl2>
                            <media-grid-view :url="media.url" :aspect-ratio="16/9">
                                <v-checkbox v-model="selected" multiple
                                            hide-details
                                            :value="media.id"
                                            class="ml-1 mt-1 fill-height"
                                            color="secondary"
                                            on-icon="check_circle"
                                            off-icon="circle"
                                />
                            </media-grid-view>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>

            <v-toolbar card>
                <v-toolbar-title v-if="selected.length" class="body-2">
                    {{ selected.length }} item(s) selected
                </v-toolbar-title>

                <v-spacer />

                <v-btn color="primary" @click="select">
                    Select
                </v-btn>
            </v-toolbar>
        </v-card>
    </v-dialog>
</template>

<script>
import MediaGridView from './MediaGridView.vue';
import axios from 'axios';

export default {
    components: {
        MediaGridView,
    },

    props: {
        show: {
            type: Boolean,
            default: false,
        },

        title: {
            type: String,
            default: 'Media Explorer',
        },
    },

    data() {
        return {
            listView: 'grid',
            mediaFiles: [],
            selected: [],
        }
    },

    watch: {
        show: {
            immediate: true,
            handler(show) {
                if (!show) return;

                axios.get(this.$route('json.media.images'))
                    .then((response) => {
                        this.mediaFiles = response.data.files;
                    });

                // this.$inertia.reload({
                //     only: ['media'],
                //     headers: {
                //         'X-Inertia-Partial-Params': JSON.stringify({ type: 'image' })
                //     },
                //     onSuccess: (page) => this.mediaFiles = page.props.media
                // });
            },
        },
    },

    methods: {
        select() {
            const selected = this.selected.map((id) => {
                return this.mediaFiles.find((media) => media.id === id);
            });

            this.$emit('select', selected);

            this.selected = [];
        },
    },
}
</script>