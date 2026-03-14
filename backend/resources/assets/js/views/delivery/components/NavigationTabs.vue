<template>
    <v-tabs
        color="grey darken-4"
        grow
        slider-color="white"
    >
        <v-tab exact :to="{ name: 'deliveries', query: { type: 'incoming' } }">
            <span>Incoming</span>
        </v-tab>
        <v-tab exact :to="{ name: 'deliveries', query: { type: 'outgoing' } }">
            <span>Outgoing</span>
        </v-tab>
    </v-tabs>
</template>
