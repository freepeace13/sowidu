<script setup>
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { useForm } from '@inertiajs/vue2'
import { computed, toRefs } from 'vue'
import { VTextField } from 'vuetify/lib'

const props = defineProps({
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

const { company } = toRefs(props)
const invoiceDefaults = computed(() => company.value.invoice_defaults)
const { $t, $route } = useGlobalVariables()

const form = useForm({
    payment_terms: invoiceDefaults.value?.payment_terms,
    bank_name: invoiceDefaults.value?.bank_name,
    iban: invoiceDefaults.value?.iban,
    bic: invoiceDefaults.value?.bic,
    managing_director: invoiceDefaults.value?.managing_director,
    website: invoiceDefaults.value?.website,
    company_email: invoiceDefaults.value?.company_email,
    commercial_register: invoiceDefaults.value?.commercial_register,
    commercial_register_number:
        invoiceDefaults.value?.commercial_register_number,
    currency: company.value.currency?.name,
    payment_terms_instructions:
        invoiceDefaults.value?.payment_terms_instructions,
})

const commercialRegisterTypes = [
    {
        value: 'HRB',
        text: $t('account.inputs.commercial-register-hrb'),
    },
    {
        value: 'HRA',
        text: $t('account.inputs.commercial-register-hra'),
    },
]

function save() {
    form.post($route('account.settings.invoice.store'), {
        preserveScroll: true,
        preserveState: true,
        only: ['company'],
    })
}
</script>
<template>
    <v-layout
        row
        wrap
    >
        <v-flex
            xs12
            class="subheading"
        >
            {{ $t('account.labels.invoice-defaults') }}
            <v-divider />
        </v-flex>

        <v-flex
            xs12
            sm6
        >
            <v-select
                v-model="form.managing_director"
                outline
                full-width
                :items="employees"
                :loading="form.processing"
                :error-messages="form?.errors.managing_director"
                :hide-details="!form?.errors.managing_director"
                :label="$t('account.inputs.managing-director')"
                clearable
                item-value="id"
            >
                <template #item="{ item }">
                    <slot
                        name="item"
                        v-bind="item"
                    >
                        <v-list-tile-avatar>
                            <img :src="item.photo" />
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title
                                class="tw-flex tw-items-end tw-justify-between"
                            >
                                <div class="">
                                    {{ item.name }}
                                </div>
                            </v-list-tile-title>
                            <v-list-tile-sub-title
                                class="!tw-text-xs tw-font-thin"
                            >
                                {{ item.roles.join(', ') }}
                            </v-list-tile-sub-title>
                        </v-list-tile-content>
                    </slot>
                </template>
                <template #selection="{ item }">
                    <v-list-tile-content>
                        <v-list-tile-title
                            class="tw-flex tw-items-end tw-justify-between"
                        >
                            <div class="">
                                {{ item.name }}
                            </div>
                        </v-list-tile-title>
                    </v-list-tile-content>
                </template>
            </v-select>
        </v-flex>
        <v-flex
            xs12
            sm6
        >
            <v-text-field
                v-model="form.payment_terms"
                :loading="form.processing"
                :disabled="form.processing"
                :error-messages="form.errors.payment_terms"
                :hide-details="!form.errors.payment_terms"
                :label="$t('account.inputs.payment-terms')"
                color="primary"
                outline
                class="required-input"
            />
        </v-flex>

        <v-flex
            sm6
            xs12
            class="mb-2"
        >
            <v-text-field
                v-model="form.bank_name"
                :loading="form.processing"
                :disabled="form.processing"
                :error-messages="form.errors.bank_name"
                :hide-details="!form.errors.bank_name"
                :label="$t('account.inputs.bank-name')"
                color="primary"
                outline
                class=""
            />
        </v-flex>
        <v-flex
            xs12
            sm6
        >
            <v-select
                v-model="form.currency"
                outline
                full-width
                :items="currencies"
                :loading="form.processing"
                :error-messages="form?.errors.currency"
                :hide-details="!form?.errors.currency"
                :label="$t('labels.inputs.currency')"
                solo
                class="required-input"
                required
            />
        </v-flex>
        <v-flex
            xs12
            sm6
        >
            <v-text-field
                v-model="form.iban"
                :label="$t('account.inputs.iban')"
                :error-messages="form.errors.iban"
                :hide-details="!form.errors.iban"
                :loading="form.processing"
                :disabled="form.processing"
                color="primary"
                outline
            />
        </v-flex>
        <v-flex
            xs12
            sm6
        >
            <v-text-field
                v-model="form.bic"
                :label="$t('account.inputs.bic')"
                :error-messages="form.errors.bic"
                :hide-details="!form.errors.bic"
                :loading="form.processing"
                :disabled="form.processing"
                color="primary"
                outline
            />
        </v-flex>
        <v-flex
            xs12
            sm6
        >
            <v-select
                v-model="form.commercial_register"
                outline
                full-width
                :items="commercialRegisterTypes"
                :loading="form.processing"
                :error-messages="form?.errors.commercial_register"
                :hide-details="!form?.errors.commercial_register"
                :label="$t('account.inputs.commercial-register')"
            />
        </v-flex>
        <v-flex
            xs12
            sm6
        >
            <v-text-field
                v-model="form.commercial_register_number"
                :label="$t('account.inputs.commercial-register-number')"
                :error-messages="form.errors.commercial_register_number"
                :hide-details="!form.errors.commercial_register_number"
                :loading="form.processing"
                :disabled="form.processing"
                color="primary"
                outline
            />
        </v-flex>

        <v-flex
            sm6
            xs12
        >
            <v-text-field
                v-model="form.website"
                :label="$t('account.inputs.website')"
                :error-messages="form.errors.website"
                :hide-details="!form.errors.website"
                :loading="form.processing"
                :disabled="form.processing"
                color="primary"
                outline
            />
        </v-flex>
        <v-flex
            sm6
            xs12
        >
            <v-text-field
                v-model="form.company_email"
                :label="$t('account.inputs.company-email')"
                :error-messages="form.errors.company_email"
                :hide-details="!form.errors.company_email"
                :loading="form.processing"
                :disabled="form.processing"
                color="primary"
                outline
            />
        </v-flex>
        <v-flex
            sm12
            xs12
        >
            <v-textarea
                v-model="form.payment_terms_instructions"
                :label="
                    $t(
                        'account.invoice-settings.labels.payment-terms-instructions',
                    )
                "
                :disabled="form.processing"
                :loading="form.processing"
                :error-messages="form.errors.payment_terms_instructions"
                :hide-details="!form.errors.payment_terms_instructions"
                outline
            />
        </v-flex>
        <v-flex mt-3>
            <v-layout>
                <v-flex
                    pa-0
                    xs12
                    align-self-center
                    class="tw-flex tw-justify-end"
                >
                    <SubmitButton
                        :is-processing="form.processing"
                        @click="save"
                    >
                        {{ $t('buttons.save-changes') }}
                    </SubmitButton>
                </v-flex>
            </v-layout>
        </v-flex>
    </v-layout>
</template>
