<script setup>
import AddressAutocomplete from '@/Apps/Shared/Components/AutoComplete/AddressAutocomplete.vue'
import { useDefaultAddress } from '@/Composables/useDefaults'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import AddressFields from '@components/Fields/AddressFields.vue'
import { useForm } from '@inertiajs/vue2'
import { computed, ref } from 'vue'

const emit = defineEmits(['select', 'close'])

const { $route, $root } = useGlobalVariables()

const getDefaultAddress = () => useDefaultAddress

const addressForm = useForm({
    address: getDefaultAddress(),
    label: 'Delivery address',
})

const wantsToCreateNew = ref(false)
const autocompleteAddress = ref(null)

const completeAddress = computed(() => {
    if (!addressForm.address) return

    const { country, zipcode, house_number, street, city, state } =
        addressForm.address
    let countryName = country?.name

    return Object.values({
        street,
        house_number,
        zipcode,
        city,
        countryName,
        state,
    })
        .filter(Boolean)
        .join(', ')
})

function showNewForm() {
    wantsToCreateNew.value = true
    reset()
}

function save() {
    if (!wantsToCreateNew.value && autocompleteAddress.value) {
        addressForm.address = autocompleteAddress.value
    }

    addressForm.post($route('account.address.store'), {
        preserveState: true,
        preserveScroll: true,
        only: ['ownedPlaces', 'flash_data', 'errors'],
        onSuccess: ({ props: { flash_data: id } }) => {
            $root.$emit('flash.success', 'New address has been added.')
            emit('select', { id })
            close()
        },
        onError: (errors) => $root.$emit('flash.validation', errors),
    })
}

function close() {
    wantsToCreateNew.value = false
    autocompleteAddress.value = null
    reset()
    emit('close')
}

function reset() {
    addressForm.clearErrors()
    addressForm.reset()
}
</script>
<template>
    <div class="tw-flex tw-flex-col tw-px-3">
        <div class="tw-font-bold tw-text-base mb-2">Add delivery address</div>
        <v-flex
            xs12
            px-0
        >
            <v-text-field
                v-model="addressForm.label"
                label="Address label"
                outline
                :hide-details="!addressForm.errors.label"
                :error-messages="addressForm.errors.label"
            />
        </v-flex>
        <span v-show="!wantsToCreateNew">
            <v-flex
                xs12
                px-0
            >
                <AddressAutocomplete
                    v-model="autocompleteAddress"
                    outline
                    color="primary"
                    placeholder="Search address, country state or city"
                    single-line
                    :disabled="addressForm.processing"
                    :loading="addressForm.processing"
                    :menu-props="{
                        closeOnContentClick: true,
                    }"
                    @click:create-new="showNewForm"
                />
            </v-flex>
        </span>
        <span v-show="wantsToCreateNew">
            <v-flex
                xs12
                px-0
            >
                <v-text-field
                    label="Complete address"
                    outline
                    readonly
                    disabled
                    :value="completeAddress"
                />
            </v-flex>
            <AddressFields
                v-model="addressForm.address"
                :errors="addressForm.errors"
                :error-key="`address`"
            />
        </span>

        <div class="tw-text-right tw-mr-0 tw-mt-theme-4">
            <v-btn
                color="primary"
                class="mr-0"
                @click="save"
            >
                Save and Select
            </v-btn>
        </div>
    </div>
</template>
