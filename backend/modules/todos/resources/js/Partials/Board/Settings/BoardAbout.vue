<template>
    <div>
        <v-list dense>
            <v-list-tile class="mt-1">
                <v-list-tile-content>
                    <v-list-tile-title
                        class="grey--text text--darken-2 font-weight-bold body-1"
                    >
                        Board Admin
                    </v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
        <v-container
            grid-list-xl
            pt-2
        >
            <v-layout column>
                <v-layout
                    row
                    pa-2
                >
                    <v-flex shrink>
                        <v-avatar
                            size="48"
                            tile
                            color="primary"
                        >
                            <img
                                :src="owner?.photo"
                                :alt="owner?.name"
                            />
                        </v-avatar>
                    </v-flex>
                    <v-flex>
                        <v-layout
                            column
                            wrap
                        >
                            <v-flex>
                                <div
                                    class="font-weight-bold body-2"
                                    v-text="owner?.name"
                                />
                                <div
                                    class="grey--text body-1"
                                    v-text="owner?.email"
                                />
                            </v-flex>
                        </v-layout>
                    </v-flex>
                </v-layout>
            </v-layout>
        </v-container>
        <v-list dense>
            <v-list-tile class="mt-1">
                <v-list-tile-content>
                    <v-list-tile-title
                        class="grey--text text--darken-2 font-weight-bold body-1"
                    >
                        Description
                    </v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
        <v-card
            ref="descriptionForm"
            class="cursor-pointer mx-3"
            tile
            flat
            hover
            color="grey lighten-4"
        >
            <v-card-text @click="isShowForm = true">
                <div
                    v-show="!isShowForm"
                    class="body-1"
                    v-text="details?.description ?? descriptionPlaceholder"
                />
                <v-textarea
                    v-show="isShowForm"
                    ref="descriptionInput"
                    v-model="form.description"
                    tabindex="0"
                    :placeholder="descriptionPlaceholder"
                    single-line
                    full-width
                    hide-details
                    class="grey lighten-4 body-1"
                    :outline="isShowForm"
                    :loading="form.processing"
                />
            </v-card-text>
            <v-card-actions v-show="isShowForm">
                <v-spacer />
                <v-btn
                    small
                    depressed
                    color="secondary"
                    :loading="form.processing"
                    @click="closeForm"
                >
                    Cancel
                </v-btn>
                <v-btn
                    small
                    depressed
                    color="primary"
                    :loading="form.processing"
                    @click="submit"
                >
                    Save
                    <template #loader>
                        <span>Saving...</span>
                    </template>
                </v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>
<script>
import { onClickOutside } from '@vueuse/core'

export default {
    props: {
        details: {
            type: Object,
            default: () => ({}),
            required: false,
        },
    },

    data: (vm) => ({
        isShowForm: false,
        form: vm.$inertia.form({
            description: '',
        }),
    }),

    computed: {
        owner() {
            return this.details?.owner
        },

        descriptionPlaceholder() {
            return [
                'Let people know what this board is',
                'used for and what they can expect to see.',
            ].join(' ')
        },
    },

    watch: {
        isShowForm(val) {
            if (val) {
                this.$nextTick(() => {
                    this.$refs.descriptionInput.focus()
                })
                this.form.description = this.details?.description
            }
        },
    },

    mounted() {
        if (!this.details) this.$inertia.reload({ only: ['details'] })

        onClickOutside(this.$refs.descriptionForm, () => this.closeForm())
    },

    methods: {
        submit() {
            const board = this.$page.props.board.id
            this.form.patch(this.$route('todos.boards.update', { board }), {
                preserveState: true,
                preserveScroll: true,
                only: ['details', 'errors'],
                onSuccess: () => {
                    this.$root.$emit(
                        'flash.success',
                        'Board description has been updated.',
                    )
                    this.isShowForm = false
                },
                onError: (errors) => {
                    this.$root.$emit('flash.validation', errors)
                },
                onFinish: () => {
                    this.isShowForm = false
                    this.$root.$emit('activities.refresh')
                },
            })
        },

        closeForm(resetForm = true) {
            this.isShowForm = false
            if (resetForm) this.form.reset()
        },
    },
}
</script>
