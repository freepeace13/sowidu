<template>
    <v-toolbar
        fixed
        floating
        top
        scroll-off-screen
        scroll-target="#message-container"
        :class="[
            'receiver',
            {
                dense: $vuetify.breakpoint.smAndDown,
            },
        ]"
        color="white"
    >
        <v-list class="py-0">
            <v-list-tile>
                <v-list-tile-avatar v-if="participants.length > 2">
                    <v-icon x-large>groups</v-icon>
                </v-list-tile-avatar>
                <template v-else>
                    <v-list-tile-avatar
                        v-for="participant in participants"
                        :key="participant.id"
                    >
                        <v-img :src="participant.photo" />
                    </v-list-tile-avatar>
                </template>
                <v-list-tile-content>
                    <v-list-tile-title>
                        {{ participantsNames }}
                    </v-list-tile-title>
                    <v-list-tile-sub-title>
                        <v-icon
                            color="green"
                            size="14px"
                        >
                            circle
                        </v-icon>
                        {{ $t('chat.active') }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
    </v-toolbar>
</template>
<script>
export default {
    props: {
        participants: {
            type: Array,
            required: true,
        },
    },

    computed: {
        participantsNames() {
            return this.participants.map((p) => p.name).join(', ')
        },
    },
}
</script>
