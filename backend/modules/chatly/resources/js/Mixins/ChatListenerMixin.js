export default {
    methods: {
        chatListener({ event, message }) {
            if (message.sender.id == this.userId) return

            if (event == 'MessageSent') {
                let messageText = `You have new message from ${message.sender.name}`
                this.$root.$emit('flash', {
                    type: 'chat',
                    message: messageText,
                })
                this.newMessage(message)
            }

            if (event == 'MessageUpdated') this.updateMessage(message)
            if (event == 'MessageDeleted') this.deleteMessage(message)
        },

        async newMessage(message) {
            if (this.conversationId == message.conversation_id) {
                if (message.sender.id != this.userId) {
                    if (!this.messages.find((m) => m.id == message.id)) {
                        message.is_mine = false
                        this.messages.push(message)
                        await this.readMessage()
                    }
                }
            } else {
                this.$root.$emit('chats-list-refresh')
            }
        },

        updateMessage(message) {
            if (this.conversationId == message.conversation_id) {
                if (message.sender.id != this.userId) {
                    message.is_mine = false
                    this.messages = this.messages.map((msg) =>
                        msg.id === message.id ? message : msg,
                    )
                }
            } else {
                this.$root.$emit('chats-list-refresh')
            }
        },

        deleteMessage({ id }) {
            this.messages = this.messages.filter((msg) => msg.id != id)
        },
    },

    mounted() {
        this.$root.$on('chat-listener', this.chatListener)
    },

    beforeDestroy() {
        this.$root.$off('chat-listener', this.chatListener)
    },
}
