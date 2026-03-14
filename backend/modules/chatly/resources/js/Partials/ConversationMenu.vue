<template>
    <v-menu
        v-model="isShow"
        :position-x="x"
        :position-y="y"
        absolute
        :close-on-content-click="true"
    >
        <v-card>
            <v-list
                dense
                subheader
            >
                <v-subheader>{{ $t('chat.more-actions') }}</v-subheader>
                <v-list-tile
                    avatar
                    @click="destroy"
                >
                    <v-list-tile-avatar
                        :size="20"
                        style="min-width: 25px"
                    >
                        <v-icon small>delete</v-icon>
                    </v-list-tile-avatar>
                    <v-list-tile-title>{{
                        $t('buttons.delete')
                    }}</v-list-tile-title>
                </v-list-tile>
            </v-list>
        </v-card>
    </v-menu>
</template>
<script>
export default {
    data: () => ({
        isShow: false,
        x: 0,
        y: 0,
        conversation: null,
    }),

    watch: {
        isShow(val) {
            if (!val) {
                this.conversation = null
            }
        },
    },

    methods: {
        show(e, conversation) {
            e.preventDefault()
            this.conversation = conversation
            this.isShow = false
            this.x = e.clientX
            this.y = e.clientY
            this.$nextTick(() => {
                this.isShow = true
            })
        },

        destroy() {
            const { id } = this.conversation
            this.$confirm.ask({
                title: this.$t('buttons.delete'),
                question: this.$t('chat.confirm-delete-conversation'),
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('chatly.destroy', {
                            id,
                        }),
                        {
                            preserveState: true,
                            preserveScroll: true,
                            onSuccess: () => {
                                this.$root.$emit(
                                    'flash.success',
                                    this.$t('chat.conversation-deleted'),
                                )
                            },
                        },
                    )
                },
            })
        },
    },
}
</script>
