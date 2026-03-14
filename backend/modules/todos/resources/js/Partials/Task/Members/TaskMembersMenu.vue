<template>
    <v-menu
        v-model="isShow"
        :position-x="x"
        :position-y="y"
        v-bind="$attrs"
        :min-width="360"
        :close-on-content-click="false"
    >
        <v-card>
            <v-toolbar
                dense
                flat
                card
                px-1
            >
                <v-toolbar-title class="body-1"> Assign </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="isShow = false"
                >
                    <v-icon>close</v-icon>
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
                            placeholder="Search user"
                            prepend-inner-icon="search"
                            hide-details
                        />
                    </v-flex>
                </v-layout>
            </v-container>
            <v-divider />
            <v-list subheader>
                <v-subheader
                    class="px-4 d-flex flex-col align-items-start py-2"
                >
                    <div class="">Board members</div>
                    <div class="blue--text caption">
                        Select subscriber to add to this task
                    </div>
                </v-subheader>
            </v-list>
            <v-list
                subheader
                class="members-list"
            >
                <SubscribersMenuItem
                    v-for="item in items"
                    :key="item.id"
                    :photo="item?.user?.photo"
                    :name="item?.user?.name"
                    :description="item?.role"
                    :subscriber="isSubscriber(item.id)"
                    @click:add="add(item)"
                    @click:remove="remove(item)"
                />
                <v-subheader
                    v-show="!items.length"
                    class="justify-center grey lighten-2 mx-4"
                >
                    Cannot found board subscribers.
                </v-subheader>
            </v-list>
            <v-divider class="my-2" />
            <v-card-actions>
                <v-spacer />
                <v-btn
                    flat
                    @click="isShow = false"
                    >Close</v-btn
                >
            </v-card-actions>
        </v-card>
    </v-menu>
</template>
<script>
import { useDebounceFn } from '@vueuse/core'
import axios from 'axios'
import SubscribersMenuItem from '../../Subscriber/SubscriberMenuItem.vue'
import TaskMemberMixin from '../../../Mixins/TaskMemberMixin'

/**
 * @TODO optimize merge this with `Partials/Subscriber/BoardSubscribersMenu`
 */
export default {
    components: { SubscribersMenuItem },

    mixins: [TaskMemberMixin],

    props: {
        boardSubscribers: {
            type: Array,
            required: false,
        },
    },

    data: () => ({
        isShow: false,
        x: 0,
        y: 0,
        keyword: '',
        isSearching: false,
        task: null,
        items: [],
        options: {
            reload: ['taskMembers'],
        },
    }),

    watch: {
        keyword(keyword) {
            if (!keyword) {
                this.results = this.boardSubscribers
                return
            }
            this.searchUser({ keyword })
        },

        isShow(value) {
            if (value) {
                this.keyword = ''
                this.result = null
            }

            if (!value) {
                this.task = null
                this.items = []
                this.options = {
                    reload: ['taskMembers'],
                }
            }
        },
    },

    mounted() {
        const board = this.$page.props?.board?.id
        this.searchUser = useDebounceFn(async (params) => {
            try {
                this.isSearching = true
                this.items = []
                const {
                    data: { results },
                } = await axios.get(
                    this.$route('todos.boards.subscribers.index', { board }),
                    {
                        params,
                    },
                )
                this.items = results
            } catch (error) {
                console.error(error)
            } finally {
                this.isSearching = false
            }
        }, 500)
    },

    methods: {
        show(e, task = null, options = null) {
            e.preventDefault()

            this.isShow = false

            this.task = task ?? this.$page.props.task

            if (task) {
                this.options = options
            }

            this.x = e.clientX
            this.y = e.clientY

            this.$nextTick(() => {
                this.items = this.boardSubscribers
                this.isShow = true
            })
        },

        isSubscriber(id) {
            const taskMembers =
                this.task?.members ?? this.$page.props.taskMembers
            return taskMembers?.some((member) => member.id == id)
        },

        async add(subscriber) {
            const task = this.task ?? this.$page.props.task
            const { id } = subscriber
            this.addMember(task, id, this.options)
            this.isShow = false
        },

        async remove(subscriber) {
            const task = this.task ?? this.$page.props.task
            const { id } = subscriber
            await this.removeMember(
                task,
                id,
                this.options,
                () => (this.isShow = false),
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
