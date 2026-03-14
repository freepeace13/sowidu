<template>
    <v-dialog
        v-model="isShow"
        max-width="600px"
        scrollable
    >
        <v-card>
            <v-toolbar
                flat
                dense
            >
                <v-toolbar-title>Add Manual Task</v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="isShow = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-card-text>
                <v-container
                    grid-list-xs
                    fluid
                    pa-0
                >
                    <v-layout wrap>
                        <v-flex xs12>
                            <v-subheader class="px-0">
                                Assign to ({{ selectedSubscribers.length }}
                                selected)
                            </v-subheader>

                            <v-chip
                                v-for="user in selectedSubscribers"
                                :key="user.id"
                                close
                                class="ma-1"
                                @click:close="removeSubscriber(user)"
                            >
                                {{ user.name }}
                            </v-chip>

                            <v-menu
                                v-model="searchMenu"
                                :close-on-content-click="false"
                                offset-y
                                max-width="600px"
                            >
                                <template #activator="{ on }">
                                    <v-text-field
                                        v-model="subscriberKeyword"
                                        :loading="isSearching"
                                        color="primary"
                                        placeholder="Search users to add..."
                                        prepend-inner-icon="search"
                                        hide-details
                                        class="mb-4 mt-2"
                                        v-on="on"
                                        @focus="searchMenu = true"
                                    />
                                </template>

                                <v-card>
                                    <v-list
                                        subheader
                                        class="members-list"
                                    >
                                        <SubscribersMenuItem
                                            v-for="item in filteredUsers"
                                            :key="item.id"
                                            :photo="item?.user?.photo"
                                            :name="item?.user?.name"
                                            :description="item?.role"
                                            :subscriber="isSubscriber(item.id)"
                                            @click:add="addSubscriber(item)"
                                        />
                                        <v-subheader
                                            v-show="!filteredUsers.length"
                                            class="justify-center grey lighten-2 mx-4"
                                        >
                                            {{
                                                isSearching
                                                    ? 'Searching...'
                                                    : 'No users found'
                                            }}
                                        </v-subheader>
                                    </v-list>
                                </v-card>
                            </v-menu>
                        </v-flex>

                        <v-flex xs6>
                            <v-menu
                                v-model="startDateMenu"
                                :close-on-content-click="false"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template #activator="{ on }">
                                    <v-text-field
                                        v-model="startDate"
                                        label="Start Date"
                                        prepend-icon="event"
                                        readonly
                                        v-on="on"
                                        @focus="searchMenu = false"
                                    />
                                </template>
                                <v-date-picker
                                    v-model="startDate"
                                    @input="startDateMenu = false"
                                />
                            </v-menu>
                        </v-flex>
                        <v-flex xs6>
                            <v-menu
                                v-model="finishDateMenu"
                                :close-on-content-click="false"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template #activator="{ on }">
                                    <v-text-field
                                        v-model="finishDate"
                                        label="Finish Date"
                                        prepend-icon="event"
                                        readonly
                                        v-on="on"
                                        @focus="searchMenu = false"
                                    />
                                </template>
                                <v-date-picker
                                    v-model="finishDate"
                                    @input="finishDateMenu = false"
                                />
                            </v-menu>
                        </v-flex>

                        <v-flex xs12>
                            <v-text-field
                                v-model="subject"
                                label="Subject"
                                prepend-icon="title"
                                required
                                @focus="searchMenu = false"
                            />
                            <v-textarea
                                v-model="description"
                                clearable
                                name="input-7-1"
                                label="Description"
                                prepend-icon="notes"
                                placeholder="Enter description..."
                                rows="3"
                                @focus="searchMenu = false"
                            />
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="primary"
                    @click="submit"
                    >Save</v-btn
                >
                <v-btn @click="isShow = false">Cancel</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { useDebounceFn } from '@vueuse/core'
import axios from 'axios'
import SubscribersMenuItem from '../../Subscriber/SubscriberMenuItem.vue'

export default {
    components: {
        SubscribersMenuItem,
    },

    props: {
        members: {
            type: Array,
            required: false,
            default: () => [],
        },
        boardSubscribers: {
            type: Array,
            required: false,
            default: () => [],
        },
    },
    data: () => ({
        isShow: false,
        searchMenu: false,
        subscriberKeyword: '',
        isSearching: false,
        subscriberItems: [],
        startDateMenu: false,
        finishDateMenu: false,
        startDate: null,
        finishDate: null,
        subject: '',
        description: '',
        selectedSubscribers: [],
    }),
    computed: {
        filteredUsers() {
            return this.subscriberItems.filter(
                (user) =>
                    !this.selectedSubscribers.some(
                        (selected) => selected.id === user.id,
                    ),
            )
        },
    },

    watch: {
        subscriberKeyword: {
            handler(keyword) {
                if (!keyword) {
                    this.subscriberItems = this.boardSubscribers
                    return
                }
                this.searchUser({ keyword })
            },
            immediate: true,
        },
    },

    mounted() {
        this.searchUser = useDebounceFn(async (params) => {
            try {
                const board = this.$page.props.board.id
                this.isSearching = true
                this.subscriberItems = []
                const {
                    data: { results },
                } = await axios.get(
                    this.$route('todos.boards.subscribers.index', { board }),
                    {
                        params,
                    },
                )
                this.subscriberItems = results
            } catch (error) {
                // console.error(error)
            } finally {
                this.isSearching = false
            }
        }, 500)
    },

    methods: {
        show() {
            this.isShow = true
            this.selectedSubscribers = [...this.members]
            this.subscriberItems = this.boardSubscribers
            this.subscriberKeyword = ''
            this.subject = ''
            this.description = ''
            this.startDate = null
            this.finishDate = null
            this.searchMenu = false
        },

        isSubscriber(id) {
            return this.selectedSubscribers.some((member) => member.id == id)
        },

        addSubscriber(item) {
            if (!this.isSubscriber(item.id)) {
                this.selectedSubscribers.push({
                    id: item.user.id,
                    name: item.user.name,
                    photo: item.user.photo,
                    role: item.role,
                })
            }
            this.searchMenu = false
        },

        removeSubscriber(subscriber) {
            this.selectedSubscribers = this.selectedSubscribers.filter(
                (member) => member.id !== subscriber.id,
            )
        },

        submit() {
            const board = this.$page.props.board.id
            const data = {
                title: this.subject,
                description: this.description,
                start_date: this.startDate,
                finish_date: this.finishDate,
                subscriber: this.selectedSubscribers.map((sub) => sub.id),
                group: 'Backlog',
                board: board,
            }

            this.$inertia.post(
                this.$route('todos.boards.manual-task.store', { board }),
                data,
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['groups', 'errors'],
                    errorBag: 'createManualTask',
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            'Manual task has been added successfully.',
                        )
                    },
                    onError: (errors) => {
                        this.$root.$emit('flash.validation', errors)
                    },
                    onFinish: () => {
                        this.$emit('close')
                        this.isShow = false
                    },
                },
            )
        },
    },
}
</script>

<style scoped>
.members-list {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
}

.v-chip {
    cursor: pointer;
    transition: all 0.3s ease;
}

.v-chip:hover {
    transform: scale(1.05);
}
</style>
