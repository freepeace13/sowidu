<template>
    <v-alert
        :value="show"
        color="info"
        icon="info"
        outline
    >
        <div
            class="tw-flex tw-justify-between tw-items-center sm:tw-flex-nowrap tw-flex-wrap"
        >
            <div class="sm:tw-my-0 tw-my-2">
                {{ $t('messages.no-pending-invitations') }}
            </div>
            <v-btn
                color="primary"
                small
                :disabled="!allowsSendInvitation"
                class="ma-0"
                @click="onSendingInvitation"
            >
                {{ $t('buttons.send-invitation') }}
            </v-btn>
        </div>
    </v-alert>
</template>

<script>
export default {
    name: 'EmptyInvitations',

    props: {
        show: {
            type: Boolean,
            default: false,
        },
        allowsSendInvitation: {
            type: Boolean,
            default: false,
        },
    },

    methods: {
        onSendingInvitation() {
            this.$emit('sending-invitation')
        },
    },
}
</script>
