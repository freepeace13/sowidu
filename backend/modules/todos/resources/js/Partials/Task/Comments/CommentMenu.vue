<template>
    <v-menu>
        <template #activator="{ on }">
            <v-icon
                class="tw-cursor-pointer"
                v-on="on"
            >
                more_horiz
            </v-icon>
        </template>
        <v-list dense>
            <v-list-tile @click="$emit('click:edit')">
                <v-list-tile-title>
                    <v-icon
                        small
                        color="blue"
                        class="mr-1"
                        >edit</v-icon
                    >
                    Edit
                </v-list-tile-title>
            </v-list-tile>
            <v-list-tile @click="$emit('click:delete')">
                <v-list-tile-title>
                    <v-icon
                        small
                        color="red"
                        class="mr-1"
                        >delete</v-icon
                    >
                    Delete
                </v-list-tile-title>
            </v-list-tile>
        </v-list>
    </v-menu>
</template>
