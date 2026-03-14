<template>
    <tr>
        <td class="">
            <div class="tw-flex tw-items-center tw-gap-x-2">
                <AppAvatar :avatar="workLog.causer.photo" />
                {{ workLog.causer.name }}
            </div>
        </td>
        <td>
            <div
                v-if="!isManualEntry"
                class="tw-flex tw-flex-row tw-items-center tw-gap-x-2"
            >
                <a
                    class="info--text hover:tw-underline tw-font-semibold"
                    :href="
                        $route('orders.show', {
                            order: workLog?.order?.id,
                        })
                    "
                    target="_blank"
                >
                    {{ workLog.order?.order_number ?? '--' }}
                </a>
                <v-btn
                    v-if="isShowChangeOrderButton"
                    v-tooltip="`${$t('order.labels.change-order')}`"
                    flat
                    icon
                    small
                    @click="$emit('click:change-order', workLog)"
                >
                    <v-icon
                        small
                        color="secondary"
                    >
                        edit
                    </v-icon>
                </v-btn>
            </div>
            <div v-else>
                {{ workLog.order?.order_number ?? '--' }}
            </div>
        </td>
        <td class="tw-table-cell !tw-h-auto md:tw-h-12 px-2">
            <div class="tw-flex tw-items-center tw-justify-between">
                <div v-if="isManualEntry">
                    <v-chip
                        label
                        :color="workLog.event_meta.color"
                        text-color="white"
                        small
                    >
                        <span>
                            {{ workLog.event_meta.name }}
                        </span>
                    </v-chip>
                </div>
                <div
                    v-else
                    class="py-1"
                >
                    {{ deliveryAddress }}
                </div>
                <div
                    class="tw-text-info hover:tw-underline tw-text-xs tw-cursor-pointer"
                    @click.stop="$emit('click:show-reports')"
                >
                    {{ expandLabel }}
                </div>
            </div>
        </td>
        <td>
            <div class="tw-flex tw-flex-col">
                <div>
                    {{ workLog.started_at | formatDate('DD.MM.YYYY') }}
                </div>
                <div>
                    {{ workLog.started_at | formatDate('HH:mm') }}
                </div>
            </div>
        </td>
        <td>
            <v-chip
                v-if="workLog.is_currently_working"
                class="tw-static"
                color="orange"
                text-color="white"
            >
                {{ $t('labels.work_log.currently_working') }}
            </v-chip>
            <div
                v-else
                class="tw-flex tw-flex-col"
            >
                <div>
                    {{ workLog.ended_at | formatDate('DD.MM.YYYY') }}
                </div>
                <div>{{ workLog.ended_at | formatDate('HH:mm') }}</div>
            </div>
        </td>
        <td>
            <v-chip
                v-tooltip.top="`${invoiceStatusTooltip}`"
                label
                :color="invoiceStatusChipColor"
                text-color="white"
                small
                class="x-small"
            >
                <v-icon small>
                    {{ workLog?.is_invoiced ? 'lock' : 'lock_open' }}
                </v-icon>
                <div class="tw-text-xs mx-2">
                    {{
                        workLog?.is_invoiced
                            ? $t('work_log.labels.invoiced')
                            : $t('work_log.labels.open')
                    }}
                </div>
                <v-icon small>
                    {{ workLog.is_paid ? 'task_alt' : 'update' }}
                </v-icon>
            </v-chip>
        </td>

        <td>
            <div class="tw-inline-flex tw-flex-col tw-items-center">
                <payment-form-modal
                    :payment-form="workLog.payment_form"
                    :document-number="workLog.document_number"
                    :document-date="workLog.document_date"
                    :options="$page.props.paymentForms"
                    @update="
                        $emit('update-payment-form', {
                            id: workLog.id,
                            value: $event,
                        })
                    "
                />

                <div class="tw-flex">
                    <v-chip
                        v-if="workLog.document_number"
                        :color="workLog.payment_form?.color || 'grey'"
                        text-color="white"
                        small
                        class="no-radius chip-dimmed"
                    >
                        {{ workLog.document_number }}
                    </v-chip>

                    <v-chip
                        v-if="workLog.document_date"
                        :color="workLog.payment_form?.color || 'grey'"
                        text-color="white"
                        small
                        class="no-radius chip-dimmed"
                    >
                        {{ workLog.document_date | formatDate('YYYY-MM-DD') }}
                    </v-chip>
                </div>
            </div>
        </td>

        <td class="font-weight-bold">
            <div class="tw-flex tw-items-center">
                <div>
                    <v-chip
                        label
                        :color="durationChipColor"
                        text-color="white"
                        small
                    >
                        <v-icon
                            left
                            small
                        >
                            schedule
                        </v-icon>
                        <span class="tw-font-bold">
                            {{ workLog?.duration ?? '--' }}
                        </span>
                    </v-chip>
                </div>
                <div
                    v-if="isManualEntry"
                    class="tw-flex tw-justify-end tw-items-center tw-w-full"
                >
                    <v-btn
                        v-if="isShowCanEdit"
                        small
                        color="info"
                        flat
                        class="tw-my-0 !tw-mx-0"
                        icon
                        @click="$emit('click:edit', workLog)"
                    >
                        <v-icon small>edit</v-icon>
                    </v-btn>
                    <v-btn
                        v-if="isShowCanDelete"
                        small
                        flat
                        class="tw-my-0 !tw-mx-0"
                        icon
                        color="error"
                        @click="$emit('click:delete', workLog)"
                    >
                        <v-icon small>delete</v-icon>
                    </v-btn>
                </div>
            </div>
        </td>
        <td v-if="isShareable && canToggleSharing">
            <div class="tw-text-xs tw-flex tw-items-center tw-gap-x-2">
                <div>{{ $t('order.labels.share-report') }}</div>
                <v-switch
                    v-model="form.is_shared"
                    hide-details
                    class="mt-0 tw-justify-center"
                    @change="toggleShareToClient"
                />
            </div>
        </td>
    </tr>
