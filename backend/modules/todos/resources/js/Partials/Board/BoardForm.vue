<template>
    <v-dialog
        v-model="isShow"
        persistent
        max-width="600px"
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ isEdit ? 'Update' : 'Create' }} board
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <v-card-text>
                <v-container
                    grid-list-lg
                    fluid
                    pa-2
                >
                    <v-layout column>
                        <v-flex>
                            <v-text-field
                                v-model="form.title"
                                color="primary"
                                :loading="form.processing"
                                :disabled="form.processing || isPredefined"
                                :error-messages="form.errors.title"
                                label="Title"
                                autofocus
                                outline
                                :hide-details="!form.errors.title"
                                class="required-input"
                            />
                        </v-flex>
                        <v-flex>
                            <v-textarea
                                v-model="form.description"
                                :loading="form.processing"
                                :disabled="form.processing"
                                color="primary"
                                label="Description"
                                outline
                                hide-details
                            />
                        </v-flex>
                        <v-flex>
                            <label class="body-2">Logo</label>
                            <div
                                class="tw-flex tw-flex-row tw-h-full tw-items-center"
                            >
                                <FileInputPreviewer
                                    :placeholder="form.logo ?? defaultBoardLogo"
                                    :attachment="attachment"
                                    :icon="boardIcon"
                                    img-class="border-primary"
                                    @clear="resetPreview"
                                />
                                <v-btn
                                    v-if="!boardIcon"
                                    small
                                    outline
                                    class="ml-3"
                                    @click="$refs.file.click()"
                                >
                                    Change Logo
                                </v-btn>
                                <input
                                    ref="file"
                                    type="file"
                                    accept="image/*"
                                    class="d-none"
                                    @change="attachingFile"
                                />
                            </div>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4">
                <v-btn
                    color="secondary"
                    depressed
                    :disabled="form.processing"
                    @click="close"
                >
                    Cancel
                </v-btn>
                <v-spacer />
                <v-btn
                    color="primary"
                    depressed
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="save"
                >
                    Save
                    <template #loader>
                        <span>Updating...</span>
                    </template>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import FileInputMixin from '@/Mixins/FileInputMixin'

export default {
    mixins: [FileInputMixin],

    data: (vm) => ({
        form: vm.$inertia.form({
            id: null,
            title: null,
            description: null,
            logo: null,
        }),
        isShow: false,
        isEdit: false,
        boardIcon: null,
        isPredefined: false,
    }),

    computed: {
        defaultBoardLogo() {
            return this.$page.props.defaults.avatars.board
        },
    },

    methods: {
        show(board = null) {
            this.reset()

            if (board) {
                const {
                    id,
                    title,
                    description,
                    logo,
                    is_icon,
                    icon_color,
                    is_predefined,
                } = board

                this.form.id = id
                this.form.title = title
                this.form.description = description
                this.form.logo = logo

                if (is_icon) {
                    this.boardIcon = {
                        color: icon_color,
                        name: logo,
                    }
                }

                this.isPredefined = is_predefined

                this.isEdit = true
            }

            this.isShow = true
        },

        close() {
            this.isShow = false
            this.reset()
        },

        reset() {
            this.form.reset()
            this.form.defaults({
                id: null,
                title: '',
                description: '',
                logo: null,
            })
            this.resetPreview()
            this.isEdit = false
            this.isPredefined = false
            this.boardIcon = null
        },

        createBoard() {
            const additionalData =
                this.hasAttachmentOnInput || this.isEdit
                    ? { logo: this.attachment.file }
                    : { logo: null }

            this.form.transform((data) => ({
                ...data,
                ...additionalData,
            }))

            this.$nextTick(() => {
                this.form.post(this.$route('todos.boards.store'), {
                    preserveScroll: true,
                    preserveState: true,
                    only: ['boards', 'errors'],
                    errorBag: 'createsBoard',
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            'Board has been created.',
                        )
                        this.isShow = false
                        this.reset()
                    },
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                })
            })
        },

        updateBoard() {
            const additionalData =
                this.hasAttachmentOnInput || this.isEdit
                    ? { logo: this.attachment.file, _method: 'PUT' }
                    : { logo: null }

            this.form.transform((data) => ({
                ...data,
                ...additionalData,
            }))

            this.$nextTick(() => {
                this.form.post(
                    this.$route('todos.boards.update', {
                        board: this.form.id,
                    }),
                    {
                        preserveScroll: true,
                        preserveState: true,
                        only: ['boards', 'errors'],
                        onSuccess: () => {
                            this.$root.$emit(
                                'flash.success',
                                'Board has been updated.',
                            )
                            this.isShow = false
                            this.reset()
                        },
                        onError: (errors) =>
                            this.$root.$emit('flash.validation', errors),
                    },
                )
            })
        },

        save() {
            this.$nextTick(() => {
                if (this.isEdit) {
                    this.updateBoard()
                    return
                }

                this.createBoard()
            })
        },
    },
}
</script>
<style>
.remove-preview-button {
    top: -32px;
    position: absolute;
    left: 54px;
}
</style>
