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
                    {{ $t('order.labels.change-order') }}
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
            <v-card-text>
                <v-container
                    grid-list-lg
                    fluid
                    pa-2
                >
                    <v-layout column>
                        <v-flex>
                            <OrderAutocomplete
                                v-model="form.order"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :label="$t('order.labels.order')"
                                :placeholder="$t('invoices.form.search-order')"
                                :error-messages="form.errors.order"
                                :hide-details="!form.errors.order"
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
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
<script>
import { useDateNow } from '@/Composables/useDayJs'
import { useDefaultAddress } from '@/Composables/useDefaults'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import OrderAutocomplete from '@/Apps/Shared/Components/AutoComplete/OrderAutocomplete.vue'

export default {
    components: {
        SubmitButton,
        OrderAutocomplete,
    },

    props: {
        refreshProps: {
            required: true,
            type: Array,
        },
    },

    data: (vm) => ({
        form: vm.$inertia.form({
            order: null,
        }),
        isShow: false,
        workLog: null,
    }),

    methods: {
        show(workLog) {
            this.form.reset()

            this.workLog = workLog
            this.isShow = true
        },

        close() {
            this.reset()
            this.isShow = false
        },

        getDefaultAddress() {
            return useDefaultAddress
        },

        defaultDate() {
            return useDateNow()
        },

        reset() {
            this.form.reset()
            this.form.clearErrors()

            this.workLog = null
        },

        submit() {
            const order = this.$page.props.order
            const workLog = this.workLog

            this.form
                .transform((data) => ({
                    ...data,
                    order: {
                        id: data.order.id,
                    },
                }))
                .patch(
                    this.$route('orders.show.time_logs.update', {
                        order,
                        workLog,
                    }),
                    {
                        preserveState: true,
                        preserveScroll: true,
                        only: this.refreshProps,
                        onSuccess: () => {
                            this.close()
                        },
                    },
                )
        },
    },
}
</script>
