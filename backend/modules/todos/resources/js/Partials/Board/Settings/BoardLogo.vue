<template>
    <div>
        <v-list dense>
            <v-list-tile class="mt-1">
                <v-list-tile-content>
                    <v-list-tile-title
                        class="grey--text text--darken-2 font-weight-bold body-1"
                    >
                        Board logo
                    </v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
        <v-container
            grid-list-xs
            fluid
            fill-height
            px-3
            pt-0
            pb-3
            class=""
        >
            <div
                class="tw-flex tw-flex-col tw-w-full tw-items-center tw-justify-center tw-gap-y-2"
            >
                <FileInputPreviewer
                    :placeholder="form.logo ?? defaultBoardLogo"
                    :width="120"
                    :height="120"
                    :attachment="attachment"
                    :icon="boardIcon"
                    img-class="border-primary"
                    @clear="resetPreview"
                />
                <v-btn
                    v-if="!details.is_icon"
                    v-show="!hasAttachmentOnInput"
                    color="primary"
                    @click="$refs.file.click()"
                >
                    Change Logo
                    <v-icon
                        right
                        dark
                    >
                        cloud_upload
                    </v-icon>
                </v-btn>
                <v-btn
                    v-show="hasAttachmentOnInput"
                    color="info"
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="updateLogo"
                >
                    Save Logo
                    <template #loader>
                        <span>Saving...</span>
                    </template>
                </v-btn>
                <input
                    ref="file"
                    type="file"
                    accept="image/*"
                    class="d-none"
                    @change="attachingFile"
                />
            </div>
        </v-container>
        <v-divider />
    </div>
</template>
<script>
import FileInputMixin from '@/Mixins/FileInputMixin'

export default {
    mixins: [FileInputMixin],

    props: {
        details: {
            type: Object,
            required: false,
        },
    },

    data: (vm) => ({
        form: vm.$inertia.form({
            logo: vm.details.logo,
        }),
    }),

    computed: {
        defaultBoardLogo() {
            return this.$page.props.defaults.avatar.board
        },

        boardIcon() {
            if (!this.details.is_icon) return null
            return { name: this.details.logo, color: this.details.icon_color }
        },
    },

    methods: {
        updateLogo() {
            this.form
                .transform((data) => ({
                    ...data,
                    logo: this.attachment.file,
                    _method: 'put',
                }))
                .post(
                    this.$route('todos.boards.update', {
                        board: this.details,
                    }),
                    {
                        preserveScroll: true,
                        preserveState: true,
                        only: ['board', 'details', 'errors'],
                        onSuccess: () => {
                            this.$root.$emit(
                                'flash.success',
                                'Board logo has been updated.',
                            )
                        },
                        onError: (errors) =>
                            this.$root.$emit('flash.validation', errors),
                        onFinish: () => {
                            this.$root.$emit('activities.refresh')
                        },
                    },
                )
        },
    },
}
</script>
