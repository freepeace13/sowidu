<template>
    <v-menu
        v-model="open"
        :close-on-content-click="false"
        :nudge-left="200"
        :nudge-width="100"
        offset-y
    >
        <template v-slot:activator="{ on }">
            <v-btn v-on="on" flat>
                <v-icon left dark>filter_list</v-icon>
                Filters
            </v-btn>
        </template>

        <v-card>
            <v-list>
                <v-list-tile>
                    <v-list-tile-content>
                        <v-list-tile-title>Filter by status</v-list-tile-title>
                    </v-list-tile-content>
                    <v-list-tile-action>
                        <v-btn flat @click="clearAll">Clear All</v-btn>
                    </v-list-tile-action>
                </v-list-tile>
            </v-list>
            <v-card-text>
                <div class="text-xs-left">{{ filters.length }} item(s) selected</div>
                <v-layout row wrap>
                    <v-flex xs4>
                        <v-checkbox
                            hide-details
                            v-model="filters"
                            value="pending"
                            :title="$t('hints.status.pending')"
                            :label="$t('labels.state.pending')"
                        />
                    </v-flex>
                    <v-flex xs4>
                        <v-checkbox
                            hide-details
                            v-model="filters"
                            value="final"
                            :title="$t('hints.status.ongoing')"
                            :label="$t('labels.state.ongoing')"
                        />
                    </v-flex>
                    <v-flex xs4>
                        <v-checkbox
                            hide-details
                            v-model="filters"
                            value="completed"
                            :title="$t('hints.status.unconfirmed')"
                            :label="$t('labels.state.unconfirmed')"
                        />
                    </v-flex>
                    <v-flex xs4>
                        <v-checkbox
                            hide-details
                            v-model="filters"
                            value="preparation"
                            :title="$t('hints.status.drafts')"
                            :label="$t('labels.state.drafts')"
                        />
                    </v-flex>
                    <v-flex xs4>
                        <v-checkbox
                            hide-details
                            v-model="filters"
                            value="done"
                            :title="$t('hints.status.done')"
                            :label="$t('labels.state.done')"
                        />
                    </v-flex>
                    <v-flex xs4>
                        <v-checkbox
                            hide-details
                            v-model="filters"
                            value="cancelled"
                            :title="$t('hints.status.cancelled')"
                            :label="$t('labels.state.cancelled')"
                        />
                    </v-flex>
                </v-layout>
            </v-card-text>
        </v-card>
    </v-menu>
</template>

<script>
export default {
    name: "OrderStatusFilter",

    data: () => ({
        open: false
    }),

    computed: {
        filters: {
            get() {
                return this.$route.query.state || [];
            },
            set(value) {
                this.$router.replace({ query: { state: value } }).catch(() => {});
            }
        }
    },

    methods: {
        clearAll() {
            this.$router.replace({ query: { state: [] } }).catch(() => {});
        }
    }
}
</script>
