<template>
    <div class="todo">
        <v-toolbar
            color="transparent"
            flat
        >
            <v-toolbar-title class="title sm:tw-w-auto tw-w-full">
                <v-icon
                    color="grey darken-1"
                    class="mr-1"
                    :medium="$vuetify.breakpoint.mdAndUp"
                    left
                >
                    leaderboard
                </v-icon>
                <span class="md:tw-text-xl tw-text-lg">Boards</span>
                <!--<span v-if="$vuetify.breakpoint.smAndUp">Boards</span>-->
            </v-toolbar-title>
            <v-spacer />
            <v-layout
                row
                wrap
            >
                <v-flex>
                    <v-text-field
                        v-model="filters.q"
                        :placeholder="`${$t('labels.search')}...`"
                        solo
                        class="input-no-margin-bottom"
                        prepend-inner-icon="search"
                        :class="{
                            small: $vuetify.breakpoint.smAndDown,
                        }"
                        :loading="filters.processing"
                        :disabled="filters.processing"
                        @input="filterBoards"
                    />
                </v-flex>
            </v-layout>
            <v-spacer class="hidden-sm-and-down" />
            <v-btn
                color="primary"
                class="tw-min-w-[32px]"
                :small="$vuetify.breakpoint.xsOnly"
                @click="$refs.boardForm.show()"
            >
                <v-icon :left="$vuetify.breakpoint.smAndUp">add</v-icon>
                {{ $vuetify.breakpoint.xs ? '' : $t('buttons.create-board') }}
            </v-btn>
        </v-toolbar>
        <v-divider />
        <v-data-table
            :headers="headers"
            :items="boards"
            :loading="filters.processing"
            :rows-per-page-items="[10]"
            class="elevation-1 pa-0"
        >
            <template #items="{ item }">
                <td
                    class="font-weight-bold"
                    @click="
                        $inertia.get(
                            $route('todos.boards.tasks.index', item.id),
                        )
                    "
                >
                    <div class="tw-flex tw-items-center">
                        <BoardLogo
                            :src="item.logo"
                            :title="item.title"
                            :is-icon="item.is_icon"
                            :color="item?.icon_color"
                        />
                        <span class="cursor-pointer hover-underline">
                            {{ item.title }}
                        </span>
                    </div>
                </td>
                <td class="tw-flex tw-items-center tw-gap-x-2">
                    <Subscriber :avatar="item.owner.photo" />
                    {{ item.owner.name }}
                </td>
                <td>{{ item.description }}</td>
                <td width="10%">
                    <BoardMenu
                        @click:edit="$refs.boardForm.show(item)"
                        @click:change-cover-photo="changeCoverPhoto(item.id)"
                        @click:duplicate="duplicate(item.id)"
                        @click:delete="deleteBoard(item.id)"
                    />
                </td>
            </template>
            <template #no-data>
                <v-alert
                    :value="true"
                    outline
                    class="small"
                    type="warning"
                    icon="info"
                >
                    <div class="tw-flex tw-justify-between tw-items-center">
                        {{
                            filters.q
                                ? $t('hints.we-didnt-find-anything-for', {
                                      keyword: filters.q,
                                  })
                                : $t('hints.no-boards-for-now')
                        }}
                        <v-btn
                            color="primary"
                            small
                            depressed
                            class="tw-ml-auto"
                            @click="$refs.boardForm.show()"
                        >
                            {{ $t('buttons.create-board') }}
                        </v-btn>
                    </div>
                </v-alert>
            </template>
        </v-data-table>
        <BoardForm ref="boardForm" />
    </div>
</template>

<script>
import BoardForm from '../Partials/Board/BoardForm.vue'
import BoardMenu from '../Partials/Board/BoardMenu.vue'
import { useDebounceFn } from '@vueuse/core'
import Subscriber from '../Partials/Subscriber/Subscriber.vue'
import BoardLogo from '../Partials/Board/BoardLogo.vue'

export default {
    components: {
        BoardForm,
        BoardMenu,
        Subscriber,
        BoardLogo,
    },

    props: {
        boards: {
            type: Array,
            default: () => [],
        },
    },

    data: (vm) => ({
        headers: [
            {
                text: 'Title',
                value: 'title',
                sortable: false,
            },
            { text: 'Admin', value: 'admin', sortable: false },
            { text: 'Description', value: 'description', sortable: false },
            { text: 'Actions', value: 'action', sortable: false },
        ],

        filters: vm.$inertia.form({
            q: vm.$route().params.q,
        }),
    }),

    created() {
        this.filterBoards = useDebounceFn(async () => {
            const { q } = this.filters

            const routeName = this.$route().current()
            const routeParams = this.$route().params

            this.filters
                .transform((data) => ({
                    ...data,
                    q,
                }))
                .get(this.$route(routeName, { ...routeParams, q }), {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['boards', 'filters'],
                })
        }, 500)
    },

    methods: {
        changeCoverPhoto(id) {
            console.log('Change cover photo', id)
        },

        duplicate(id) {
            this.$confirm.ask({
                title: this.$t('todo.labels.duplicate-board'),
                question: this.$t('todo.labels.confirm-duplicate'),
                confirm: () => {
                    this.$inertia.post(
                        this.$route('todos.boards.duplicate', { board: id }),
                        {
                            onSuccess: () =>
                                this.$root.$emit(
                                    'flash.success',
                                    this.$t('todo.labels.duplicate-success'),
                                ),
                        },
                    )
                },
            })
        },

        deleteBoard(id) {
            this.$confirm.ask({
                title: this.$t('todo.labels.delete-board'),
                question: this.$t('todo.labels.confirm-delete'),
                type: this.$t('todo.labels.delete'),
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('todos.boards.destroy', { board: id }),
                        {
                            onSuccess: () =>
                                this.$root.$emit(
                                    'flash.success',
                                    this.$t('todo.labels.board-board-deleted'),
                                ),
                        },
                    )
                },
            })
        },
    },
}
</script>
