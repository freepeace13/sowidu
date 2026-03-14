<template>
    <v-app dark>
        <v-container grid-list-lg fluid>
            <slot></slot>
        </v-container>

        <ModalContainer />
        <Gallery />
    </v-app>
</template>

<script>
import ModalContainer from '~/components/containers/Modal';
import Gallery from '~/components/containers/Gallery';
import store from '~/services/store';

export default {
    store,
    components: {
        Gallery,
        ModalContainer,
    },
}
</script>