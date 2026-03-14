<template>
    <div class="image-viewer">
        <slot></slot>

        <v-dialog v-model="show" width="80%" :persistent="editing">
            <v-card class="round-0">
                <v-carousel
                    :hide-controls="editing"
                    hide-delimiters
                    :value="current"
                    :cycle="false"
                    height="600"
                    mandatory
                >
                    <v-carousel-item
                        class="media-wrapper"
                        v-for="[index, source] of sources.entries()"
                        :key="index"
                    >
                        <VideoPlayer
                            v-if="$store.getters['media/isVideo'](source.type)"
                            :sources="[source]"
                        />

                        <ImageEditor
                            @started="() => editing = true"
                            @aborted="() => editing = false"
                            @confirmed="croppedFile"
                            :src="source.src"
                            v-else
                        >
                            <template v-slot="{ src, start }">
                                <v-img
                                    :lazy-src="src"
                                    :src="src"
                                    contain
                                >
                                    <v-btn
                                        color="white"
                                        class="t-2"
                                        absolute right fab flat dark
                                        @click="start"
                                    >
                                        <v-icon>edit</v-icon>
                                    </v-btn>
                                </v-img>
                            </template>
                        </ImageEditor>
                    </v-carousel-item>
                </v-carousel>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import Media from '~/services/models/media';
import VideoPlayer from '~/components/VideoPlayer';
import ImageEditor from '~/components/containers/ImageEditor';
import { mapState, mapMutations } from 'vuex';
import UsesMediaStore from '~/components/Mixins/UsesMediaStore';

export default {
    name: 'Gallery',

    mixins: [UsesMediaStore()],

    components: {
        VideoPlayer,
        ImageEditor
    },

    data: () => ({
        editing: false
    }),

    computed: {
        ...mapState({
            sources: (state) => state.ui.gallery.sources,
            current: state => state.ui.gallery.current,
        }),

        show: {
            get() {
                return this.sources.length > 0;
            },
            set(value) {
                value || this.close();
            }
        }
    },

    methods: {
        ...mapMutations({
            'updateSource': 'ui/gallery/UPDATE_SOURCE',
            'close': 'ui/gallery/CLOSE_GALLERY',
        }),

        async croppedFile(file) {
            const mediaId = this.sources[this.current].id;
            const uri = `/api/media/${mediaId}/upload`;

            this.$media.reupload(mediaId, [file])((response) => {
                const result = Media.create(response.data);
                this.$store.commit('media/MEDIA_UPDATE', result);
                this.updateSource(result);
                this.editing = false;
            }, (error) => {
                this.$events.$emit('alert', error.message);
                this.editing = false;
            });

            // try {
            //     const result = await this.$store.dispatch('media/reupload', {
            //         mediaId: this.sources[this.current].id,
            //         files: [file]
            //     });

            //     this.updateSource(result);
            // } catch (error) {
            //     this.$events.$emit('alert', error);
            // } finally {
            //     this.editing = false;
            // }
        }
    }
}
</script>