<template>
    <v-list class="py-0 ma-0 status-filter-menu grey darken-4" dense two-line>
        <v-subheader class="text-uppercase">Quick Search</v-subheader>
        <v-list-tile>
            <v-text-field solo hide-details append-icon="search" flat></v-text-field>
        </v-list-tile>

        <v-divider class="mt-2"></v-divider>

        <v-subheader class="text-uppercase">Statuses</v-subheader>
        <v-list-tile avatar tag="div">
            <v-list-tile-avatar>
                <v-icon>fiber_manual_record</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-title>In Preparation</v-list-tile-title>
        </v-list-tile>
        <v-list-tile avatar tag="div">
            <v-list-tile-avatar>
                <v-icon>fiber_manual_record</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-title>Completed</v-list-tile-title>
        </v-list-tile>
        <v-list-tile avatar tag="div">
            <v-list-tile-avatar>
                <v-icon>fiber_manual_record</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-title>Pending</v-list-tile-title>
        </v-list-tile>
        <v-list-tile avatar tag="div">
            <v-list-tile-avatar>
                <v-icon>fiber_manual_record</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-title>Final</v-list-tile-title>
        </v-list-tile>
        <v-list-tile avatar tag="div">
            <v-list-tile-avatar>
                <v-icon>fiber_manual_record</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-title>Done</v-list-tile-title>
        </v-list-tile>
        <v-list-tile avatar tag="div">
            <v-list-tile-avatar>
                <v-icon>fiber_manual_record</v-icon>
            </v-list-tile-avatar>
            <v-list-tile-title>Cancelled</v-list-tile-title>
        </v-list-tile>
    </v-list>
</template>
