<template>
    <v-list class="py-0 ma-0 status-filter-menu grey darken-4" dense two-line>
        <v-subheader class="text-uppercase">Quick Search</v-subheader>
        <v-list-tile>
            <v-text-field solo placeholder="Search Orders" hide-details append-icon="search" flat></v-text-field>
        </v-list-tile>

        <v-divider class="mt-2"></v-divider>

        <v-subheader class="text-uppercase">Statuses</v-subheader>
        <v-list-tile
            @click="$emit('state', 'all')"
            :class="{ active: !$route.query.state || $route.query.state === 'all' }"
        >
            <v-list-tile-avatar><v-icon>refresh</v-icon></v-list-tile-avatar>
            <v-list-tile-title>Pending/Ongoing Orders</v-list-tile-title>
        </v-list-tile>
        <v-list-tile
            @click="$emit('state', 'pending')"
            :class="{ active: $route.query.state === 'pending' }"
        >
            <v-list-tile-avatar><v-icon>fact_check</v-icon></v-list-tile-avatar>
            <v-list-tile-title>Pending Confirmation</v-list-tile-title>
        </v-list-tile>
        <v-list-tile
            @click="$emit('state', 'drafts')"
            :class="{ active: $route.query.state === 'drafts' }"
        >
            <v-list-tile-avatar><v-icon>edit</v-icon></v-list-tile-avatar>
            <v-list-tile-title>Drafts</v-list-tile-title>
        </v-list-tile>
        <v-list-tile
            @click="$emit('state', 'history')"
            :class="{ active: $route.query.state === 'history' }"
        >
            <v-list-tile-avatar><v-icon>history</v-icon></v-list-tile-avatar>
            <v-list-tile-title>History</v-list-tile-title>
        </v-list-tile>
        <v-divider></v-divider>

        <v-subheader class="text-uppercase">Recently Created</v-subheader>
        <v-list-tile>Order item 1</v-list-tile>
    </v-list>
</template>

<script>
export default {
    props: {
        filterBy: Function
    }
}
</script>

<style lang="scss" scoped>
    .status-filter-menu {
        /deep/ div[role="listitem"] {
            &.active > .v-list__tile {
                background: rgba(255,255,255,0.08);
            }
        }
    }
</style>
