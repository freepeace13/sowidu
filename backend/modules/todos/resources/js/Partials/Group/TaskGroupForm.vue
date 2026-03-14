<template>
    <v-flex
        ref="taskGroupForm"
        xs3
        class="task-group"
    >
        <v-card
            class="mx-auto"
            color="grey lighten-3 black--text"
            max-width="400"
            hover
        >
            <v-card-actions v-show="!isShowForm">
                <v-btn
                    block
                    depressed
                    color="grey lighten-2 grey--text text--darken-3"
                    @click="isShowForm = true"
                >
                    <v-icon>add</v-icon>
                    {{ $t('hints.add_another_group') }}
                </v-btn>
            </v-card-actions>

            <v-card-text
                v-show="isShowForm"
                class="pa-0"
            >
                <v-text-field
                    ref="groupName"
                    v-model="form.name"
                    :loading="form.processing"
                    tabindex="0"
                    placeholder="Enter group name"
                    single-line
                    full-width
                    hide-details
                    class="body-1 white black--text pb-2"
                    @keyup.enter="submit"
                />
            </v-card-text>

            <v-card-actions v-show="isShowForm">
                <v-btn
                    depressed
                    small
                    color="secondary"
                    :disabled="form.processing"
                    @click="isShowForm = false"
                >
                    Cancel
                </v-btn>
                <v-spacer />
                <v-btn
                    depressed
                    small
                    color="primary"
                    :loading="form.processing"
                    :disabled="form.processing || form.name.trim() == ''"
                    @click="submit"
                >
                    Add
                    <template #loader>
                        <span>Adding...</span>
                    </template>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-flex>
</template>
<script>
import { onClickOutside } from '@vueuse/core'
import TodoPoliciesMixin from '../../Mixins/TodoPoliciesMixin'

export default {
    mixins: [TodoPoliciesMixin],

    data: (vm) => ({
        isShowForm: false,
        form: vm.$inertia.form({
            name: '',
        }),
    }),

    watch: {
        isShowForm(val) {
            if (val) {
                this.$nextTick(() => {
                    this.$refs.groupName.focus()
                })
            }
        },
    },

    mounted() {
        onClickOutside(this.$refs.taskGroupForm, () => {
            if (this.isShowForm) this.reset()
        })
    },

    methods: {
        submit() {
            if (!this.allowedToManageGroup()) return

            const board = this.$page.props.board.id
            this.form.post(
                this.$route('todos.boards.groups.store', { board }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['groups', 'errors'],
                    errorBag: 'createBoardGroup',
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            'New group has been created.',
                        )
                    },
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                    onFinish: () => this.reset(),
                },
            )
        },

        reset() {
            this.$nextTick(() => {
                this.form.reset()
                this.form.reset()
                this.isShowForm = false
            })
        },
    },
}
</script>