</template>

<script>
import AppAvatar from '@/Components/AppAvatar.vue'
import PaymentFormModal from './WorkLogPaymentForm.vue'

export default {
    components: { AppAvatar, PaymentFormModal },
    props: {
        workLog: {
            required: true,
            type: Object,
        },
        isShareable: {
            required: false,
            type: Boolean,
            default: false,
        },
        isExpanded: {
            required: false,
            type: Boolean,
            default: false,
        },
        isShowCanEdit: {
            required: false,
            type: Boolean,
            default: false,
        },
        isShowCanDelete: {
            required: false,
            type: Boolean,
            default: false,
        },
        canShareToClient: {
            required: false,
            type: Boolean,
            default: true,
        },
        isShowChangeOrderButton: {
            required: false,
            type: Boolean,
            default: false,
        },
    },

    data: (vm) => ({
        form: vm.$inertia.form({
            is_shared: vm.workLog?.is_shared ?? false,
        }),
    }),

    computed: {
        canToggleSharing() {
            const user = this.$page.props.user

            if (this.$page.props?.viewer?.is_client || !this.canShareToClient) {
                return false
            }

            return user?.tenant.is_owner || user.id === this.workLog.causer.id
        },

        durationChipColor() {
            if (this.isManualEntry) {
                return this.workLog.event_meta.color
            }

            return 'primary'
        },

        isManualEntry() {
            return (
                this.workLog?.order === null ||
                this.workLog?.order === undefined
            )
        },

        deliveryAddress() {
            if (!this.workLog?.order) return '--'

            return this.workLog.order.delivery_address.full
        },

        expandLabel() {
            if (this.isExpanded) return this.$t('labels.work_log.hide-reports')

            return this.isManualEntry
                ? this.$t('work_log.labels.show-note')
                : this.$t('work_log.labels.show-reports')
        },

        invoiceStatusTooltip() {
            if (this.workLog.is_paid) {
                return this.$t('work_log.labels.invoiced-paid')
            }

            return this.workLog.is_invoiced
                ? this.$t('work_log.labels.invoiced-not-paid')
                : this.$t('work_log.labels.open')
        },

        invoiceStatusChipColor() {
            if (this.workLog.is_paid) {
                return 'greener'
            }

            return this.workLog.is_invoiced ? 'blue-info' : 'primary'
        },
    },

    methods: {
        toggleShareToClient() {
            const workLog = this.workLog
            const { order } = workLog

            this.form.patch(
                this.$route('orders.work_logs.share', {
                    order,
                    workLog,
                }),
                {
                    only: ['errors', 'flash', 'timeLogs'],
                    preserveState: true,
                    preserveScroll: true,
                },
            )
        },
    },
}
</script>
<style scoped>
.no-radius {
    border-radius: 0 !important;
}
.chip-dimmed {
    opacity: 0.5;
}
</style>
