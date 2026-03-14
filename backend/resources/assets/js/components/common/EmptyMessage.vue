<template>
    <v-flex class="pa-5 text-xs-center x12">
        <v-icon large color="grey darken-3" style="font-size:70px;">block</v-icon>
        <h1 class="mt-3 grey--text text--darken-1" v-text="message"></h1>
    </v-flex>
</template>
<script>
/**
 * Empty list component
 * - message = Message body of empty list
 * @type {Object}
 */
export default {
    props: {
        message: {
            required: false,
            default: 'No results found.',
            type: String
        },
    },
}
</script>
