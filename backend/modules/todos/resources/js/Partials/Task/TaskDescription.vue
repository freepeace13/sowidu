<template>
    <v-layout
        column
        justify-start
    >
        <v-flex class="font-weight-bold title mb-1"> Description </v-flex>
        <v-flex>
            <v-card
                ref="description"
                hover
                class="cursor-pointer"
                color="grey lighten-4"
            >
                <v-card-text>
                    <div
                        v-show="!isEditing"
                        :class="{
                            'grey--text text--darken-1': !form.description,
                        }"
                        @click="showForm"
                    >
                        {{
                            description ??
                            'Add more detailed description here...'
                        }}
                    </div>
                    <v-textarea
                        v-show="isEditing"
                        ref="descriptionInput"
                        v-model="form.description"
                        :outline="isEditing"
                        :loading="form.processing"
                        :disabled="form.processing"
                        tabindex="0"
                        placeholder="Add more detailed description here..."
                        value=""
                        single-line
                        full-width
                        hide-details
                        class="grey lighten-4"
                        @focus="showForm"
                    />
                </v-card-text>
                <v-card-actions v-show="isEditing">
                    <v-spacer />
                    <v-btn
                        :disabled="form.processing"
                        small
                        depressed
                        color="secondary"
                        @click="isEditing = false"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        :loading="form.processing"
                        :disabled="form.processing"
                        small
                        depressed
                        color="primary"
                        @click="update"
                    >
                        Save
                        <template #loader>
                            <span>Saving...</span>
                        </template>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-flex>
    </v-layout>
</template>
<script>
import { onClickOutside } from '@vueuse/core'
import { socketIdHeader } from '@/Composables/useSocketId'

export default {
    props: {
        description: {
            type: String,
            default: null,
        },
    },
    data: (vm) => ({
        isEditing: false,
        form: vm.$inertia.form({
            description: vm.description,
        }),
    }),

    watch: {
        isEditing(val) {
            if (!val) this.update()
        },
    },

    mounted() {
        onClickOutside(this.$refs.description, () => {
            if (this.isEditing) this.isEditing = false
        })
    },

    methods: {
        showForm() {
            this.isEditing = true
            this.$nextTick(() => {
                this.$refs.descriptionInput.focus()
            })
        },

        update() {
            if (!this.form.description) return

            const { board, task } = this.$page.props
            this.form.patch(
                this.$route('todos.boards.tasks.update', {
                    task,
                    board,
                }),
                {
                    ...socketIdHeader,
                    preserveState: true,
                    preserveScroll: true,
                    only: ['task', 'errors'],
                    errorBag: 'updateBoardTask',
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                    onSuccess: () => {
                        this.isEditing = false
                        this.$root.$emit('task_activities.refresh')
                    },
                },
            )
        },
    },
}
</script>
