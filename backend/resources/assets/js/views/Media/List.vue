<template>
    <section id="media">
        <v-layout
            v-for="[date, items] of Object.entries(timeline)"
            :key="date"
            row
        >
            <v-flex xs1>
                <v-subheader>{{ date }}</v-subheader>
            </v-flex>
            <v-flex>
                <v-layout row wrap>
                    <v-flex
                        lg3 md4 sm6 xs12
                        v-for="source in $options.filters.sortDesc(items)"
                        :key="`${date}_${source.id}`"
                    >
                        <MediaCard
                            :inline="false"
                            :src="source.url"
                            :title="source.name || 'Untitled'"
                            :type="source.mimetype"
                            :is-avatar="isAvatar(source)"
                            :description="source.uploadedOn"
                            :video="$store.getters['media/isVideo'](source.mimetype)"

                            @click:photo="openGallery(items, source)"
                            @click:title="openGallery(items, source)"
                            @menu:preview="openGallery(items, source)"
                            @menu:avatar="setAvatar(source)"
                            @menu:edit="openMedia(source.id)"
                        >
                            <template v-slot:default v-if="source.description">
                                {{ source.description }}
                            </template>
                        </MediaCard>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </section>
</template>

<script>
import { postdata } from '~/utils/request'
import { bytesToSize } from '~/utils/filesystem'
import axios from 'axios'
import { fetchMedia } from '~/services/api/media';
import { mapState } from 'vuex';
import { createResource } from 'vue-async-manager';
import MediaCard from '~/components/UI/Cards/MediaCard';
import Media from '~/services/models/media';
import collect from 'collect.js';
import { showMedia } from '~/services/events/modal';
import { UsesMediaStore } from '~/components/Mixins';

export default {
    mixins: [UsesMediaStore()],

    components: { MediaCard },

    filters: {
        sortDesc(collection) {
            return collection.sortByDesc('createdAt').all();
        }
    },

    computed: {
        isAvatar() {
            return (media) => {
                const { avatar } = this.$store.getters['auth/profile']();
                return avatar.reference && avatar.reference.mediaId === media.id;
            }
        }
    },

    methods: {
        openGallery(items, current) {
            this.$lightbox.open(this.$options.filters.sortDesc(items), current);
        },

        openMedia(mediaId) {
            showMedia(mediaId);
        },

        setAvatar(media) {
            this.$store.dispatch('media/setAsAvatar', media);
        },
    }
}
</script>
