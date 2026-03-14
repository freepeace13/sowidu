<!-- eslint-disable vue/no-template-shadow -->
<script>
import FileUploader from '@/Components/FileUploader/FileUploader.vue'
import MediaDrawer from '@/Components/Media/MediaDrawer.vue'
import Http from '@/Modules/Http'
import axios from 'axios'
import { debounce, has, uniqBy } from 'lodash'
import AttachFileInput from '~Chatly/Components/AttachFileInput.vue'
import AttachmentPreviewer from '~Chatly/Components/AttachmentPreviewer.vue'
import ChatListenerMixin from '~Chatly/Mixins/ChatListenerMixin'
import AttachmentViewer from '~Chatly/Partials/Attachment/Viewer/AttachmentViewer.vue'
import ChatsList from '~Chatly/Partials/ChatsList.vue'
import DeleteMessageModal from '~Chatly/Partials/DeleteMessageModal.vue'
import Message from '~Chatly/Partials/Message/Message.vue'
import ReceiverInfo from '~Chatly/Partials/ReceiverInfo.vue'

export default {
    components: {
        ChatsList,
        Message,
        AttachFileInput,
        FileUploader,
        AttachmentViewer,
        AttachmentPreviewer,
        DeleteMessageModal,
        MediaDrawer,
        ReceiverInfo,
    },

    mixins: [ChatListenerMixin],

    props: {
        conversation: {
            required: true,
            type: Object,
        },

        user: {
            required: true,
            type: Object,
        },

        files: {
            required: false,
            type: Array,
            default: () => [],
        },
    },

    data: () => ({
        messages: [],
        message: {
            type: 'text',
            body: null,
        },
        pagination: {
            current_page: 0,
            last_page: 1,
            total: 0,
        },
        isFetching: false,
        attachment: {
            preview: null,
            files: null,
            type: null,
        },
    }),

    computed: {
        participants() {
            return this.conversation.participants
        },

        userId() {
            return this.user.id
        },

        conversationId() {
            return this.conversation.id
        },

        isLastPage() {
            return this.pagination.current_page == this.pagination.last_page
        },
    },

    mounted() {
        this.reloadMessages()
        this.$root.$on('chat-messages-reload', this.reloadMessages)
    },

    beforeDestroy() {
        this.$root.$off('chat-messages-reload', this.reloadMessages)
    },

    methods: {
        async send() {
            try {
                if (!this.message.body && !this.attachment.files) return

                let isCreating = true
                let url = this.$route('chatly.messages.store', {
                    conversation: this.conversationId,
                })
                const { message } = this

                if (has(message, 'id')) {
                    isCreating = false
                    url = this.$route('chatly.messages.update', {
                        conversation: this.conversationId,
                        message: this.message.id,
                    })
                }

                if (this.attachment.files) {
                    let query = {}
                    if (this.message.body) query = { body: this.message.body }
                    this.$refs.uploadFile.upload(url, this.attachment.files, {
                        query,
                        headers: {
                            Accept: 'application/json',
                        },
                    })
                } else {
                    const { data } = await axios({
                        method: isCreating ? 'post' : 'patch',
                        url: url,
                        data: { message },
                    })
                    if (isCreating) this.messages.push(data)
                    else
                        this.messages = this.messages.map((msg) =>
                            msg.id === data.id ? data : msg,
                        )
                }

                this.scrollDown()
            } catch ({
                response: {
                    data: { errors },
                },
            }) {
                Object.values(errors).forEach((bag) => {
                    bag.forEach((err) => this.$root.$emit('flash.error', err))
                })
            } finally {
                this.message.body = null
                this.attachment = {
                    preview: null,
                    files: null,
                    type: null,
                }
            }
        },

        async reloadMessages(page = 1) {
            try {
                this.isFetching = true
                const {
                    data: { items, ...pagination },
                } = await axios.get(
                    this.$route('chatly.messages.show', {
                        conversation: this.conversationId,
                        page: page,
                    }),
                )

                if (pagination.current_page == 1) this.messages = items
                else this.messages = uniqBy(items.concat(this.messages), 'id')

                this.pagination = pagination
                if (page == 1) this.scrollDown()
            } catch (error) {
                console.log(error)
            } finally {
                this.isFetching = false
            }
        },

        readMessage: debounce(function () {
            // mark this message as seen
            Http.patch(
                this.$route('chatly.messages.read', {
                    conversation: this.conversationId,
                }),
            )
                .then(() => this.$root.$emit('chats-list-refresh'))
                .catch(console.error)
        }, 500),

        loadMore() {
            this.reloadMessages(this.pagination.current_page + 1)
        },

        editMessage(message) {
            this.message = message
        },

        resetForm() {
            this.message = {
                type: 'text',
                body: null,
            }
        },

        attachingFile(files) {
            const file = files[0]
            const type = file.type.split('/')[0]
            this.attachment = {
                preview: (window.URL || window.webkitURL).createObjectURL(file),
                type,
                files,
            }
        },

        uploadFileSuccess(payload) {
            try {
                this.messages.push(JSON.parse(payload))
                this.scrollDown()
            } catch (error) {
                console.error(error)
            } finally {
                this.attachment.preview = null
                this.attachment.files = null
                this.message.body = null
            }
        },

        uploadFileError(payload) {
            try {
                const { errors } = JSON.parse(payload)
                Object.values(errors).forEach((bag) => {
                    bag.forEach((err) => this.$root.$emit('flash.error', err))
                })
            } catch (error) {
                console.error(error)
            }
        },

        viewAttachment(message) {
            this.$refs.attachmentViewer.viewMessageAttachment(message)
        },

        scrollDown() {
            setTimeout(() => {
                window.scrollTo(0, document.body.scrollHeight)
            }, 200)
        },

        async attachMedia(media) {
            try {
                const conversation = this.$route().params.id
                const media_id = media.id

                await axios.post(
                    this.$route('chatly.messages.store', {
                        conversation,
                    }),
                    {
                        media_id,
                    },
                )

                this.isShow = false
            } catch (error) {
                console.error(error)
            } finally {
                this.$root.$emit('chat-messages-reload')
            }
        },
    },
}
</script>

