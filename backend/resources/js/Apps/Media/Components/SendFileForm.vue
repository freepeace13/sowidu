<template>
    <v-dialog
        v-model="isShow"
        max-width="500"
        :persistent="true"
    >
        <v-card v-if="isShow">
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>{{
                    $t('headings.send-media')
                }}</v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="isShow = false"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-container
                fluid
                grid-list-lg
            >
                <v-layout
                    row
                    wrap
                >
                    <v-flex xs12>
                        <v-card>
                            <v-img
                                class="white--text"
                                height="200px"
                                :src="media.conversions.thumbnail"
                            />
                            <v-card-title>
                                <v-layout
                                    align-start
                                    justify-center
                                    column
                                >
                                    <span
                                        class="subheading"
                                        v-text="media.file_name"
                                    />
                                    <span
                                        class="caption grey--text"
                                        v-text="media.size"
                                    />
                                    <span
                                        class="caption grey--text"
                                        v-text="media.mime_type"
                                    />
                                </v-layout>
                            </v-card-title>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
            <v-divider />

            <v-card-text>
                <v-container
                    grid-list-md
                    px-2
                    py-0
                >
                    <v-layout wrap>
                        <v-flex xs12>
                            <v-text-field
                                v-model="keyword"
                                :label="$t('hints.search-for-people')"
                                prepend-icon="search"
                            />
                        </v-flex>
                    </v-layout>
                </v-container>
                <v-list class="">
                    <div
                        v-show="!results.length && recent.conversations.length"
                        id="recent-conversation-list"
                        style="max-height: 247px; overflow: auto"
                    >
                        <v-subheader>{{
                            $t('labels.recent-chat')
                        }}</v-subheader>
                        <recent-conversation
                            v-for="conversation in recent.conversations"
                            :key="`conversation-${conversation.id}`"
                            :conversation="conversation"
                            :media="media"
                        />
                        <v-list-tile
                            v-show="isLoading"
                            class="w-full justify-content-center"
                        >
                            <v-progress-circular
                                indeterminate
                                color="primary"
                            />
                        </v-list-tile>
                    </div>
                    <div v-show="results.length">
                        <v-list-group
                            v-for="group in results"
                            :key="`group-${group.header}`"
                            v-model="group.active"
                            :prepend-icon="group.icon"
                            no-action
                        >
                            <template #activator>
                                <v-list-tile avatar>
                                    <v-list-tile-avatar
                                        v-if="group.thumbnail"
                                        size="30"
                                    >
                                        <img :src="group.thumbnail" />
                                    </v-list-tile-avatar>
                                    <v-list-tile-content>
                                        <v-list-tile-title
                                            class="v-subheader theme--light px-0"
                                        >
                                            {{ group.header }}
                                        </v-list-tile-title>
                                    </v-list-tile-content>
                                </v-list-tile>
                            </template>
                            <message-receiver
                                v-for="receiver in group.items"
                                :key="`receiver-${receiver.id}`"
                                :receiver="receiver"
                                :media="media"
                            />
                        </v-list-group>
                    </div>
                </v-list>
            </v-card-text>

            <v-divider />
            <v-card-actions class="text-lg-right grey lighten-4">
                <v-spacer />
                <v-btn
                    flat
                    @click="isShow = false"
                >
                    {{ $t('buttons.done') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import axios from 'axios'
import { debounce } from 'lodash'
import MessageReceiver from '~Chatly/Partials/MessageReceiver.vue'
import RecentConversation from '~Chatly/Partials/RecentConversation.vue'

export default {
    components: { MessageReceiver, RecentConversation },

    data: () => ({
        keyword: '',
        isShow: false,
        media: null,
        results: [],
        isLoading: true,
        receivers: [],
        recent: {
            conversations: [],
            page: 1,
            isLast: false,
        },
    }),

    watch: {
        keyword(value) {
            if (!value) {
                this.results = []
                return
            }

            this.searchUsers({ keyword: value })
        },
    },

    mounted() {
        this.searchUsers = debounce(async (params) => {
            try {
                this.results = []
                this.isLoading = true

                const { data } = await axios.get(
                    this.$route('chat.search', params),
                )
                const rawItems = [
                    {
                        header: 'People',
                        id: 'people',
                        items: [...data.people],
                        active: true,
                        icon: 'people',
                    },
                    ...Object.keys(data.groups).map((group) => ({
                        header: group,
                        id: `group-${group}`,
                        thumbnail: data.groups[group][0].photo,
                        items: [...data.groups[group]],
                    })),
                ]

                this.results = rawItems.flat()
            } catch (error) {
                console.error(error)
            } finally {
                this.isLoading = false
            }
        }, 500)
    },

    methods: {
        show(media) {
            this.media = media
            this.isShow = true
            this.keyword = ''
            this.fetchRecentConversations()

            this.$nextTick(() => this.addInfiniteScroll())
        },

        addInfiniteScroll() {
            const list = document.querySelector('#recent-conversation-list')
            list.addEventListener('scroll', () => {
                if (list.scrollTop + list.clientHeight >= list.scrollHeight) {
                    this.fetchRecentConversations(
                        this.recentConversationPage + 1,
                    )
                }
            })
        },

        async fetchRecentConversations(page = 1) {
            try {
                if (this.recent.isLast) return

                this.results = []
                if (page == 1) this.recent.conversations = []
                this.isLoading = true

                this.recentConversationPage = page

                const { data } = await axios.get(
                    this.$route('chatly.index', { limit: 5, page }),
                )

                if (!data.length) this.recent.isLast = true
                if (page == 1) this.recent.conversations = data
                else
                    this.recent.conversations =
                        this.recent.conversations.concat(data)
            } catch (error) {
                console.error(error)
            } finally {
                this.isLoading = false
            }
        },
    },
}
</script>
<style>
.justify-content-center .v-list__tile {
    justify-content: center !important;
}

.subheading {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: 0.3scubic-bezier (0.25, 0.8, 0.5, 1);
    width: 100%;
}
</style>
