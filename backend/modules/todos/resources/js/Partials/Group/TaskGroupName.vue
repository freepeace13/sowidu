<template>
    <v-text-field
        ref="formInput"
        v-model="form.name"
        :value="form.name"
        :outline="editing"
        tabindex="0"
        single-line
        full-width
        hide-details
        color="primary"
        class="title font-weight-bold group-name-field"
        @focus="editing = true"
        @focusout="editing = false"
        @keyup.enter="submit"
    />
</template>
<script>
import TodoPoliciesMixin from '../../Mixins/TodoPoliciesMixin'

export default {
    mixins: [TodoPoliciesMixin],
    props: {
        group: {
            required: true,
            type: Object,
            default: () => ({
                name: '',
            }),
        },
    },

    data: (vm) => ({
        editing: false,
        form: vm.$inertia.form({
            name: vm.group.name,
        }),
    }),

    watch: {
        editing(newValue) {
            if (newValue) this.$refs.formInput.focus()
        },
    },

    mounted() {
        this.form.name = this.group.name
    },

    methods: {
        edit() {
            this.editing = true
        },

        submit() {
            if (!this.allowedToManageGroup()) return

            const { board } = this.$page.props
            const group = this.group.name

            if (group == this.form.name) return

            this.form.patch(
                this.$route('todos.boards.groups.update', {
                    board,
                    group,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['groups', 'errors'],
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            'Group name has been updated.',
                        )
                        this.$refs.formInput.blur()
                    },
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                    onFinish: () => (this.editing = false),
                },
            )
        },
    },
}
</script>
