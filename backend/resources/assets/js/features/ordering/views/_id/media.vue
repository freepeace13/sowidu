<template>
    <OrderSectionLayout
        id="order-media"
        :title="$t('labels.media')"
        :hide-actions="!orderCopy.policies.isModifiable()"
    >
        <template slot="actions">
            <v-btn flat icon @click="browseMedia">
                <v-icon>add</v-icon> 
            </v-btn>
        </template>

        <v-layout
            v-for="[date, items] in Object.entries(timeline)"
            :key="date"
            column
        >
            <v-flex xs1>
                <v-subheader>{{ date }}</v-subheader>
                <v-divider></v-divider>
            </v-flex>
            <v-flex>
                <v-layout row wrap>
                    <v-flex
                        lg3 md4 sm6 xs12
                        v-for="source in items.all()"
                        :key="`${date}_${source.id}`"
                    >
                        <MediaCard
                            :inline="false"
                            :src="source.url"
                            :title="source.name || 'Untitled'"
                            :type="source.mimetype"
                            :is-avatar="source.isAvatar"
                            :description="source.uploadedOn"
                            :hide-menu="!orderCopy.policies.isModifiable()"
                            :video="$store.getters['media/isVideo'](source.mimetype)"
                        >
                            <template v-slot:default v-if="source.description">
                                {{ source.description }}
                            </template>

                            <template slot="menu">
                                <v-list light>
                                    <v-list-tile>
                                        <v-list-tile-title
                                            @click="dispatchRemovedMedia(source)"
                                        >
                                            Remove
                                        </v-list-tile-title>
                                    </v-list-tile>
                                </v-list>
                            </template>
                        </MediaCard>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
    </OrderSectionLayout>
</template>

<script>
import Media from '~/services/models/media';
import MediaCard from '~/components/UI/Cards/MediaCard';
import { showMediaSelector } from '~/services/events/modal';
import OrderSectionLayout from '../components/OrderSectionLayout';
import PageMixin from '../mixins/PageMixin';
import ShowOrderHandlers from '../../mixins/ShowOrderHandlers';
import ShowOrderStates from '../../mixins/ShowOrderStates';

export default {
    name: 'ShowMediaView',

    mixins: [
        ShowOrderHandlers(),
        ShowOrderStates()
    ],

    components: {
        MediaCard,
        OrderSectionLayout
    },

    computed: {
        timeline() {
            return Media
                .collection(this.order.media)
                .groupBy(({ formattedDates: { monthDayUploaded } }) => monthDayUploaded)
                .all();
        }
    },

    methods: {
        dispatchRemovedMedia(source) {
            const media = Media
                .collection(this.order.media)
                .remove(source)
                .all();

            this.handleResetMedia(media);
        },

        browseMedia() {
            showMediaSelector({
                selected: this.order.media,
                onSelect: (response) => {
                    this.handleResetMedia(media);
                    response.close();
                }
            });
        }
    }
}
</script>
