<script setup>
import { authCan } from '@/Composables/useAuth'
import { useDateFormat } from '@/Composables/useDayJs'
import { computed, ref } from 'vue'
import DurationForm from '../DurationForm.vue'

const props = defineProps({
    invoice: {
        type: Object,
        required: true,
    },
    isPrinting: {
        required: false,
        type: Boolean,
        default: false,
    },
    isDownloading: {
        required: false,
        type: Boolean,
        default: false,
    },
})

const durationFormRef = ref()

const order = computed(() => props.invoice.order)
const deliveryAddress = computed(() => order.value.delivery_address)
const viewerCanEdit = computed(() => authCan('can_update_invoice'))
const client = computed(() => props.invoice.client)

const clientLegalForm = computed(
    () =>
        client.value?.legalform ??
        client.value?.legal_form?.legal_form ??
        client.value?.legal_form?.abbreviation ??
        '',
)

const executionStart = computed(() =>
    useDateFormat(
        props.invoice.execution_period_start ??
            props.invoice?.execution_period?.start,
    ),
)

const executionEnd = computed(() =>
    useDateFormat(
        props.invoice.execution_period_end ??
            props.invoice?.execution_period?.end,
    ),
)
</script>
<template>
    <div class="invoice-preview-information">
        <div
            xs6
            class="tw-pb-3 tw-flex tw-justify-between main"
        >
            <div class="tw-pr-4 invoice-id">
                <div class="tw-text-xs tw-font-semibold">
                    {{ $t('invoices.preview.invoice') }}:
                </div>
                <div>
                    {{ invoice.external_id ?? invoice.internal_id }}
                </div>
            </div>
            <div class="tw-pr-2 order-number">
                <div class="tw-text-xs tw-font-semibold">
                    {{ $t('invoices.labels.order-no') }}:
                </div>
                <div>
                    {{ order?.order_number }}
                </div>
            </div>
            <div class="tw-ml-auto invoice-date">
                <div class="tw-font-semibold tw-text-xs">
                    {{ $t('labels.date') }}:
                </div>
                <div class="tw-font-normal">
                    {{
                        useDateFormat(
                            invoice?.send_date ??
                                invoice?.updated_at ??
                                invoice?.created_at,
                        )
                    }}
                </div>
            </div>
        </div>
        <v-layout
            row
            wrap
            class="tw-text-xs construction-site"
        >
            <v-flex
                xs2
                pt-0
            >
                {{ $t('invoices.preview.construction-site') }}:
            </v-flex>
            <v-flex
                xs6
                pt-0
                class="tw-text-xs"
            >
                {{
                    [
                        deliveryAddress?.street,
                        deliveryAddress?.house_number,
                        deliveryAddress?.zipcode,
                        deliveryAddress?.city,
                    ]
                        .filter((i) => i)
                        .join('-')
                }}
            </v-flex>
        </v-layout>
        <v-layout
            row
            wrap
            class="tw-text-xs tw-leading-3 execution-period"
        >
            <v-flex
                xs2
                pt-0
            >
                {{ $t('invoices.preview.execution-period') }}:
            </v-flex>
            <v-flex
                xs6
                pt-0
                :class="[
                    'tw-flex',
                    'tw-gap-x-2',
                    {
                        'tw-cursor-pointer': !isDownloading,
                    },
                ]"
                @click="durationFormRef.showForm()"
            >
                <div>
                    {{ executionStart }}
                </div>
                <div>-</div>
                <div>
                    {{ executionEnd }}
                </div>
                <!-- TODO - move this to the parent component -->
                <DurationForm
                    v-if="viewerCanEdit && !isDownloading"
                    ref="durationFormRef"
                    :invoice="invoice.id"
                    :start="invoice.execution_period.start"
                    :end="invoice.execution_period.end"
                />
            </v-flex>
        </v-layout>
        <v-layout
            row
            wrap
            class="tw-text-xs service-recipient"
        >
            <v-flex
                xs2
                py-0
            >
                {{ $t('invoices.preview.service-recipient') }}:
            </v-flex>
            <v-flex
                xs6
                py-0
                class="tw-text-xs"
            >
                {{ client?.name }} {{ clientLegalForm }}
                {{
                    [
                        client?.address?.street,
                        client?.address?.house_number,
                        client?.address?.zipcode,
                        client?.address?.city,
                    ]
                        .filter((val) => val !== null)
                        .join(' ')
                }}
            </v-flex>
        </v-layout>
    </div>
</template>
