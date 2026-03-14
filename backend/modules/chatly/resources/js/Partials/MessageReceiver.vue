<template>
    <v-list-tile
        avatar
        px-0
    >
        <v-list-tile-avatar size="30">
            <img :src="receiver.photo" />
        </v-list-tile-avatar>

        <v-list-tile-content>
            <v-list-tile-title>
                {{ receiver.name }}
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
                    >done</v-icon
                >
                {{ $t(`buttons.${sent ? 'sent' : 'send'}`) }}
            </v-btn>
        </v-list-tile-action>
    </v-list-tile>
</template>
<script>
import axios from 'axios'

export default {
    props: {
        receiver: {
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

    computed: {
        companyName() {
            return !this.receiver.is_user ? `(${this.receiver.role})` : ''
        },
    },

    methods: {
        async send() {
            try {
                this.isLoading = true
                await axios.post(this.$route('chatly.store'), {
                    participants: [this.receiver],
                    media_id: this.media.id,
                })
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
