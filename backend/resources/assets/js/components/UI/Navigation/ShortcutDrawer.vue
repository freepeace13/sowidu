<template>
    <v-navigation-drawer
        v-model="visibility"
        temporary fixed
        class="d-flex"
    >
        <v-list dense two-line>
            <v-list-tile avatar>
                <v-text-field
                    placeholder="Search"
                    v-model="search"
                    hide-details
                    ref="search"
                    solo
                    prepend-inner-icon="search"
                ></v-text-field>
            </v-list-tile>

            <v-subheader>SHORTCUT MENU</v-subheader>

            <v-divider></v-divider>

            <template v-for="page in computedPages">
                <v-list-tile
                    avatar
                    :to="page.route"
                    class="shortcut-item"
                    :key="page.name"
                >
                    <v-list-tile-avatar>
                        <v-icon v-html="page.icon"></v-icon>
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title class="text-uppercase" v-html="page.name"></v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>

                <v-divider :key="`divider_${page.name}`"></v-divider>
            </template>
        </v-list>
    </v-navigation-drawer>
</template>

<script>
import pages from '~/Menu';

export default {
    data: () => ({
        search: null
    }),

    computed: {
        pages,

        visibility: {
            get() {
                return this.$store.state.ui.shortcut;
            },
            set(value) {
                this.$store.commit('ui/TOGGLE_SHORTCUT', value);
            }
        },

        computedPages() {
            let pages = this.search
                ? this.pages.filter(e =>
                    e.name.toLowerCase().includes(this.search.toLowerCase())
                ) : this.pages;

            return pages.filter(e => e.visible);
        },
    }
}
</script>