<template>
    <v-dialog v-model="show" width="80%" :persistent="$lightbox.editing">
        <v-card class="round-0">
            <v-carousel
                :hide-controls="$lightbox.editing"
                hide-delimiters
                v-model="$lightbox.current"
                :cycle="false"
                height="600"
                mandatory
            >
                <v-carousel-item
                    class="media-wrapper"
                    lazy
                    v-for="(item, index) in $lightbox.items"
                    :key="item.identifier"
                    :value="index"
                >
                    <v-image-editor
                        v-if="item.isImage"
                        :item="item"
                        @start="$lightbox.editing = true"
                        @cancel="$lightbox.editing = false"
                        @apply="$lightbox.apply($event)"
                    >
                        <template v-slot="{ url, start }">
                            <v-img
                                :lazy-src="url"
                                :src="url"
                                contain
                            >
                                <v-btn
                                    v-if="$lightbox.settings.editable"
                                    color="white"
                                    class="t-2"
                                    absolute right fab flat dark
                                    @click="start"
                                >
                                    <v-icon>edit</v-icon>
                                </v-btn>
                            </v-img>
                        </template>
                    </v-image-editor>

                    <v-media-player :item="item" v-else />
                </v-carousel-item>
            </v-carousel>
        </v-card>
    </v-dialog>
</template>

<script>
import Media from '~/services/models/media';
import vMediaPlayer from './v-media-player';
import vImageEditor from './v-image-editor';
import { mapState, mapMutations } from 'vuex';

export default {
    name: 'v-lightbox-carousel',

    components: {
        vMediaPlayer,
        vImageEditor
    },

    data: () => ({
        editing: false
    }),

    computed: {
        show: {
            get() {
                return this.$lightbox.visible;
            },
            set(value) {
                if (value === false) {
                    this.$lightbox.close();
                }
            }
        }
    },

    methods: {
        ...mapMutations({
            'updateSource': 'ui/gallery/UPDATE_SOURCE',
        }),

        async applyChanges(file) {
            console.log('cropped file', file)
            // const mediaId = this.sources[this.current].id;
            // const uri = `/api/media/${mediaId}/upload`;

            // this.$media.reupload(mediaId, [file])((response) => {
            //     const result = Media.create(response.data);
            //     this.$store.commit('media/MEDIA_UPDATE', result);
            //     this.updateSource(result);
            //     this.editing = false;
            // }, (error) => {
            //     this.$events.$emit('alert', error.message);
            //     this.editing = false;
            // });
        }
    }
}
</script>