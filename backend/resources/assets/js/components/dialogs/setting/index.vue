<template>
    <v-dialog v-model="value" fullscreen hide-overlay transition="dialog-bottom-transition">
        <v-card class="background">
            <!-- Setting dialog toolbar -->
            <v-toolbar dark color="grey darken-4">
                <v-btn icon dark @click="close">
                    <v-icon>close</v-icon>
                </v-btn>

                <v-toolbar-title class="fw100">Settings</v-toolbar-title>

                <v-spacer></v-spacer>

                <v-toolbar-items>
                    <v-btn dark flat @click="close">Save</v-btn>
                </v-toolbar-items>
            </v-toolbar>

            <!-- Setting tabs -->
            <v-tabs icons-and-text centered dark color="grey darken-4">
                <v-tabs-slider color="cyan"></v-tabs-slider>

                <v-tab href="#tab-1">
                    Account Settings
                    <v-icon>account_circle</v-icon>
                </v-tab>

                <v-tab href="#tab-2">
                    Global Settings
                    <v-icon>beach_access</v-icon>
                </v-tab>

                <v-tab href="#tab-3">
                    Rights and Permissions
                    <v-icon>security</v-icon>
                </v-tab>

                <v-tab-item v-for="i in 3" :key="i" :id="'tab-' + i">
                    <v-card flat>
                        <v-card-text>Sample text</v-card-text>
                    </v-card>
                </v-tab-item>
            </v-tabs>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        props: ['value'],

        methods: {
            close () {
                this.$emit('input', false)
            }
        }
    }
</script>