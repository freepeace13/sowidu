<template>
    <v-dialog
        v-model="isShow"
        max-width="650"
    >
        <v-card>
            <v-card-title class="title">
                {{ isEditing ? $t('buttons.update') : $t('buttons.create') }}
                {{ $t('labels.inputs.role') }}
            </v-card-title>

            <v-card-text>
                <v-text-field
                    v-model="form.name"
                    color="primary"
                    :loading="form.processing"
                    :disabled="form.processing"
                    :error-messages="form.errors.name"
                    :label="$t('labels.inputs.name')"
                    :placeholder="$t('account.inputs.role-name')"
                    autofocus
                    outline
                    :hide-details="!form.errors.name"
                    class="required-input"
                />
            </v-card-text>

            <v-card-actions>
                <v-spacer />

                <v-btn
                    :disabled="form.processing"
                    @click="close"
                >
                    {{ $t('buttons.close') }}
                </v-btn>
                <v-btn
                    color="primary"
                    :disabled="form.processing"
                    :loading="form.processing"
                    @click="save"
                >
                    {{ $t(`buttons.${isEditing ? 'update' : 'create'}`) }}

                    <template #loader>
                        <span>
                            {{
                                isEditing
                                    ? $t('buttons.updating')
                                    : $t('buttons.creating')
                            }}
                        </span>
                    </template>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
export default {
    data: (vm) => ({
        isShow: false,
        form: vm.$inertia.form({
            old_name: null,
            name: null,
        }),
    }),

    computed: {
        isEditing() {
            return this.form.old_name
        },
    },

    methods: {
        show(role = null) {
            this.form.reset()
            this.form.clearErrors()
            if (role) {
                this.form.old_name = role
                this.form.name = role
            }

            this.$nextTick(() => {
                this.isShow = true
            })
        },

        createNewRole() {
            this.$inertia.post(
                this.$route('account.access.roles.store'),
                { name: this.newRole },
                {
                    onSuccess: () => {
                        this.form.roles.push(this.newRole)
                        this.newRole = null
                    },
                },
            )
        },

        close() {
            this.form.reset()
            this.isShow = false
        },

        save() {
            const isEditing = this.isEditing
            const method = this.isEditing ? 'patch' : 'post'
            const params = isEditing ? { id: this.form.old_name } : null

            this.form[method](
                this.$route(
                    `account.organizations.roles.${
                        isEditing ? 'update' : 'store'
                    }`,
                    {
                        ...params,
                    },
                ),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['roles', 'errors', 'employees'],
                    onSuccess: () => {
                        this.$root.$emit(
                            'flash.success',
                            isEditing
                                ? this.$t('account.messages.role.updated')
                                : this.$t('account.messages.role.created'),
                        )
                        this.close()
                    },
                    onError: (errors) =>
                        this.$root.$emit('flash.validation', errors),
                },
            )
        },
    },
}
</script>
