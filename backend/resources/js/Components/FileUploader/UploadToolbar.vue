<template>
    <v-toolbar
        color="grey darken-4"
        dark
        card
    >
        <v-toolbar-title class="subheading">
            <slot />
        </v-toolbar-title>

        <v-toolbar-items class="ml-auto">
            <v-btn
                icon
                @click="$emit('update:expand', !expand)"
            >
                <v-icon>{{ expand ? 'expand_more' : 'expand_less' }}</v-icon>
            </v-btn>

            <confirmation-dialog
                v-if="confirmOnClose"
                :confirm-text="$t('media.buttons.cancel-upload')"
                :cancel-text="$t('media.buttons.continue-uploading')"
                @confirm="$emit('cancel')"
            >
                <template #activator="{ on }">
                    <v-btn
                        icon
                        v-on="on"
                    >
                        <v-icon>close</v-icon>
                    </v-btn>
                </template>

                <template #header>
                    <h3>{{ $t('media.message.cancel-upload?') }}</h3>
                </template>

                <template #body>
                    {{ $t('media.message.cancel-upload-confirm-message') }}
                </template>
            </confirmation-dialog>

            <v-btn
                v-else
                icon
                @click="$emit('close')"
            >
                <v-icon>close</v-icon>
            </v-btn>
        </v-toolbar-items>
    </v-toolbar>
</template>

<script>
import ConfirmationDialog from '../ConfirmationDialog.vue'

export default {
    components: {
        ConfirmationDialog,
    },
    props: {
        expand: {
            type: Boolean,
            required: true,
        },

        confirmOnClose: {
            type: Boolean,
            required: true,
        },
    },
}
</script>
