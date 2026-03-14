<template>
    <v-dialog
        v-model="isShow"
        max-width="650"
    >
        <v-card>
            <v-card-title class="title">
                {{ isEditing ? $t('buttons.update') : $t('buttons.create') }}
                {{ $t('account.labels.category') }}
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
                <SubmitButton
                    :is-processing="form.processing"
                    @click="save"
                >
                    {{ $t(`buttons.${isEditing ? 'update' : 'create'}`) }}
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import SubmitButton from '@components/Forms/SubmitButton.vue'

export default {
    components: { SubmitButton },
    data: (vm) => ({
        isShow: false,
        form: vm.$inertia.form({
            name: null,
        }),
        isEditing: false,
        category: null,
    }),
    methods: {
        show(category = null) {
            this.form.reset()
            this.form.clearErrors()
            this.category = category
            if (category) {
                this.form.name = category.name
                this.isEditing = true
            }
            this.$nextTick(() => {
                this.isShow = true
            })
        },
        close() {
            this.form.reset()
            this.isShow = false
        },
        save() {
            const isEditing = this.isEditing
            const method = this.isEditing ? 'patch' : 'post'

            let params = {}
            if (isEditing) {
                params = {
                    category: this.category.id,
                    roles: this.category.settings.auto_share_to_roles,
                }
            }

            this.form.transform((data) => ({
                ...data,
            }))

            this.form[method](
                this.$route(
                    `account.categories.${isEditing ? 'update' : 'store'}`,
                    {
                        ...params,
                    },
                ),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['categories', 'errors', 'category'],
                    onSuccess: () => {
                        let message = this.$t(
                            'account.messages.category.created',
                        )
                        if (isEditing) {
                            message = this.$t(
                                'account.messages.category.updated',
                            )
                        }

                        this.$root.$emit('flash.success', message)
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
