<template>
    <v-dialog
        v-model="show"
        max-width="650"
    >
        <v-card>
            <v-card-text class="mb-3">
                <v-layout
                    column
                    align-center
                    justify-start
                >
                    <div class="subheading mb-2">
                        {{ $t('hints.tell-us-about-your-organization') }}
                    </div>

                    <div
                        class="display-1 font-weight-bold primary--text tw-text-center"
                    >
                        {{ $t('hints.setup-your-organization') }}
                    </div>
                </v-layout>
            </v-card-text>

            <v-card-text>
                <input-text-field
                    v-model="form.name"
                    :label="$t('labels.inputs.name')"
                    :error-messages="form.errors.name"
                    required
                />

                <v-layout
                    -tw-m-2
                    tw-flex-wrap
                    sm:tw-flex-nowrap
                >
                    <v-flex
                        sm6
                        tw-w-full
                        tw-p-2
                    >
                        <input-select
                            v-model="form.institution_type"
                            :label="$t('labels.inputs.institution-type')"
                            :error-messages="form.errors.institution_type"
                            :items="$page.props.institutionTypes"
                            item-text="name"
                            item-value="id"
                            required
                        />
                    </v-flex>

                    <v-flex
                        sm6
                        tw-w-full
                        tw-p-2
                    >
                        <input-select
                            v-model="form.legal_form"
                            :label="$t('labels.inputs.legal-form')"
                            :error-messages="form.errors.legal_form"
                            :items="$page.props.legalForms"
                            item-text="name"
                            item-value="id"
                            required
                        />
                    </v-flex>
                </v-layout>

                <v-btn
                    color="primary"
                    :disabled="form.processing"
                    :loading="form.processing"
                    block
                    @click="createOrganization"
                >
                    {{ $t('buttons.create') }}
                    <template #loader>
                        <span>Creating...</span>
                    </template>
                </v-btn>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import InputSelect from '@components/InputSelect.vue'
import InputTextField from '@components/InputTextField.vue'

export default {
    components: {
        InputTextField,
        InputSelect,
    },

    data: (vm) => ({
        show: false,

        form: vm.$inertia.form({
            name: null,
            legal_form: null,
            institution_type: null,
        }),
    }),

    methods: {
        start() {
            this.form.reset()
            this.show = true
        },

        close() {
            this.form.reset()
            this.show = false
        },

        createOrganization() {
            this.form.clearErrors()
            this.form.post(this.$route('account.organizations.store'), {
                onSuccess: () => this.close(),
            })
        },
    },
}
</script>
