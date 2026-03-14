<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import AccountPageLayout from '../AccountPageLayout.vue'
import { computed, ref } from 'vue'
import TaxForm from './Components/TaxForm.vue'
import { router, useForm } from '@inertiajs/vue2'
import InvoiceDefaultsForm from './Components/InvoiceDefaultsForm.vue'

export default {
    layout: [AuthLayout, AccountPageLayout],
}
</script>
<script setup>
import useGlobalVariables from '@/Composables/useGlobalVariables'

const props = defineProps({
    taxes: {
        required: true,
        type: Array,
    },
    company: {
        required: true,
        type: Object,
    },
    currencies: {
        required: false,
        type: Array,
        default: () => [],
    },
    employees: {
        required: false,
        type: Array,
        default: () => [],
    },
})

const { $t, $confirm, $route } = useGlobalVariables()
const taxFormRef = ref(null)

const taxNumberForm = useForm({
    tax_number: props.company.tax_number,
})

const vatForm = useForm({
    vat_identification_number: props.company.vat_identification_number,
})

const headers = [
    {
        text: $t('account.tax.labels.tax-name'),
        align: 'left',
        sortable: false,
        value: 'name',
    },
    {
        text: $t('account.tax.labels.tax-rate'),
        value: 'name',
        sortable: false,
    },
    {
        text: $t('labels.actions'),
        value: 'actions',
        align: 'right',
        sortable: false,
    },
]

const vatIdentificationNumberChanged = computed(
    () =>
        props.company.vat_identification_number !==
        vatForm.vat_identification_number,
)

const taxNumberChanged = computed(
    () => props.company.tax_number !== taxNumberForm.tax_number,
)

function confirmDeleting(tax) {
    $confirm({
        title: $t('labels.warning'),
        question: $t('account.tax.labels.tax-delete'),
        type: 'warning',
        confirm: () => {
            router.delete(
                $route('account.settings.tax.destroy', {
                    tax,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['taxes'],
                },
            )
        },
    })
}

function updateVatIdentificationNumber() {
    vatForm.patch($route('account.settings.vat-identification-number.update'), {
        preserveState: true,
        preserveScroll: true,
        only: ['company'],
    })
}

function updateTaxNumber() {
    taxNumberForm.patch($route('account.settings.tax-number.update'), {
        preserveState: true,
        preserveScroll: true,
        only: ['company'],
    })
}
</script>
<template>
    <div>
        <div class="mb-5">
            <TaxForm ref="taxFormRef" />

            <v-layout
                align-center
                justify-space-between
            >
                <v-flex class="headline shrink font-weight-bold">
                    {{ $t('account.labels.invoice-settings') }}
                </v-flex>
            </v-layout>
            <v-divider class="mb-3" />
            <v-layout
                row
                wrap
            >
                <!-- Invoice Defaults -->
                <v-flex xs12>
                    <InvoiceDefaultsForm v-bind="$props" />
                </v-flex>
                <!-- End Invoice Defaults -->
                <v-flex
                    xs12
                    class="subheading"
                >
                    {{ $t('account.labels.tax-settings') }}
                    <v-divider />
                </v-flex>
                <v-flex
                    sm6
                    xs12
                    class="mb-2"
                >
                    <div class="tw-flex tw-items-center">
                        <v-text-field
                            v-model="vatForm.vat_identification_number"
                            :loading="vatForm.processing"
                            :disabled="vatForm.processing"
                            :error-messages="
                                vatForm.errors.vat_identification_number
                            "
                            :hide-details="
                                !vatForm.errors.vat_identification_number
                            "
                            required
                            :label="
                                $t(
                                    'account.tax.labels.vat-identification-number',
                                )
                            "
                            color="primary"
                            outline
                            class="required-input append-outer"
                        >
                            <template #append-outer />
                        </v-text-field>
                        <v-btn
                            :class="{
                                'tw-invisible': !vatIdentificationNumberChanged,
                            }"
                            color="primary"
                            :disabled="vatForm.processing"
                            :loading="vatForm.processing"
                            @click="updateVatIdentificationNumber"
                        >
                            {{ $t('buttons.update') }}
                        </v-btn>
                    </div>
                </v-flex>
                <v-flex
                    sm6
                    xs12
                />
                <v-flex
                    sm6
                    xs12
                    class="mb-2"
                >
                    <div class="tw-flex tw-items-center">
                        <v-text-field
                            v-model="taxNumberForm.tax_number"
                            :loading="taxNumberForm.processing"
                            :disabled="taxNumberForm.processing"
                            :error-messages="taxNumberForm.errors.tax_number"
                            :hide-details="!taxNumberForm.errors.tax_number"
                            required
                            :label="$t('account.tax.labels.company-tax-number')"
                            color="primary"
                            outline
                            class="required-input append-outer"
                        />
                        <v-btn
                            :class="{
                                'tw-invisible': !taxNumberChanged,
                            }"
                            color="primary"
                            :loading="taxNumberForm.processing"
                            :disabled="taxNumberForm.processing"
                            @click="updateTaxNumber"
                        >
                            {{ $t('buttons.update') }}
                        </v-btn>
                    </div>
                </v-flex>

                <v-flex
                    class="subheading"
                    xs12
                >
                    {{ $t('account.tax.labels.taxes') }}
                    <v-divider />
                </v-flex>
                <v-flex class="pt-0 tw-flex">
                    <v-subheader class="px-0">
                        {{ $t('account.tax.hints.tax-default') }}
                    </v-subheader>
                    <v-spacer />
                    <v-btn
                        color="primary"
                        @click="taxFormRef.show()"
                    >
                        {{ $t('buttons.create') }}
                    </v-btn>
                </v-flex>
                <v-flex xs12>
                    <v-data-table
                        :headers="headers"
                        :items="taxes"
                        hide-actions
                        class="elevation-1"
                    >
                        <template #items="{ item }">
                            <td>{{ item.name }}</td>
                            <td>{{ item.rate }}</td>
                            <td class="tw-text-right">
                                <v-chip
                                    v-show="item.is_default"
                                    color="primary"
                                    class="white--text"
                                >
                                    {{ $t('account.tax.labels.default') }}
                                </v-chip>
                                <v-btn
                                    flat
                                    icon
                                    color="info"
                                    small
                                    @click="taxFormRef.show(item)"
                                >
                                    <v-icon small>edit</v-icon>
                                </v-btn>
                                <v-btn
                                    flat
                                    icon
                                    small
                                    color="error"
                                    @click="confirmDeleting(item)"
                                >
                                    <v-icon small>delete</v-icon>
                                </v-btn>
                            </td>
                        </template>
                    </v-data-table>
                </v-flex>
            </v-layout>
        </div>
    </div>
</template>
<style scoped lang="scss">
.append-outer {
    .v-input__append-outer {
        @apply tw-mt-0;
    }
}
</style>
