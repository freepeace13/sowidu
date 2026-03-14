<template>
    <v-dialog
        v-model="isShow"
        persistent
        max-width="600px"
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{
                        $t(
                            `order.labels.${
                                isEditing ? 'edit-report' : 'create-report'
                            }`,
                        )
                    }}
                </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <v-card-text class="pt-0">
                <v-container
                    grid-list-lg
                    fluid
                    pa-2
                >
                    <v-layout column>
                        <v-flex xs12>
                            <v-textarea
                                v-model="form.note"
                                :loading="form.processing"
                                :label="$t('order.labels.report')"
                                :error-messages="form.errors.note"
                                :hide-details="!form.errors.note"
                                outline
                                class="required-input"
                            />
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-divider />
            <v-card-actions class="px-4">
                <v-btn
                    :disabled="form.processing"
                    outline
                    depressed
                    @click="close"
                >
                    Cancel
                </v-btn>
                <v-spacer />
                <SubmitButton
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="submit"
                >
                    {{ $t('buttons.save') }}
                    <template #loader>
                        <span>{{ $t('buttons.saving') }}...</span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import SubmitButton from '@components/Forms/SubmitButton.vue'

export default {
    components: {
        SubmitButton,
    },

    props: {
        orderId: {
            required: true,
            type: Number,
        },
    },

    data: (vm) => ({
        form: vm.$inertia.form({
            note: null,
        }),
        isShow: false,
        report: null,
        workLog: null,
        isEditing: false,
    }),

    methods: {
        async show(workLog, report = null) {
            if (!workLog) return

            this.reset()

            this.workLog = workLog

            if (report) {
                this.form.note = report.note
                this.report = report

                this.isEditing = true
            }

            this.isShow = true
        },

        close() {
            this.workLog = null
            this.report = null

            this.reset()

            this.isEditing = false
            this.isShow = false
        },

        reset() {
            this.form.reset()
            this.form.clearErrors()
        },

        submit() {
            const workLog = this.workLog
            const order = this.orderId

            if (this.isEditing) {
                const report = this.report

                this.form.path(
                    this.$route('orders.time_tracker.report.update', {
                        order,
                        workLog,
                        report,
                    }),
                    {
                        preserveState: true,
                        preserveScroll: true,
                        onError: (errors) =>
                            this.$root.$emit('flash.validation', errors),
                    },
                )
            } else {
                this.form.post(
                    this.$route('orders.time_tracker.report.store', {
                        order,
                        workLog,
                    }),
                    {
                        preserveState: true,
                        preserveScroll: true,
                        onSuccess: () => this.close(),
                    },
                )
            }
        },
    },
}
</script>
