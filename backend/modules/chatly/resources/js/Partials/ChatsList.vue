<template>
    <v-navigation-drawer
        ref="messageNavigation"
        permanent
        fixed
        app
        :mini-variant="$vuetify.breakpoint.smAndDown"
        :class="{
            'mt-5': $vuetify.breakpoint.smAndDown,
            'mt-6': $vuetify.breakpoint.mdAndUp,
        }"
    >
        <v-toolbar
            flat
            class="transparent"
        >
            <v-list
                :class="{
                    'pt-0 tw-border-b tw-border-b-grey-500 tw-border-solid tw-border-t-0 tw-border-x-0':
                        $vuetify.breakpoint.smAndDown,
                    'pt-2': $vuetify.breakpoint.smAndUp,
                }"
            >
                <v-list-tile
                    avatar
                    class="tw-border tw-border-gray-500"
                >
                    <v-list-tile-avatar v-if="$vuetify.breakpoint.smAndDown">
                        <v-icon>question_answer</v-icon>
                    </v-list-tile-avatar>

                    <v-list-tile-content>
                        <v-list-tile-title class="font-weight-bold title">
                            {{ $t('chat.messages') }}
                        </v-list-tile-title>
                    </v-list-tile-content>
                    <v-list-tile-action>
                        <v-btn icon>
                            <v-icon @click="createConversation">
                                add_circle_outline
                            </v-icon>
                        </v-btn>
                    </v-list-tile-action>
                </v-list-tile>
            </v-list>
        </v-toolbar>

        <v-list
            v-if="$vuetify.breakpoint.mdAndUp"
            class="pa-0"
            :three-line="$vuetify.breakpoint.mdAndUp"
        >
            <v-divider />
            <v-list-tile
                avatar
                px-0
            >
                <v-list-tile-avatar v-if="$vuetify.breakpoint.smAndDown">
                    <v-icon icon="search" />
                </v-list-tile-avatar>
                <v-list-tile-action v-if="$vuetify.breakpoint.mdAndUp">
                    <SearchField class="w-full" />
                </v-list-tile-action>
            </v-list-tile>
            <v-divider />
        </v-list>

        <v-list
            class="pa-0 conversation-container"
            two-line
            dense
        >
            <Conversation
                v-for="conversation in conversations"
                :key="`chat-${conversation.id}`"
                :conversation="conversation"
                class="conversation"
                @click:more="
                    (e) => $refs.conversationMenuRef.show(e, conversation)
                "
            />
        </v-list>

        <CreateConversation ref="createConversation" />
        <ConversationMenu ref="conversationMenuRef" />
    </v-navigation-drawer>
</template>

<script>
import axios from 'axios'
import Conversation from './Conversation.vue'
import ConversationMenu from './ConversationMenu.vue'
import CreateConversation from './CreateConversation.vue'
import SearchField from './SearchField.vue'

export default {
    components: {
        SearchField,
        CreateConversation,
        Conversation,
        ConversationMenu,
    },

    data: () => ({
        isShowCreateConversationModal: false,
        conversations: [],
    }),

    mounted() {
        this.fetchConversations()
        this.$root.$on('chats-list-refresh', this.fetchConversations)
    },

    beforeDestroy() {
        this.$root.$off('chats-list-refresh', this.fetchConversations)
    },

    methods: {
        async fetchConversations() {
            const { data } = await axios.get(this.$route('chatly.index'))
            this.conversations = data
        },

        createConversation() {
            this.$refs.createConversation.show()
        },
    },
}
</script>
<style lang="scss" scoped>
.conversation-container {
    .conversation {
        border-bottom: 1px solid #bdbdbd;
    }
}
</style>
