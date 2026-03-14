<template>
    <v-list-tile
        avatar
        px-0
    >
        <v-list-tile-avatar size="30">
            <v-icon
                v-if="participants.length > 2"
                x-large
            >
                groups
            </v-icon>
            <template v-else>
                <img
                    v-for="participant in participants"
                    :key="`participant-avatar-${participant.id}`"
                    :src="
                        !participant.organization
                            ? participant.photo
                            : participant.organization.photo
                    "
                />
            </template>
        </v-list-tile-avatar>
        <v-list-tile-content>
            <v-list-tile-title>
                {{ participantNames }}
                <span
                    class="font-weight-bold grey--text text-capitalize"
                    v-text="companyName"
                />
            </v-list-tile-title>
        </v-list-tile-content>
        <v-list-tile-action>
            <v-btn
                color="primary"
                depressed
                small
                :loading="isLoading"
                :disabled="sent"
                @click="send"
            >
                <v-icon
                    v-show="sent"
                    small
                    class="mr-2"
                >
                    done
                </v-icon>
                {{ $t(`buttons.${sent ? 'sent' : 'send'}`) }}
            </v-btn>
        </v-list-tile-action>
    </v-list-tile>
</template>
<script>
import axios from 'axios'
import ConversationMixin from '../Mixins/ConversationMixin'

export default {
    mixins: [ConversationMixin],

    props: {
        conversation: {
            type: Object,
            required: true,
        },

        media: {
            type: Object,
            required: true,
        },
    },

    data: () => ({
        isLoading: false,
        sent: false,
    }),

    methods: {
        async send() {
            try {
                this.isLoading = true

                await axios.post(
                    this.$route('chatly.messages.store', {
                        conversation: this.conversation.id,
                    }),
                    {
                        media_id: this.media.id,
                    },
                )
                this.sent = true
            } catch ({ response: { data } }) {
                this.$root.$emit('flash.error', data.message)
            } finally {
                this.isLoading = false
            }
        },
    },
}
</script>
