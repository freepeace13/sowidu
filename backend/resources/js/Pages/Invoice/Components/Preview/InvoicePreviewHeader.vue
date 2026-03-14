<script setup>
import { isNotNil } from '@/Composables/useUtils'
import { computed, ref } from 'vue'

const props = defineProps({
    invoice: {
        type: Object,
        required: true,
    },
    showClientAndCustomerDetails: {
        type: Boolean,
        required: false,
    },
    page: {
        type: Number,
        required: false,
    },
})

const emit = defineEmits(['logo:loaded'])
const companyLogoRef = ref(null)

const company = computed(() => props.invoice.company)
const client = computed(() => props.invoice.client)
const companyInfo = computed(() => {
    return [company.value?.name, `${companyLegalForm.value};`]
        .filter((val) => val)
        .join(' ')
})

const logo = computed(() => {
    return props.invoice.company?.photo ?? '/storage/companies.png'
})

const companyHasWebsite = computed(() =>
    isNotNil(company.value.invoice_defaults?.website),
)

const companyLegalForm = computed(
    () =>
        company.value.legal_form?.legal_form ??
        company.value.legal_form?.abbreviation,
)
const addressSectionIsOverflowing = computed(() => {
    const all = [props.companyInfo, companyAddressSection.value]
        .filter((val) => val)
        .join(' ')

    return all.length > 59
})
const clientLegalForm = computed(
    () =>
        client.value.legalform ??
        client.value.legal_form?.legal_form ??
        client.value.legal_form?.abbreviation ??
        '',
)

const companyAddressSection = computed(() => {
    const address = company.value.address

    const firstPart = [address?.street, address?.house_number]
        .filter((val) => val !== null)
        .join(' ')

    const secondPart = [address?.zipcode, address?.city]
        .filter((val) => val !== null)
        .join(' ')

    return [`${firstPart};`, secondPart].filter((val) => val).join(' ')
})

const careOf = computed(() => {
    const invoice = props.invoice
    if (invoice?.care_of_address && invoice.care_of_name) {
        return [
            invoice.care_of_name,
            invoice.care_of_legalform,
            invoice.care_of_address,
        ]
            .filter(Boolean)
            .join(' ')
    }

    return null
})

async function imageLoaded() {
    await new Promise((resolve) => setTimeout(resolve, 1000))
    emit('logo:loaded', {
        logoHeight: companyLogoRef.value.offsetHeight,
    })
}
</script>
<template>
    <div class="tw-flex tw-items-start tw-mb-5 invoice-sheet-header">
        <div
            :class="[
                'tw-text-lg tw-w-full payer tw-h-full tw-flex tw-flex-col',
                {
                    'tw-invisible': !showClientAndCustomerDetails,
                },
            ]"
        >
            <div
                :class="[
                    'tw-text-xxs',
                    'tw-flex',
                    'address',
                    {
                        'is-overflowed': addressSectionIsOverflowing,
                    },
                ]"
            >
                <!-- Company Address -->
                <div>{{ companyInfo }} {{ companyAddressSection }}</div>
            </div>
            <div class="tw-text-base">
                {{ client?.name }}
                {{ clientLegalForm }}
            </div>
            <!-- eslint-disable vue/no-v-html -->
            <div
                v-if="careOf"
                class="tw-text-base payer-info"
                v-html="careOf"
            />
            <!-- eslint-enable vue/no-v-html -->
            <div
                v-else
                class="tw-text-base payer-info"
            >
                {{
                    [client?.address?.street, client?.address?.house_number]
                        .filter((val) => val !== null)
                        .join(' ')
                }}
                <br />
                {{ client.address.zipcode }} {{ client.address.city }}
            </div>
            <div
                class="tw-flex tw-flex-grow tw-items-end tw-font-semibold tw-text-base invoice-kind"
            >
                {{ invoice?.kind?.label }}
            </div>
        </div>

        <!-- Vendor details -->
        <div class="tw-text-left pt-0 contractor tw-w-[18rem]">
            <div class="tw-flex tw-justify-end contractor-logo-container">
                <img
                    ref="companyLogoRef"
                    :src="logo"
                    class="tw-object-fill w-full contractor-logo"
                    @load="imageLoaded"
                />
            </div>
            <div
                v-if="showClientAndCustomerDetails"
                class="tw-font-semibold tw-text-base"
            >
                {{ company.name }}
                {{ companyLegalForm }}
            </div>
            <div
                v-if="showClientAndCustomerDetails"
                class="tw-font-semibold tw-text-sm"
            >
                <div>
                    {{
                        [
                            company?.address?.street,
                            company?.address?.house_number,
                        ].join(' ')
                    }}
                </div>
                <div>
                    {{
                        [
                            company?.address?.zipcode,
                            company?.address?.city,
                        ].join(' ')
                    }}
                </div>
            </div>
            <div
                v-if="companyHasWebsite && showClientAndCustomerDetails"
                class="tw-font-semibold tw-text-[10px] tw-underline contractor-contact-info"
            >
                <div>
                    <span>
                        {{ company?.invoice_defaults?.website }}
                    </span>
                </div>
                <div>
                    <span>
                        {{ company?.invoice_defaults?.company_email }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
