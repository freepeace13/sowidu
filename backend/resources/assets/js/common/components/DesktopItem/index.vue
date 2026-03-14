<template>
    <v-flex xs12 md12 lg3>
        <v-card class="py-5 px-4 desktop-item" :to="to" :href="href">
            <v-layout align-center>
                <v-flex>
                    <v-card-title class="title text-uppercase pa-0" v-html="title" />
                </v-flex>
                <v-flex shrink>
                    <v-btn fab dark small depressed color="primary">0</v-btn>
                    <v-btn fab dark small depressed color="primary">0</v-btn>
                </v-flex>
            </v-layout>
        </v-card>
    </v-flex>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            default: null,
        },

        to: {
            type: Object,
            default: null
        },

        href: {
            type: String,
            default: null
        }
    }
}
</script>

<style lang="scss" scoped>
    @import './styles.scss';
</style>