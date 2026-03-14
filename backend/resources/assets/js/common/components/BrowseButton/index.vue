<template>
    <v-menu offset-x>
        <template v-slot:activator="{ on }">
            <v-btn
                color="grey darken-3"
                class="mb-0"
                v-on="on"
            >
                <v-icon size="17">folder</v-icon> &nbsp; {{ $t('buttons.browse') }}
            </v-btn>
        </template>

        <v-list class="grey darken-3" dense>
            <v-list-tile
                v-if="includes('deliveries')"
                @click="$emit('browse-deliveries')"
            >
                <v-list-tile-title>Deliveries</v-list-tile-title>
            </v-list-tile>

            <v-list-tile
                v-if="includes('media')"
                @click="$emit('browse-media')"
            >
                <v-list-tile-title>Media</v-list-tile-title>
            </v-list-tile>

            <v-list-tile
                v-if="includes('tasks')"
                @click="$emit('browse-tasks')"
            >
                <v-list-tile-title>Tasks</v-list-tile-title>
            </v-list-tile>

            <v-list-tile
                v-if="includes('orders')"
                @click="$emit('browse-orders')"
            >
                <v-list-tile-title>Orders</v-list-tile-title>
            </v-list-tile>
        </v-list>
    </v-menu>
</template>

<script>
const links = [
    'orders',
    'deliveries',
    'tasks',
    'media'
];

export default {
    name: 'BrowsersMenu',

    props: {
        only: {
            type: Array,
            default: () => ([]),
            validator(prop) {
                return prop.every((v) => links.includes(v));
            }
        },

        except: {
            type: Array,
            default: () => ([]),
            validator(prop) {
                return prop.every((v) => links.includes(v));
            }
        }
    },

    computed: {
        includes() {
            return (link) => {
                if (this.only.length) {
                    return this.only.includes(link);
                }

                return this.except.length ? !this.except.includes(link) : true;
            }
        }
    }
}
</script>