<template>
    <v-layout
        align-space-around
        justify-end
        column
        fill-height
        mb-2
        :class="[
            'message',
            {
                mine: isMine,
                their: !isMine,
            },
        ]"
    >
        <v-card>
            <v-tooltip
                :left="isMine"
                :right="!isMine"
            >
                <template #activator="{ on: onToolTip }">
                    <component
                        :is="attachmentComponent"
                        :key="`attachment_thumbnail_${message.id}`"
                        :data="message.data"
                        class="cursor-pointer"
                        v-on="onToolTip"
                        @click.native="$emit('view', message)"
                    />
                    <v-card-text
                        v-if="attachmentWithMessage || !message.is_attachment"
                        :class="{
                            'white--text blue': isMine,
                            white: !isMine,
                            'light-blue lighten-5 grey--text text--darken-4':
                                isEditing,
                        }"
                        v-on="onToolTip"
                    >
                        {{ message.body }}
                    </v-card-text>
                    <v-tooltip
                        top
                        color="light-blue"
                    >
                        <template #activator="{ on }">
                            <v-btn
                                v-if="isMine && !message.is_attachment"
                                v-show="!isEditing"
                                absolute
                                icon
                                dark
                                top
                                right
                                small
                                color="light-blue lighten-4"
                                class="action-button"
                                @click="edit"
                                v-on="on"
                            >
                                <v-icon
                                    size="14"
                                    color="black"
                                >
                                    edit
                                </v-icon>
                            </v-btn>
                        </template>
                        <span>Edit</span>
                    </v-tooltip>
                    <v-tooltip
                        top
                        color="red lighten-1"
                    >
                        <template #activator="{ on: onActivate }">
                            <v-btn
                                v-if="isMine"
                                v-show="!isEditing"
                                absolute
                                icon
                                dark
                                top
                                right
                                small
                                color="red lighten-5"
                                class="action-button delete"
                                v-on="onActivate"
                                @click="$emit('delete', message)"
                            >
                                <v-icon
                                    size="14"
                                    color="red"
                                >
                                    delete
                                </v-icon>
                            </v-btn>
                        </template>
                        <span>{{ $t('buttons.delete') }}</span>
                    </v-tooltip>
                </template>
                <span>{{ fullDate }}</span>
            </v-tooltip>
        </v-card>
        <div
            :class="[
                'caption grey--text lighten-1 mt-1',
                {
                    'text-xs-right': isMine,
                    'text-xs-left': !isMine,
                },
            ]"
        >
            <div
                :class="[
                    'details',
                    {
                        mine: isMine,
                        their: !isMine,
                    },
                ]"
            >
                <div>{{ senderName }}</div>
                <div class="time">{{ timeFromNow }}</div>

                <v-btn
                    v-if="isMine"
                    v-show="isEditing"
                    small
                    @click="cancelEdit"
                >
                    {{ $t('chat.cancel-edit') }}
                </v-btn>
            </div>
        </div>
    </v-layout>
</template>
<script>
import moment from 'moment'
import MessageWithImage from './MessageWithImage.vue'
import MessageWithPdf from './MessageWithPdf.vue'
import MessageWithVideo from './MessageWithVideo.vue'

export default {
    components: {
        MessageWithImage,
        MessageWithPdf,
        MessageWithVideo,
    },

    props: {
        message: {
            required: true,
            type: Object,
        },
    },

    data: () => ({
        isEditing: false,
    }),

    computed: {
        isMine() {
            return this.message.is_mine
        },

        senderName() {
            return this.isMine ? 'You' : this.message.sender?.name || 'Unknown'
        },

        createdAt() {
            return moment.utc(this.message.created_at).local()
        },

        timeFromNow() {
            return this.createdAt.fromNow()
        },

        fullDate() {
            return this.createdAt.format('llll')
        },

        attachmentWithMessage() {
            return (
                this.message.is_attachment &&
                this.message.body != 'Sent an attachment'
            )
        },

        attachmentComponent() {
            if (!this.message.is_attachment) return null
            return {
                image: 'MessageWithImage',
                pdf: 'MessageWithPdf',
                video: 'MessageWithVideo',
            }[this.message.data.type]
        },
    },

    watch: {
        message: {
            handler: function (val, oldVal) {
                if (val.updated_at != oldVal.updated_at) this.isEditing = false
            },
            deep: true,
        },
    },

    methods: {
        edit() {
            this.isEditing = true
            this.$emit('edit', this.message)
        },

        cancelEdit() {
            this.isEditing = false
            this.$emit('reset')
        },
    },
}
</script>
<style lang="scss" scoped>
.message {
    width: 80% !important;

    .action-button {
        width: 25px;
        height: 25px;
        top: -12px;
        z-index: 1;

        &.delete {
            right: -15px;
        }
    }

    &.mine {
        @apply tw-place-self-end;
    }
}

.details {
    display: flex;
    flex-direction: row;
    justify-content: space-between;

    &.mine {
        flex-direction: row-reverse;

        .time {
            justify-self: end;
        }
    }

    &.their {
        .time {
            justify-self: start;
        }
    }
}

.attachment {
    display: flex;
    justify-content: flex-end;
}
.cursor-pointer {
    cursor: pointer;
}
</style>
