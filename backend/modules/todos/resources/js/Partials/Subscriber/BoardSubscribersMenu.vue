<template>
    <v-menu
        v-model="menu"
        :min-width="360"
        :close-on-content-click="false"
        offset-y
    >
        <template #activator="{ on }">
            <div v-on="on">
                <v-flex fill-height>
                    <v-btn
                        icon
                        dark
                        outline
                        color="blue-grey darken-2"
                        depressed
                        class="mt-2"
                    >
                        <v-icon> person_add </v-icon>
                    </v-btn>
                </v-flex>
            </div>
        </template>

        <v-card>
            <v-toolbar
                dense
                flat
                card
                px-1
            >
                <v-toolbar-title class="body-1">
                    {{ $t('todo.labels.add-to-board') }}
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="menu = false"
                >
                    <v-icon> close </v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <v-container
                grid-list-xs
                pt-0
                pb-4
            >
                <v-layout
                    row
                    wrap
                >
                    <v-flex>
                        <v-text-field
                            v-model="keyword"
                            :loading="isSearching"
                            color="primary"
                            :placeholder="$t('todo.hints.search-user')"
                            prepend-inner-icon="search"
                            hide-details
                        />
                    </v-flex>
                </v-layout>
            </v-container>
            <v-divider />
            <v-list subheader>
                <v-subheader class="px-4">
                    {{ $t('labels.board_members') }}</v-subheader
                >
            </v-list>
            <v-list
                subheader
                class="members-list"
            >
                <SubscribersMenuItem
                    v-for="item in items"
                    :key="item.id"
                    :photo="item?.user?.photo ?? item.photo"
                    :name="item?.user?.name ?? item.name"
                    :description="item?.role ?? item.email"
                    :subscriber="isSubscriber(item?.user?.id ?? item.id)"
                    @click:add="add(item.email)"
                    @click:remove="remove(item)"
                />
                <v-subheader
                    v-show="!items.length"
                    class="justify-center grey lighten-2 mx-4"
                >
                    No user found.
                </v-subheader>
            </v-list>
            <v-divider class="my-2" />
            <v-card-actions>
                <v-spacer />
                <v-btn
                    flat
                    @click="menu = false"
                >
                    {{ $t('buttons.close') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>
<script>
import { useDebounceFn } from '@vueuse/core'
import axios from 'axios'
import SubscribersMenuItem from './SubscriberMenuItem.vue'
import BoardSubscriberMixin from '../../Mixins/BoardSubscriberMixin'

export default {
    components: { SubscribersMenuItem },
    mixins: [BoardSubscriberMixin],

    props: {
        subscribers: {
            type: Array,
            required: false,
            default: () => [],
        },
    },
    data: () => ({
        menu: false,
        keyword: '',
        results: null,
        isSearching: false,
    }),

    computed: {
        items() {
            return this.results ?? this.subscribers
        },
    },

    watch: {
        keyword(keyword) {
            if (!keyword) {
                this.results = null
                return
            }
            this.searchUser({ keyword })
        },

        menu(value) {
            if (value) {
                this.keyword = ''
                this.result = null
            }
        },
    },

    mounted() {
        this.searchUser = useDebounceFn(async (params) => {
            try {
                this.isSearching = true
                this.results = []
                const {
                    data: { results },
                } = await axios.get(this.$route('todos.users.index', params))
                this.results = results
            } catch (error) {
                console.error(error)
            } finally {
                this.isSearching = false
            }
        }, 500)
    },

    methods: {
        isSubscriber(id) {
            return this.subscribers.some(
                (subscriber) => subscriber.user.id == id,
            )
        },

        remove(item) {
            if (!this.allowedToManageSubscribers()) return

            const subscriber = Object.prototype.hasOwnProperty.call(
                item,
                'user',
            )
                ? item.id
                : this.subscribers.find((s) => s.user.id == item.id)?.id

            this.removeBoardSubscriber(subscriber)
        },

        add(email) {
            if (!this.allowedToManageSubscribers()) return

            this.$inertia.post(
                this.$route('todos.boards.subscribers.store', {
                    board: this.$page.props.board.id,
                }),
                {
                    email,
                    role: 'guest',
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['subscribers', 'errors'],
                    errorBag: 'addBoardSubscriber',
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                    onSuccess: () =>
                        this.$root.$emit(
                            'flash.success',
                            'User has been added to this board.',
                        ),
                },
            )
        },
    },
}
</script>
<style scoped>
.members-list {
    overflow-y: auto;
    max-height: 200px;
}
</style>
