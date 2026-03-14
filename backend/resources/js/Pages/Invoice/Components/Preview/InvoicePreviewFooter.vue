<script setup>
import { isNotNil } from '@/Composables/useUtils'
import { computed } from 'vue'

const props = defineProps({
    invoice: {
        type: Object,
        required: true,
    },
})

const company = computed(() => props.invoice.company)

const hasManagingDirector = computed(() =>
    isNotNil(company.value.invoice_defaults?.managing_director?.name),
)
const companyInvoiceDefaults = computed(() => company.value.invoice_defaults)
const hasValidCommercialRegister = computed(
    () =>
        isNotNil(companyInvoiceDefaults.value?.commercial_register) &&
        isNotNil(companyInvoiceDefaults.value?.commercial_register_number),
)
</script>
<template>
    <v-layout
        row
        wrap
        align-end
        mt-3
        class="!tw-flex-none no-break invoice-footer"
    >
        <v-flex
            xs12
            class="tw-flex tw-text-xs bank-name"
        >
            <div>{{ $t('invoices.preview.bank-details') }}:</div>
            <div class="ml-1">
                {{ company?.invoice_defaults?.bank_name ?? '--' }}
            </div>
        </v-flex>
        <v-flex
            xs4
            class="tw-flex tw-text-xs iban-bic tw-flex-col tw-gap-y-2"
        >
            <div class="iban tw-flex">
                <div>{{ $t('invoices.preview.iban') }}:</div>
                <div class="ml-1">
                    {{ company?.invoice_defaults?.iban ?? '--' }}
                </div>
            </div>
            <div class="bic tw-flex">
                <div>{{ $t('invoices.preview.bic') }}:</div>
                <div class="ml-1">
                    {{ companyInvoiceDefaults?.bic ?? '--' }}
                </div>
            </div>
        </v-flex>

        <!-- HRA Nr. -->
        <v-flex
            xs4
            class="tw-flex tw-text-xs vat-hra tw-flex-col tw-gap-y-2"
        >
            <div class="vat tw-flex">
                <div>
                    {{ $t('invoices.tax.labels.vat-identification-number') }}:
                </div>
                <div class="ml-1">
                    {{ invoice?.company?.vat_identification_number ?? '--' }}
                </div>
            </div>
            <div
                v-if="hasValidCommercialRegister"
                class="hra tw-flex"
            >
                <div>
                    {{
                        `${companyInvoiceDefaults?.commercial_register} ${$t(
                            'invoices.preview.commercial-register-number',
                        )}`
                    }}:
                </div>
                <div class="ml-1">
                    {{
                        companyInvoiceDefaults?.commercial_register_number ??
                        '--'
                    }}
                </div>
            </div>
        </v-flex>

        <v-flex
            xs4
            class="tw-text-xs !tw-text-right managing-director"
        >
            <div v-if="hasManagingDirector">
                {{ $t('invoices.preview.managing-director') }}:
            </div>
            <div class="tw-mt-2">
                {{ companyInvoiceDefaults?.managing_director?.name }}
            </div>
        </v-flex>
    </v-layout>
</template>
