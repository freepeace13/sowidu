<template>
    <v-menu
        v-model="isShow"
        :position-x="x"
        :position-y="y"
        absolute
        offset-y
        :z-index="zIndex"
    >
        <v-card>
            <v-img
                :src="user?.photo"
                height="150px"
                contain
            />
            <v-divider />
            <v-list
                two-line
                dense
            >
                <v-subheader class="subheading">{{ user?.name }}</v-subheader>
                <v-divider />
                <v-list-tile>
                    <v-list-tile-action>
                        <v-icon color="primary"> mail </v-icon>
                    </v-list-tile-action>

                    <v-list-tile-content>
                        <v-list-tile-title>
                            {{ user?.email }}
                        </v-list-tile-title>
                        <v-list-tile-sub-title>Email</v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>

                <v-divider />

                <v-list-tile v-show="isLoading">
                    <v-progress-linear :indeterminate="true" />
                </v-list-tile>
                <v-list-tile v-show="!isLoading && additionalDetails">
                    <v-list-tile-action>
                        <v-icon color="indigo">location_on</v-icon>
                    </v-list-tile-action>

                    <v-list-tile-content>
                        <v-list-tile-title>{{ address }}</v-list-tile-title>
                        <v-list-tile-sub-title>
                            {{ addressCountryAndCode }}
                        </v-list-tile-sub-title>
                    </v-list-tile-content>
                </v-list-tile>

                <v-divider />

                <v-list-tile @click="removeUser">
                    <v-list-tile-action>
                        <v-icon color="red">delete</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>
                            {{ removeMessage }}
                        </v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-card>
    </v-menu>
</template>
<script>
import axios from 'axios'
import useRemoveNullOnArray from '@/Composables/useRemoveNullOnArray'
import TaskMemberMixin from '../../Mixins/TaskMemberMixin'
import BoardSubscriberMixin from '../../Mixins/BoardSubscriberMixin'

export default {
    mixins: [TaskMemberMixin, BoardSubscriberMixin],

    props: {
        zIndex: {
            type: Number,
            default: 11,
        },
    },
    data: () => ({
        isShow: false,
        x: 0,
        y: 0,
        subscriber: null,
        additionalDetails: null,
        isLoading: false,
        removeMessage: null,
    }),

    computed: {
        user() {
            return this.subscriber?.user
        },

        addressCountryAndCode() {
            if (!this.additionalDetails) return
            const { city, full, house_number, state, ...data } =
                this.additionalDetails.address
            return useRemoveNullOnArray(data).join(', ')
        },

        address() {
            if (!this.additionalDetails) return
            const { full, zipcode, country, ...data } =
                this.additionalDetails.address
            return useRemoveNullOnArray(data).join(', ')
        },
    },

    watch: {
        isShow(val) {
            if (!val) {
                this.subscriber = null
                this.additionalDetails = null
            }

            if (val) {
                const { task } = this.$page.props
                this.removeMessage = `Remove from ${!task ? 'board' : 'task'}`
            }
        },
    },

    methods: {
        show(e, subscriber) {
            this.subscriber = null

            e.preventDefault()

            this.isShow = false
            this.x = e.clientX
            this.y = e.clientY

            this.subscriber = subscriber
            this.$nextTick(() => {
                this.isShow = true
            })

            this.fetchDetails()
        },

        async fetchDetails() {
            try {
                this.isLoading = true
                const { data } = await axios.get(
                    this.$route('todos.users.show', {
                        user: this.subscriber.user.id,
                    }),
                )
                this.additionalDetails = data
            } catch (error) {
                this.$root.$emit('flash.error', error)
            } finally {
                this.isLoading = false
            }
        },

        removeUser() {
            const task = this.task
            const subscriber = this.subscriber

            if (task) {
                // Remove this user from the `task`
                this.removeMember(task, subscriber, {
                    reload: ['taskMembers'],
                })
                return
            }

            // Remove user from board
            this.removeBoardSubscriber(subscriber)
        },
    },
}
</script>
