<template>
    <v-menu
        v-model="menu"
        right
        offset-overflow
        :close-on-content-click="false"
        min-width="280"
        z-index="12"
    >
        <template #activator="{ on }">
            <v-btn
                depressed
                class="px-2"
                :color="filterIsOn ? 'primary' : ''"
                :small="$vuetify.breakpoint.xs"
                v-on="on"
            >
                <v-icon left>filter_list</v-icon>Filter
            </v-btn>
            <v-btn
                v-show="filterIsOn"
                v-tooltip.top="'Clear filter'"
                icon
                color="secondary"
                small
                class="mx-0"
                @click="clearFilters"
            >
                <v-icon small>clear</v-icon>
            </v-btn>
        </template>
        <v-toolbar
            dense
            flat
            card
            class="content-pad-sm"
        >
            <v-toolbar-title class="body-1">
                {{ $t('labels.filters') }}
            </v-toolbar-title>
            <v-spacer />
            <v-btn
                icon
                @click="menu = false"
            >
                <v-icon>close</v-icon>
            </v-btn>
        </v-toolbar>
        <v-list
            two-line
            subheader
        >
            <v-subheader> {{ $t('hints.keyword') }} </v-subheader>
            <v-list-tile>
                <v-list-tile-content>
                    <v-text-field
                        v-model="filters.q"
                        :placeholder="$t('hints.enter-keyword')"
                        :hint="$t('todo.hints.search-cards-members')"
                        persistent-hint
                        class="w-full pt-0"
                        :loading="isLoading"
                    />
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
        <v-divider />
        <v-list
            subheader
            dense
        >
            <v-subheader class="body-1">
                {{ $t('labels.members') }}</v-subheader
            >
            <v-list-tile :class="['cursor-pointer', { disabled: isLoading }]">
                <v-list-tile-action class="min-w-32">
                    <v-checkbox
                        v-model="filters.unassigned"
                        :loading="isLoading"
                        :disabled="isLoading"
                    />
                </v-list-tile-action>
                <v-list-tile-content
                    @click="filters.unassigned = !filters.unassigned"
                >
                    <v-layout
                        row
                        wrap
                        align-center
                    >
                        <v-flex
                            shrink
                            mr-2
                        >
                            <v-icon size="24"> account_circle </v-icon>
                        </v-flex>
                        <v-flex class="body-1">
                            {{ $t('labels.no-members') }}
                        </v-flex>
                    </v-layout>
                </v-list-tile-content>
            </v-list-tile>
            <div class="board-filter-options">
                <v-list-tile
                    v-for="subscriber in subscribers"
                    :key="subscriber.id"
                    :class="['cursor-pointer', { disabled: isLoading }]"
                >
                    <v-list-tile-action class="min-w-32">
                        <v-checkbox
                            v-model="filters.members"
                            :value="subscriber.id"
                            :loading="isLoading"
                            :disabled="isLoading"
                        />
                    </v-list-tile-action>
                    <FilterSubscriberItem
                        :subscriber="subscriber"
                        @click="
                            (id) =>
                                filters.members.includes(id)
                                    ? (filters.members = filters.members.filter(
                                          (m) => m != id,
                                      ))
                                    : (filters.members = [
                                          ...filters.members,
                                          id,
                                      ])
                        "
                    />
                </v-list-tile>
            </div>
        </v-list>
        <v-divider />
        <v-list
            subheader
            dense
        >
            <v-subheader class="body-1">
                {{ $t('labels.labels') }}
            </v-subheader>
            <div class="board-filter-options">
                <v-list-tile
                    v-for="label in labels"
                    :key="label.id"
                    :class="['cursor-pointer', { disabled: isLoading }]"
                >
                    <v-list-tile-action class="min-w-32">
                        <v-checkbox
                            v-model="filters.labels"
                            :value="label.id"
                            :loading="isLoading"
                            :disabled="isLoading"
                        />
                    </v-list-tile-action>
                    <v-list-tile-content
                        @click="
                            () =>
                                filters.labels.includes(label.id)
                                    ? (filters.labels = filters.labels.filter(
                                          (l) => l != label.id,
                                      ))
                                    : (filters.labels = [
                                          ...filters.labels,
                                          label.id,
                                      ])
                        "
                    >
                        <TaskLabel
                            :label="label"
                            align="left"
                            class="task-label-filter"
                        />
                    </v-list-tile-content>
                </v-list-tile>
            </div>
        </v-list>
    </v-menu>
</template>
<script>
import FilterSubscriberItem from './FilterSubscriberItem.vue'
import TaskLabel from '../../Label/TaskLabel.vue'
import { useDebounceFn } from '@vueuse/core'
import isEmpty from 'lodash/isEmpty'

export default {
    components: { FilterSubscriberItem, TaskLabel },

    props: {
        subscribers: {
            type: Array,
            default: () => [],
            required: false,
        },

        labels: {
            required: false,
            type: Array,
            default: () => [],
        },

        activeFilters: {
            required: false,
            type: Object,
        },
    },

    data: () => ({
        menu: false,
        filters: {
            members: [],
            labels: [],
            q: null,
            unassigned: null,
        },
        isLoading: false,
    }),

    computed: {
        filterIsOn() {
            return Object.values(this.filters).find((value) => !isEmpty(value))
        },
    },

    watch: {
        filters: {
            handler: function (filters) {
                this.filterTasks(filters)
            },
            deep: true,
        },
    },

    created() {
        this.filterTasks = useDebounceFn(async (params) => {
            this.isLoading = true
            const filters = Object.fromEntries(
                Object.entries(params).filter(([, value]) => value),
            )
            const routeName = this.$route().current()
            const routeParams = this.$route().params

            this.$inertia.get(
                this.$route(routeName, { ...routeParams, filters }),
                {},
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['groups', 'filters'],
                    onFinish: () => (this.isLoading = false),
                },
            )
        }, 500)

        if (
            Object.values(this.activeFilters).find((value) => !isEmpty(value))
        ) {
            this.filters = this.activeFilters
            this.menu = true
        }
    },

    methods: {
        clearFilters() {
            this.filters = {
                members: [],
                labels: [],
                q: null,
                unassigned: null,
            }

            this.menu = false
        },
    },
}
</script>
