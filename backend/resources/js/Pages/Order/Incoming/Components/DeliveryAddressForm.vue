<script setup>
import AddressFields from '@components/Fields/AddressFields.vue'
import AddressAutocomplete from '@/Apps/Shared/Components/AutoComplete/AddressAutocomplete.vue'
import { computed, ref } from 'vue'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { router } from '@inertiajs/vue2'

const props = defineProps({
    value: {
        type: Object,
        required: false,
    },
    errors: {
        required: false,
        type: Object,
        default: () => ({}),
    },
})

defineExpose({ show })

const emit = defineEmits(['input'])

const { $t, $route, $root } = useGlobalVariables()

const autocompleteAddress = ref(null)
const wantsToCreateNew = ref(false)
const isShow = ref(false)

const delivery = computed(() => props.value)

function show() {
    isShow.value = true
}

function showCreateNewForm() {
    wantsToCreateNew.value = true
    console.log(
        '🚀 ~ showCreateNewForm ~ showCreateNewForm:',
        wantsToCreateNew.value,
    )
}

function reset() {
    autocompleteAddress.value = null
    wantsToCreateNew.value = false
}

function close() {
    reset()
    isShow.value = false
}

function selectAddress() {
    if (!wantsToCreateNew.value && autocompleteAddress.value) {
        emit('input', autocompleteAddress.value)
        return close()
    }

    const { id, ...address } = delivery.value
    console.log('🚀 ~ selectAddress ~ delivery:', delivery.value)
    console.log('🚀 ~ selectAddress ~ address:', address)

    router.post(
        $route('addressbooks.addresses.store'),
        {
            address: {
                ...address,
                country: address.country?.code,
            },
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['flash_data'],
            onSuccess: ({ props: { flash_data } }) => {
                emit('input', flash_data)
                close()
            },
            onError: (errors) => $root.$emit('flash.validation', errors),
        },
    )
}
</script>
<template>
    <div
        v-show="isShow"
        class="tw-flex tw-flex-col"
    >
        <v-alert
            type="error"
            :value="
                Object.hasOwn(errors, 'delivery_address.id') ||
                Object.hasOwn(errors, 'delivery_address.city') ||
                Object.hasOwn(errors, 'delivery_address.state') ||
                Object.hasOwn(errors, 'delivery_address.country')
            "
            class="tw-w-full tw-mb-3"
        >
            Must add delivery address!
        </v-alert>
        <div class="tw-font-bold tw-text-base mb-2">Add delivery address</div>

        <AddressAutocomplete
            v-show="!wantsToCreateNew"
            v-model="autocompleteAddress"
            outline
            color="primary"
            placeholder="Search address, country state or city"
            single-line
            :menu-props="{
                closeOnContentClick: true,
            }"
            @click:create-new="showCreateNewForm"
        />
        <AddressFields
            v-show="wantsToCreateNew"
            v-model="delivery"
            :errors="errors"
            :error-key="'delivery_address'"
        />

        <div class="tw-text-right tw-mr-0">
            <v-btn
                color="primary"
                class="mt-3 mr-0"
                @click="selectAddress"
            >
                {{ $t('buttons.done') }}
            </v-btn>
        </div>
    </div>
</template>
