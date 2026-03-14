<template>
    <v-list light>
        <v-list-tile>
            <v-list-tile-title @click="$emit('remove')">
                Remove
            </v-list-tile-title>
        </v-list-tile>
    </v-list>
</template>