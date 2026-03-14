<template>
    <v-flex
        v-show="isShow"
        xs12
        class="pt-1 pb-2"
    >
        <v-card hover>
            <v-card-text class="pa-0">
                <v-text-field
                    ref="cardInputTitle"
                    v-model="form.title"
                    :loading="form.processing"
                    :disabled="form.processing"
                    tabindex="0"
                    placeholder="Enter title for this card"
                    single-line
                    full-width
                    hide-details
                    class="body-1 white black--text pb-2"
                    @keyup.enter="submit"
                />
            </v-card-text>
            <v-divider />
            <v-card-actions>
                <v-btn
                    depressed
                    small
                    color="secondary"
                    :disabled="form.processing"
                    @click="$emit('close')"
                >
                    Cancel
                </v-btn>
                <v-spacer />
                <v-btn
                    depressed
                    small
                    color="primary"
                    :loading="form.processing"
                    :disabled="form.processing || form.title.trim() == ''"
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
export default {
    props: {
        isShow: {
            type: Boolean,
            default: false,
        },
        group: {
            type: String,
            required: true,
        },
    },
    data: (vm) => ({
        form: vm.$inertia.form({
            title: '',
            group: vm.group,
        }),
    }),

    watch: {
        isShow(val) {
            if (val) this.form.reset()
        },
    },

    methods: {
        submit() {
            const board = this.$page.props.board.id
            this.form.post(this.$route('todos.boards.tasks.store', { board }), {
                preserveState: true,
                preserveScroll: true,
                only: ['groups', 'errors'],
                errorBag: 'createBoardTask',
                onSuccess: () => {
                    this.$root.$emit(
                        'flash.success',
                        'Task has been added to this board.',
                    )
                },
                onError: (errors) =>
                    this.$root.$emit('flash.validation', errors),
                onFinish: () => this.$emit('close'),
            })
        },
    },
}
</script>
