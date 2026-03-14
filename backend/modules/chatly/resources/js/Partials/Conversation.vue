<template>
    <v-list-tile
        avatar
        :class="{
            'blue lighten-4': $route().current('chatly.show', {
                id: conversation.id,
            }),
        }"
        @click="
            $inertia.get(
                $route('chatly.show', conversation.id),
                {},
                { preserveState: true },
            )
        "
    >
        <v-badge
            left
            overlap
            color="red lighten-1"
            :value="conversation.unread_count"
        >
            <template #badge>
                <span class="caption">{{ conversation.unread_count }}</span>
            </template>
            <v-list-tile-avatar>
                <v-icon
                    v-if="participants.length > 2"
                    x-large
                >
                    groups
                </v-icon>
                <template v-else>
                    <img
                        v-for="participant in conversation.participants"
                        :key="`participant-avatar-${participant.id}`"
                        :src="
                            !participant.organization
                                ? participant.photo
                                : participant.organization.photo
                        "
                        class="avatar"
                        width="26"
                    />
                </template>
            </v-list-tile-avatar>
        </v-badge>

        <v-list-tile-content>
            <v-list-tile-title class="justify-space-between d-flex">
                <div class="self-centers">
                    {{ participantNames }}
                    <span
                        class="font-weight-bold grey--text"
                        v-text="companyName"
                    />
                </div>

                <v-icon
                    color="blue"
                    size="10px"
                    class="flex-none"
                >
                    circle
                </v-icon>
            </v-list-tile-title>
            <v-list-tile-sub-title
                :class="{
                    'font-weight-black grey--text text--darken-4':
                        !conversation.last_message.is_seen,
                }"
            >
                {{ lastMessage.body }}
            </v-list-tile-sub-title>
        </v-list-tile-content>
        <v-list-tile-action>
            <v-list-tile-action-text
                class="text-truncate details hidden-sm-and-down"
            >
                {{ timeFromNow }}
            </v-list-tile-action-text>
            <v-btn
                icon
                @click.stop="(e) => $emit('click:more', e)"
            >
                <v-icon>more_vert</v-icon>
            </v-btn>
        </v-list-tile-action>
    </v-list-tile>
</template>
<script>
import moment from 'moment'
import ConversationMixin from '../Mixins/ConversationMixin'

export default {
    mixins: [ConversationMixin],

    props: {
        conversation: {
            required: true,
            type: Object,
        },
    },

    data: () => ({
        isShowMenu: false,
    }),

    computed: {
        lastMessage() {
            return this.conversation.last_message
        },

        timeFromNow() {
            return moment.utc(this.lastMessage.created_at).local().fromNow()
        },

        fullDate() {
            return moment
                .utc(this.lastMessage.created_at)
                .local()
                .format('llll')
        },

        isGroupMessage() {
            return this.participants.length > 2
        },

        avatar() {
            let url = this.participants[0].photo
            if (this.isGroupMessage && this.lastMessage.length) {
                url = this.lastMessage.sender.photo
            }
            return url
        },
    },
}
</script>
<style lang="scss" scoped>
.flex-none {
    flex: none !important;
}

.details {
    max-width: 50px;
}
</style>
