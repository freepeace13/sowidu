<template>
    <v-menu nudge-bottom="52" nudge-right="-40" min-width="200px">
        <template v-slot:activator="{ on }">
            <v-btn flat v-on="on">
                New Contact <v-icon>arrow_drop_down</v-icon> 
            </v-btn>
        </template>

        <v-list>
            <v-list-tile tag="div" @click="$emit('click:user')">
                <v-list-tile-title>New User Contact</v-list-tile-title>
            </v-list-tile>

            <v-list-tile tag="div" @click="$emit('click:company')">
                <v-list-tile-title>New Company Contact</v-list-tile-title>
            </v-list-tile>

            <v-list-tile tag="div" @click="$emit('click:employee')">
                <v-list-tile-title>New Employee Contact</v-list-tile-title>
            </v-list-tile>
        </v-list>
    </v-menu>
</template>