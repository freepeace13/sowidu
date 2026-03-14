<template>
    <v-container
        v-show="isShow"
        grid-list-md
        pt-0
        pb-2
        mt-3
        fluid
    >
        <v-layout
            row
            wrap
            mb-2
        >
            <v-flex
                xs12
                mb-2
            >
                <label> Name </label>
                <v-text-field
                    v-model="form.name"
                    outline
                    placeholder="Enter label name"
                    hide-details
                    required
                    class="small"
                />
            </v-flex>
            <v-flex
                xs12
                mb-1
            >
                <label> Select color </label>
            </v-flex>
            <v-flex
                v-for="(availableLabel, key) in labelAvailableColors"
                :key="key"
                xs3
                @click="form.color = availableLabel.color"
            >
                <TaskLabel
                    :label="availableLabel"
                    :selected="form.color == availableLabel.color"
                    class="justify-center"
                />
            </v-flex>
        </v-layout>
        <v-divider />
        <v-layout
            align-center
            justify-space-between
            row
            fill-height
            mt-2
        >
            <v-btn
                v-show="form.id"
                depressed
                color="error"
                small
                @click="deleteLabel"
            >
                Delete
            </v-btn>
            <v-spacer />
            <v-btn
                depressed
                color="primary"
                small
                @click="submit"
                >Save
            </v-btn>
        </v-layout>
    </v-container>
</template>
<script>
import TodoPoliciesMixin from '../../Mixins/TodoPoliciesMixin'
import TaskLabel from './TaskLabel.vue'

export default {
    components: { TaskLabel },

    mixins: [TodoPoliciesMixin],

    props: {
        availableColors: {
            type: Array,
            default: () => [],
            required: false,
        },
    },

    data: (vm) => ({
        isShow: false,
        form: vm.$inertia.form({
            id: null,
            name: null,
            color: null,
        }),
    }),

    computed: {
        labelAvailableColors() {
            return this.availableColors.map((color) => ({
                name: '',
                color,
            }))
        },
    },

    methods: {
        show(label = null) {
            if (!this.allowedToManageLabels()) return

            if (label) this.form = { ...this.form, ...label }
            this.isShow = true
            this.$emit('display', this.isShow)
        },

        close() {
            this.form.reset()
            this.isShow = false
            this.$emit('display', this.isShow)
        },

        submit() {
            const { board } = this.$page.props
            const { id } = this.form
            const isCreating = !id
            const route = this.$route(
                `todos.boards.labels.${isCreating ? 'store' : 'update'}`,
                { board, label: id },
            )

            this.form[isCreating ? 'post' : 'patch'](route, {
                preserveState: true,
                preserveScroll: true,
                only: ['labels', 'taskLabels', 'errors'],
                onSuccess: () => {
                    this.$root.$emit(
                        'flash.success',
                        `Label has been ${isCreating ? 'created' : 'updated'}.`,
                    )
                    this.close()
                },
                onError: (errors) =>
                    this.$root.$emit('flash.validation', errors),
            })
        },

        deleteLabel() {
            this.$confirm.ask({
                title: 'Delete label',
                question: 'Do you want to delete this label?',
                type: 'delete',
                confirm: () => {
                    this.$inertia.delete(
                        this.$route('todos.boards.labels.destroy', {
                            board: this.$page.props.board.id,
                            label: this.form.id,
                        }),
                        {
                            only: ['taskLabels', 'labels', 'errors'],
                            onSuccess: () =>
                                this.$root.$emit(
                                    'flash.success',
                                    'Label has been deleted.',
                                ),
                            onError: (errors) => {
                                this.$root.$emit('flash.validation', errors)
                            },
                        },
                    )
                },
            })
        },
    },
}
</script>