<template>
    <v-container
        fill-height
        pa-0
    >
        <ChatsList />

        <v-footer
            height="60"
            app
            inset
        >
            <v-text-field
                v-model="message.body"
                hide-details
                single-line
                solo
                :height="attachment.preview ? 160 : 60"
                :placeholder="$t('chat.type-message')"
                append-icon="send"
                loading
                @click:append="send"
                @keyup.enter="send"
            >
                <template #prepend-inner>
                    <attach-file-input
                        ref="fileInput"
                        @file-selected="attachingFile"
                        @show:media-drawer="$refs.mediaDrawer.show()"
                    />
                </template>
                <template #progress>
                    <attachment-previewer
                        :attachment="attachment"
                        @clear="
                            attachment = {
                                preview: null,
                                files: null,
                                type: null,
                            }
                        "
                    />
                </template>
            </v-text-field>
        </v-footer>

        <ReceiverInfo :participants="participants" />

        <v-container
            id="message-container"
            class="tw-flex tw-flex-col"
        >
            <div
                v-show="!isLastPage"
                class="separator mb-3"
            >
                <v-btn
                    round
                    small
                    :loading="isFetching"
                    @click="loadMore"
                >
                    {{ isFetching ? 'Loading' : 'Load more' }}
                </v-btn>
            </div>
            <Message
                v-for="message in messages"
                :key="message.id"
                :message="message"
                @edit="editMessage"
                @reset="resetForm"
                @delete="$refs.deleteMessageModal.open(message)"
                @view="viewAttachment"
            />
        </v-container>

        <DeleteMessageModal
            ref="deleteMessageModal"
            @message:delete="deleteMessage"
        />

        <FileUploader
            ref="uploadFile"
            :timeout="1000"
            @success="uploadFileSuccess"
            @error="uploadFileError"
        />

        <AttachmentViewer ref="attachmentViewer" />

        <MediaDrawer
            ref="mediaDrawer"
            right
            absolute
            style="z-index: 11"
            @attach="(media) => attachMedia(media)"
        />
    </v-container>
</template>

<style scoped lang="scss">
.receiver {
    margin-top: 64px !important;
    display: block;
    width: 100%;
    margin-left: 300px;

    &.dense {
        margin-left: 80px;
        margin-top: 48px !important;
    }
}

#message-container {
    margin-top: 64px;
}

.separator {
    display: flex;
    align-items: center;
    text-align: center;
}

.separator::before,
.separator::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #9e9e9e;
}

.separator:not(:empty)::before {
    margin-right: 0.25em;
}

.separator:not(:empty)::after {
    margin-left: 0.25em;
}
</style>
