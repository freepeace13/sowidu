<template>
    <v-dialog
        v-model="isRemovingFile"
        max-width="450"
    >
        <v-card>
            <v-card-title class="title">
                {{ $t('headings.remove-media') }}
            </v-card-title>

            <v-card-text class="text-truncate">
                {{ $t('messages.confirm-file-remove') }}
                <b>{{ target.name }}</b>
                ?
            </v-card-text>

            <v-card-actions class="grey lighten-4">
                <v-spacer />

                <v-btn
                    flat
                    :disabled="processing"
                    @click="isRemovingFile = false"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>

                <v-btn
                    :disabled="processing"
                    :loading="processing"
                    color="primary"
                    depressed
                    @click="removeFile"
                >
                    {{ $t('buttons.remove') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    data: () => ({
        isRemovingFile: false,
        processing: false,
        target: {
            id: null,
            uuid: null,
            is_dir: false,
            name: null,
        },
    }),

    methods: {
        start(target) {
            this.target = target
            this.$nextTick(() => (this.isRemovingFile = true))
        },

        removeFile() {
            this.processing = true

            this.$inertia.delete(
                this.$route('media.destroy', { media: this.target.uuid }),
                {
                    only: ['folders', 'files', 'root_folders'],
                    onSuccess: () => {
                        this.$emit('success', this.target.id)
                        this.target = {
                            id: null,
                            uuid: null,
                            is_dir: false,
                            name: null,
                        }

                        this.isRemovingFile = false
                    },
                    onFinish: () => {
                        this.processing = false
                    },
                },
            )
        },
    },
}
</script>
