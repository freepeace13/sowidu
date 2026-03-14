<template>
    <v-card
        v-show="attachment.preview"
        class="attachment-preview ml-3"
    >
        <v-img
            v-if="attachment.preview && attachment.type == 'image'"
            width="60"
            height="60"
            :src="attachment.preview"
        />
        <embed
            v-if="attachment.type == 'application'"
            :src="attachment.preview"
            width="60"
            height="60"
        />
        <video
            v-if="attachment.type == 'video'"
            :src="attachment.preview"
            width="60"
            height="60"
            controls
        >
            {{ $t('chat.browser-videotag-not-supported') }}
        </video>
        <v-tooltip
            top
            color="red"
        >
            <template #activator="{ on }">
                <v-btn
                    color="primary"
                    small
                    fab
                    flat
                    style="position: absolute; top: 0; left: 54px"
                    v-on="on"
                    @click="$emit('clear')"
                >
                    <v-icon
                        size="14"
                        color="red lighten-3"
                    >
                        remove_circle
                    </v-icon>
                </v-btn>
            </template>
            <span>{{ $t('buttons.remove') }}</span>
        </v-tooltip>
    </v-card>
</template>
<script>
export default {
    props: {
        attachment: {
            required: true,
            type: Object,
        },
    },
}
</script>
