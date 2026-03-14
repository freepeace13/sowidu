<template>
    <v-container fill-height>
        <ChatsList ref="chatsListRef" />
        <v-layout align-start>
            <v-flex>
                <h3 class="display-1">
                    {{ $t('chat.connect-with-your-team') }}
                </h3>

                <span class="tw-text-sm">
                    Lorem ipsum dolor sit amet, pri veniam forensibus id. Vis
                    maluisset molestiae id, ad semper lobortis cum. At impetus
                    detraxit incorrupte usu, repudiare assueverit ex eum, ne nam
                    essent vocent admodum.
                </span>

                <v-divider class="my-3" />

                <div class="tw-text-xl mb-3">
                    {{ $t('chat.start-conversation-now') }}
                </div>

                <v-btn
                    class="mx-0"
                    color="primary"
                    large
                    @click="$refs.chatsListRef.createConversation()"
                >
                    {{ $t('chat.search-user') }}
                </v-btn>
            </v-flex>
        </v-layout>
    </v-container>
</template>
<script>
import ChatListenerMixin from '~Chatly/Mixins/ChatListenerMixin'
import ChatsList from '~Chatly/Partials/ChatsList.vue'

export default {
    components: {
        ChatsList,
    },

    mixins: [ChatListenerMixin],
}
</script>
