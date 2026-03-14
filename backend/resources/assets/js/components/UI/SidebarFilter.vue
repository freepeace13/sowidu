<template>
    <v-list class="py-0 ma-0 status-filter-menu grey darken-4" dense two-line>
        <v-subheader class="text-uppercase">Quick Search</v-subheader>
        <v-list-tile>
            <v-text-field solo
                @change="$emit('search', search)"
                v-model="search"
                :placeholder="searchPlaceHolder"
                hide-details
                append-icon="search" flat></v-text-field>
        </v-list-tile>
        <span v-show="isShowFilter">
            <v-divider class="mt-2"></v-divider>

            <v-subheader class="text-uppercase">Filters</v-subheader>
            <v-list-tile
                avatar
                v-for="(filter, index) in filters"
                :key="index"
                @click="filterChanged(filter.key)"
                tag="div"
                :class="{ active: activeFilter === filter.key }">
                <v-list-tile-avatar>
                    <v-icon :color="filter.color">fiber_manual_record</v-icon>
                </v-list-tile-avatar>
                <v-list-tile-title ><span v-text="filter.title" class="text-capitalize"></span></v-list-tile-title>
            </v-list-tile>
        </span>

    </v-list>
</template>

<script>

    /**
     * Side bar filter component
     * sample props: filters: [{key: 'active', color: 'red', title: 'active'}],
     *
     * @type {Object}
     */
    export default {
        props: {
            filters: {
                required: true,
                type: Array
            },
            module: {
                required: false,
                type: String,
                default: ''
            },
            defaultActive: {
                required: false,
                type: String,
                default: ''
            },
            isShowFilter: {
                required: false,
                type: Boolean,
                default: true
            }
        },

        data: () => ({
            activeFilter: '',
            search: '',
        }),

        computed: {
            activeStatus()
            {
                return this.filters.find(f => f.key === this.activeFilter) || {}
            },

            searchPlaceHolder()
            {
                return `Search ${this.module}`
            },
        },

        methods: {
            filterChanged(key) {
                this.activeFilter = key
                this.$emit('filter', key)
            }
        },

        mounted() {
            if (this.defaultActive != '')
                this.activeFilter = this.defaultActive

            AppEvent.on('contacts.sidebar.search.reset', () => this.search = '')
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
