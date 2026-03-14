<template>
    <div class="global-components-section">
        <Modals />
        <Snackbar />
        <ChunkUploader />

        <v-resumable></v-resumable>
        <v-lightbox-carousel></v-lightbox-carousel>
    </div>
</template>

<script>
import Snackbar from '~/components/containers/Snackbar';
import Modals from '~/components/containers/Modal';
import ChunkUploader from '~/components/containers/ChunkUploader';
import VResumable from '@libs/vue-resumable/components/v-resumable';
import vLightboxCarousel from '@libs/v-lightbox/components/v-lightbox-carousel';
import VLightboxCarousel from '../../libs/v-lightbox/components/v-lightbox-carousel.vue';

export default {
    components: {
        Snackbar,
        Modals,
        ChunkUploader,
        VResumable,
        vLightboxCarousel
    }
}
</script>