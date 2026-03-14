<template>
    <v-snackbar
        :value="!!message"
        :top="true"
        @input="message = null"
        :timeout="5000"
    >
        <div v-html="message"></div>
        <v-btn
            color="pink"
            flat
            @click="message = null"
        >
            Close
        </v-btn>
    </v-snackbar>
</template>

<script>
export default {
    name: 'AlertToaster',

    data: () => ({
        message: null
    }),

    mounted() {
        this.$events.$on('alert', (message) => this.message = message);
    }
}
</script>