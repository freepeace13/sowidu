<template>
    <v-card flat tile :class="{ 'd-none': !items.length }">
        <v-window v-model="batch" class="px-2">
            <v-window-item
                v-for="(chunk, index) in chunkedItems"
                :key="`chunk_${index}`"
            >
                <v-layout class="justify-space-around">
                    <v-flex
                        xs4
                        v-for="media in chunk"
                        :key="`media_${media.id}`"
                    >
                        <v-img
                            :src="media.url"
                            :lazy-src="media.url"
                            height="200px"
                            aspect-ratio="1"
                            @click="$lightbox.open(chunk, media, { editable: false })"
                        />
                    </v-flex>
                </v-layout>
            </v-window-item>
        </v-window>
        
        <v-card-actions class="justify-space-between" v-if="chunkedItems.length > 1">
            <v-btn icon @click="prev" flat>
                <v-icon>chevron_left</v-icon>
            </v-btn>

            <v-btn icon @click="next" flat>
                <v-icon>chevron_right</v-icon>
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import { Media } from '~/services/models';
import { showMediaSelector } from '~/services/events/modal';

export default {
    name: 'MediaSlider',

    props: {
        items: {
            type: Array,
            validator(prop) {
                return prop.every((v) => v instanceof Media);
            }
        }
    },

    data: () => ({
        batch: 0
    }),

    computed: {
        chunkedItems() {
            return Media.collection(this.items)
                .chunk(3)
                .all();
        }
    },

    methods: {
        next() {
            this.batch = this.batch + 1 === this.chunkedItems.length
                ? 0 
                : this.batch + 1;
        },

        prev() {
            this.batch = this.batch - 1 < 0
                ? this.chunkedItems.length - 1
                : this.batch - 1;
        }
    }
}
</script>