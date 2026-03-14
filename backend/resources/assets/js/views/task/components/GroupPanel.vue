<template>
    <v-flex xs12 sm6 md4 xl3>
        <v-card class="grey darken-4 elevation-3">
            <v-toolbar flat color="grey darken-3">
                <v-icon :color="color">fiber_manual_record</v-icon>
                <v-toolbar-title>{{ name }}</v-toolbar-title>
            </v-toolbar>

            <div class="pa-3">
                <slot></slot>
            </div>
        </v-card>
    </v-flex>
</template>

<script>
export default {
    name: 'GroupPanel',

    props: {
        name: {
            type: String,
            required: true
        },

        color: {
            type: String,
            required: true
        }
    }
}
</script>