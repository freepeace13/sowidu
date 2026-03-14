<template>
    <section id="media" style="width:100%;">
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
import { AuthorizationService } from '@features/auth/api';
import MediaCard from '../components/MediaCard';
import Media from '~/services/models/media';
import collect from 'collect.js';
import { showMedia } from '~/services/events/modal';
import UsesMediaStore from '../mixins/UsesMediaStore';
import DispatchWhenTokenChanges from '@common/mixins/DispatchWhenTokenChanges';
import * as Enums from '../enums';

export default {
    mixins: [
        UsesMediaStore(),
        DispatchWhenTokenChanges('media/all')
    ],

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
            items = this.$options.filters.sortDesc(items);

            AuthorizationService
                .can(Enums.PERMISSIONS.UPDATE_MEDIA)
                .then(() => {
                    this.$lightbox.open(items, current, { editable: true });
                })
                .catch(() => {
                    this.$lightbox.open(items, current, { editable: false });
                });
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
