<template>
    <section id="media" class="mb-3">
        <v-divider></v-divider>
        <v-subheader class="px-4">Media</v-subheader>
        <v-container grid-list-lg fluid class="py-0">
            <MediaSlider :items="media" />
        </v-container>
    </section>
</template>

<script>
import MediaSlider from './MediaSlider';
import { Media } from '~/services/models';

export default {
    name: 'FormsMediaList',

    components: { MediaSlider },

    props: {
        media: {
            type: Array,
            validator(prop) {
                return prop.every((v) => v instanceof Media);
            }
        }
    }
}
</script>