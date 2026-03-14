<template>
    <v-list-tile
        avatar
        data-app
    >
        <v-list-tile-content>
            <v-list-tile-title>
                {{ label }}
            </v-list-tile-title>
            <v-list-tile-sub-title class="caption">
                {{ details }}
            </v-list-tile-sub-title>
        </v-list-tile-content>
        <v-list-tile-action>
            <v-switch
                v-model="form.value"
                hide-details
                :loading="form.processing"
                :disabled="form.processing || disabled"
                color="primary"
                class="align-end"
                @change="update($event)"
            />
        </v-list-tile-action>
    </v-list-tile>
</template>
<script>
export default {
    props: {
        label: {
            type: String,
            required: true,
        },
        details: {
            required: true,
            type: String,
        },
        value: {
            required: true,
            type: Boolean,
        },
        role: {
            type: String,
            required: true,
        },
        permission: {
            type: String,
            required: true,
        },
        disabled: {
            required: false,
            type: Boolean,
            default: false,
        },
    },

    data: (vm) => ({
        form: vm.$inertia.form({
            value: vm.value,
            role: vm.role,
            permission: vm.permission,
        }),
    }),

    methods: {
        update(value) {
            const { board } = this.$page.props
            this.form
                .transform((data) => ({
                    ...data,
                    value,
                }))
                .patch(this.$route('todos.boards.permissions', { board }), {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['errors', 'settings'],
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            'Members permission has been updated.',
                        )
                    },
                    onError: (errors) => {
                        this.$root.$emit('flash.validation', errors)
                    },
                    onFinish: () => {
                        this.form.value = this.value
                    },
                })
        },
    },
}
</script>
