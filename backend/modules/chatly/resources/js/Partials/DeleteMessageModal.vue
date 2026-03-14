<template>
    <v-dialog
        v-model="show"
        max-width="290"
    >
        <v-card>
            <v-card-title class="headline white--text red lighten-1">
                {{ $t('buttons.delete') }}
            </v-card-title>

            <v-card-text>
                {{ $t('chat.confirm-delete-message') }}
            </v-card-text>

            <v-card-actions>
                <v-spacer />

                <v-btn
                    flat
                    @click="close"
                >
                    {{ $t('chat.no') }}
                </v-btn>

                <v-btn
                    color="error"
                    flat
                    @click="deleteMessage"
                >
                    {{ $t('chat.yes') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import axios from 'axios'

export default {
    data: () => ({
        show: false,
        message: null,
    }),

    methods: {
        open(message) {
            this.message = message
            this.show = true
        },

        close() {
            this.message = null
            this.show = false
        },

        async deleteMessage() {
            try {
                if (!this.message.id) this.close()
                axios.delete(
                    this.$route('chatly.messages.destroy', {
                        conversation: this.message.conversation_id,
                        message: this.message.id,
                    }),
                )
                this.$emit('message:delete', this.message)
                this.close()
            } catch (error) {
                console.error(error)
            } finally {
                this.close()
            }
        },
    },
}
</script>
