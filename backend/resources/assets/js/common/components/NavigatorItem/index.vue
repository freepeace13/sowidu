<template>
    <div>
        <v-tooltip right>
            <template v-slot:activator="{ on }">
                <v-list-tile
                    v-on="on"
                    avatar
                    active-class="grey darken-3"
                    :to="to"
                    :href="href"
                    class="shortcut-item"
                >
                    
                    <v-list-tile-avatar>
                        <v-icon v-html="icon"></v-icon>
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title
                            class="text-uppercase"
                            v-html="title"
                        />
                    </v-list-tile-content>
                </v-list-tile>
            </template>
            <span v-html="title"></span>
        </v-tooltip>
    </div>
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

        icon: {
            type: String,
            default: null
        },

        href: {
            type: String,
            default: null
        }
    }
}
</script>