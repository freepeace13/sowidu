<template>
    <v-layout justify-center align-center fill-height>
        <Spinner :size="50" color="primary" />
    </v-layout>
</template>

<script>
import Spinner from '../Spinner';

export default {
    name: 'FallbackSpinner',

    components: { Spinner }
}
</script>