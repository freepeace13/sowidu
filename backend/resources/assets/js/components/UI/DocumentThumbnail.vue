<template>
    <v-hover>
        <v-card
            slot-scope="{ hover }"
            :class="[{ 'light-blue accent-4': selected }, `elevation-${hover ? 15 : 2}`]"
            class="mx-auto"
        >
            <v-menu offset-y v-model="dropdown" absolute>
                <template v-slot:activator="{ on }">
                    <div class="text-xs-center py-3" v-on="on">
                        <v-icon size="80" color="grey lighten-4">
                            insert_drive_file
                        </v-icon>
                    </div>
                </template>

                <v-list>
                    <v-list-tile v-if="!selected" @click="$emit('toggle-selection', attachable)">
                        <v-list-tile-title>Select File</v-list-tile-title>
                    </v-list-tile>

                    <v-list-tile v-else @click="$emit('toggle-selection', attachable)">
                        <v-list-tile-title>Unselect File</v-list-tile-title>
                    </v-list-tile>

                    <v-list-tile target="_blank">
                        <v-list-tile-title>View Details</v-list-tile-title>
                    </v-list-tile>
                </v-list>
            </v-menu>

            <v-divider></v-divider>

            <v-card-title class="text-xs-center d-block py-2">{{ label }}</v-card-title>
        </v-card>
    </v-hover>
</template>

<script>
import { Order, Delivery, Task } from '~/services/models';

export default {
    props: {
        attachable: {
            type: [Order, Delivery, Task],
            required: true
        },

        label: {
            type: String,
            required: true
        },

        selected: {
            type: Boolean,
            default: false
        }
    },

    data: () => ({
        dropdown: false,
    })
}
</script>

<style lang="scss" scoped>
    .document-thumbnail {
        padding: 8px 5px;
        border-radius: 3px;
        color: #F5F5F5;
        background-color: #424242;
    }
</style>
