<script setup>
import SearchInput from '@/Components/Fields/SearchInput.vue'
import {
    useOrganizationSearch,
    useShowAddressbook,
} from '@/Composables/useAddressbook'
import { useDateNow } from '@/Composables/useDayJs'
import { useDefaultAddress } from '@/Composables/useDefaults'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import { router, useForm, usePage } from '@inertiajs/vue2'
import { useDebounceFn } from '@vueuse/core'
import { computed, reactive, ref, watch } from 'vue'
import SelectAddressForm from '../../Components/SelectAddressForm.vue'
import ContractorDetails from './ContractorDetails.vue'
import ForeignContractorForm from './ForeignContractorForm.vue'

defineExpose({ show })

const props = defineProps({
    ownedPlaces: {
        type: Array,
        required: true,
    },
    currentAddress: {
        required: true,
        type: Object,
    },
})

const { $t, $route, $root } = useGlobalVariables()
const page = usePage()

const getDefaultAddress = () => useDefaultAddress
const defaultDate = () => useDateNow()

const form = useForm({
    id: null,
    contractor: {
        address: getDefaultAddress(),
    },
    description: '',
    order_date: defaultDate(),
    planned_start_date: null,
    planned_finish_date: null,
    delivery_address: getDefaultAddress(),
})

const deliverySameToCurrentAddress = ref(true)
const isShow = ref(false)
const selectedContractor = ref(null)
const contractorIsForeign = ref(false)
const selectAddressFormRef = ref(null)

const search = reactive({
    isLoading: false,
    items: [],
    input: '',
})

const hasChosenContractor = computed(
    () => selectedContractor.value || contractorIsForeign.value,
)

const defaultClientAvatar = computed(
    () => page.props.defaults.avatars.foreign_contractor,
)

const deliveryCompleteAddress = computed(() => {
    if (deliverySameToCurrentAddress.value)
        return defaultDeliveryAddress.value?.full

    try {
        const { country, zipcode, house_number, street, city, state } =
            form.delivery_address
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
    } catch (error) {
        return null
    }
})

const defaultDeliveryAddress = computed(() => {
    if (!props.currentAddress) return useDefaultAddress()

    return props.currentAddress
})

watch(
    selectedContractor,
    async (selected) => {
        if (!selected || !selected?.id) return

        const { data: contractor } = await useShowAddressbook(selected.id)
        setContractor(contractor)
    },
    { deep: true },
)

const searchOrganization = useDebounceFn(
    async (keyword) => searchOrganizations(keyword),
    500,
)

async function searchOrganizations(keyword) {
    try {
        search.input = keyword

        if (isShow.value && !selectedContractor.value && !keyword)
            return getSuggestions()

        if (!keyword || (selectedContractor.value && !isShow.value)) return

        search.isLoading = true
        search.items = []
        const { data } = await useOrganizationSearch(keyword, 5)
        search.items = data
    } catch (error) {
        $root.$emit('flash.error', error)
    } finally {
        search.isLoading = false
    }
}

async function getSuggestions() {
    const { data } = await useOrganizationSearch(null, 5)
    search.items = data
}

async function show() {
    form.reset()
    setupClientDetails()
    isShow.value = true
}

function setupClientDetails() {
    // Set default delivery address to client - current address
    form.delivery_address = props.currentAddress
}

function close() {
    isShow.value = false
    reset()
}

function reset() {
    form.reset()
    form.clearErrors()
    selectedContractor.value = null
    contractorIsForeign.value = false
}

function foreignerSelected() {
    contractorIsForeign.value = true
    const [first_name, ...lastName] = search.input.split(' ')

    setContractor({
        first_name,
        last_name: lastName.length ? lastName.join(' ') : '',
        email: null,
        phone: null,
        address: getDefaultAddress(),
    })
}

function setContractor(contractor) {
    form.contractor = contractor
}

function submit() {
    const routeName = contractorIsForeign.value
        ? 'orders.outgoing.foreign_contractor.store'
        : 'orders.outgoing.store'

    if (!contractorIsForeign.value) {
        form.transform((data) => ({
            ...data,
            contractor_id: selectedContractor.value.id,
        }))
    }

    if (contractorIsForeign.value) {
        // Add delivery-address
        let delivery_address = form.delivery_address
        if (deliverySameToCurrentAddress.value) {
            delivery_address = form.contractor.address
        }

        form.transform((data) => ({
            ...data,
            delivery_address,
            contractor: {
                ...data.contractor,
                address: {
                    ...data.contractor.address,
                    country: data.contractor.address.country?.code,
                },
            },
        }))
    }

    form.post($route(routeName), {
        preserveState: true,
        preserveScroll: true,
        only: ['orders', 'paginator'],
        onSuccess: () => {
            $root.$emit(
                'flash.success',
                $t('order.notifications.outgoing.created'),
            )
            close()
        },
        onError: (errors) => $root.$emit('flash.validation', errors),
    })
}

function deliveryAddressChanged(isSameAsAddress) {
    form.delivery_address = getDefaultAddress()
    if (isSameAsAddress) {
        // Delivery address is same as current address
        form.delivery_address.id = form.contractor.address.id
    }

    if (!props.ownedPlaces.length) {
        router.reload({ only: ['ownedPlaces'] })
    }

    if (!isSameAsAddress) {
        selectAddressFormRef.value?.show()
    }
}

function selectDeliveryAddress(id) {
    form.delivery_address = props.ownedPlaces.find(
        (address) => address.id == id,
    )
}
</script>
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
                    {{ $t('order.outgoing.labels.create-order') }}
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
                            <SearchInput
                                v-show="!contractorIsForeign"
                                v-model="selectedContractor"
                                :items="search.items"
                                :is-loading="search.isLoading"
                                :menu-props="{
                                    closeOnContentClick: true,
                                }"
                                :placeholder="
                                    $t('order.hints.search-contractor')
                                "
                                @update:search-input="searchOrganization"
                                @focus="getSuggestions"
                            >
                                <template #item="item">
                                    <v-list-tile-avatar>
                                        <img :src="item.photo" />
                                    </v-list-tile-avatar>
                                    <v-list-tile-content>
                                        <v-list-tile-title>
                                            {{ item.name }}
                                        </v-list-tile-title>
                                        <v-list-tile-sub-title>
                                            {{ item.email }}
                                        </v-list-tile-sub-title>
                                    </v-list-tile-content>
                                    <v-list-tile-action>
                                        <v-icon
                                            small
                                            color="secondary"
                                        >
                                            business
                                        </v-icon>
                                    </v-list-tile-action>
                                </template>
                                <template #no-data>
                                    <v-list-tile
                                        v-show="search.input"
                                        avatar
                                        ripple
                                        primary
                                        class="tw-cursor-pointer hover:tw-bg-grey-400"
                                        @mousedown.prevent="foreignerSelected"
                                        @click.stop.prevent="foreignerSelected"
                                    >
                                        <v-list-tile-avatar>
                                            <img :src="defaultClientAvatar" />
                                        </v-list-tile-avatar>
                                        <v-list-tile-content>
                                            <v-list-tile-title>
                                                {{
                                                    $t(
                                                        'order.hints.add-input-as-new-contact',
                                                        {
                                                            input: search.input,
                                                        },
                                                    )
                                                }}
                                            </v-list-tile-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                </template>
                            </SearchInput>
                        </v-flex>
                        <v-divider
                            v-if="!contractorIsForeign"
                            class="my-2"
                        />
                        <v-expand-transition>
                            <!-- Client Details -->
                            <div v-show="hasChosenContractor">
                                <ForeignContractorForm
                                    v-if="contractorIsForeign"
                                    v-model="form"
                                />
                                <ContractorDetails
                                    v-if="!contractorIsForeign"
                                    v-model="form"
                                />

                                <v-flex xs12>
                                    <div
                                        class="tw-text-primary tw-font-bold !tw-text-sm mb-3"
                                    >
                                        {{
                                            $t('order.labels.delivery-address')
                                        }}
                                    </div>
                                    <v-card
                                        color="white"
                                        class="mx-2"
                                    >
                                        <v-card-title
                                            primary-title
                                            class="pa-2"
                                        >
                                            <div>
                                                <v-checkbox
                                                    v-model="
                                                        deliverySameToCurrentAddress
                                                    "
                                                    :label="
                                                        $t(
                                                            'labels.my-current-address',
                                                        )
                                                    "
                                                    class="pt-0"
                                                    hide-details
                                                    :loading="form.processing"
                                                    :disabled="form.processing"
                                                    @change="
                                                        deliveryAddressChanged
                                                    "
                                                />
                                                <div class="tw-text-lg mt-2">
                                                    {{
                                                        deliveryCompleteAddress
                                                    }}
                                                </div>
                                            </div>
                                        </v-card-title>
                                    </v-card>
                                </v-flex>

                                <v-divider class="mt-3" />
                                <v-flex>
                                    <v-subheader
                                        px-0
                                        class="subheading tw-text-primary tw-font-bold"
                                    >
                                        {{ $t('order.labels.order-details') }}
                                    </v-subheader>
                                </v-flex>
                                <v-layout
                                    row
                                    wrap
                                    px-2
                                >
                                    <v-flex
                                        sm6
                                        offset-sm6
                                        xs12
                                    >
                                        <v-menu
                                            :close-on-content-click="false"
                                            lazy
                                            transition="scale-transition"
                                            offset-y
                                            full-width
                                            min-width="290px"
                                        >
                                            <template #activator="{ on }">
                                                <v-text-field
                                                    :value="form.order_date"
                                                    :loading="form.processing"
                                                    :disabled="form.processing"
                                                    :error-messages="
                                                        form.errors.order_date
                                                    "
                                                    :hide-details="
                                                        !form.errors.order_date
                                                    "
                                                    :label="
                                                        $t(
                                                            'order.labels.inputs.order-date',
                                                        )
                                                    "
                                                    required
                                                    readonly
                                                    color="primary"
                                                    outline
                                                    class="required-input"
                                                    v-on="on"
                                                />
                                            </template>
                                            <v-date-picker
                                                v-model="form.order_date"
                                                :loading="form.processing"
                                                :disabled="form.processing"
                                                scrollable
                                                reactive
                                                picker-date
                                            />
                                        </v-menu>
                                    </v-flex>
                                    <v-flex
                                        sm6
                                        xs12
                                    >
                                        <v-menu
                                            :close-on-content-click="false"
                                            lazy
                                            transition="scale-transition"
                                            offset-y
                                            full-width
                                            min-width="290px"
                                        >
                                            <template #activator="{ on }">
                                                <v-text-field
                                                    :value="
                                                        form.planned_start_date
                                                    "
                                                    :loading="form.processing"
                                                    :disabled="form.processing"
                                                    :error-messages="
                                                        form.errors
                                                            .planned_start_date
                                                    "
                                                    :hide-details="
                                                        !form.errors
                                                            .planned_start_date
                                                    "
                                                    :label="
                                                        $t(
                                                            'order.labels.inputs.planned-start-date',
                                                        )
                                                    "
                                                    required
                                                    readonly
                                                    color="primary"
                                                    outline
                                                    v-on="on"
                                                />
                                            </template>
                                            <v-date-picker
                                                v-model="
                                                    form.planned_start_date
                                                "
                                                :loading="form.processing"
                                                :disabled="form.processing"
                                                scrollable
                                                reactive
                                                picker-date
                                            />
                                        </v-menu>
                                    </v-flex>
                                    <v-flex
                                        sm6
                                        xs12
                                    >
                                        <v-menu
                                            :close-on-content-click="false"
                                            lazy
                                            transition="scale-transition"
                                            offset-y
                                            full-width
                                            min-width="290px"
                                        >
                                            <template #activator="{ on }">
                                                <v-text-field
                                                    :value="
                                                        form.planned_finish_date
                                                    "
                                                    :loading="form.processing"
                                                    :disabled="form.processing"
                                                    :error-messages="
                                                        form.errors
                                                            .planned_finish_date
                                                    "
                                                    :hide-details="
                                                        !form.errors
                                                            .planned_finish_date
                                                    "
                                                    :label="
                                                        $t(
                                                            'order.labels.inputs.planned-finish-date',
                                                        )
                                                    "
                                                    required
                                                    readonly
                                                    color="primary"
                                                    outline
                                                    v-on="on"
                                                />
                                            </template>
                                            <v-date-picker
                                                v-model="
                                                    form.planned_finish_date
                                                "
                                                :loading="form.processing"
                                                :disabled="form.processing"
                                                scrollable
                                                reactive
                                                picker-date
                                            />
                                        </v-menu>
                                    </v-flex>
                                </v-layout>

                                <v-flex xs12>
                                    <v-textarea
                                        v-model="form.description"
                                        :loading="form.processing"
                                        :label="
                                            $t(
                                                'order.labels.inputs.description',
                                            )
                                        "
                                        :error-messages="
                                            form.errors.description
                                        "
                                        :hide-details="!form.errors.description"
                                        outline
                                        class="required-input"
                                    />
                                </v-flex>
                            </div>
                        </v-expand-transition>
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
                    {{ $t('buttons.cancel') }}
                </v-btn>
                <v-spacer />
                <SubmitButton
                    :loading="form.processing"
                    :disabled="form.processing || !hasChosenContractor"
                    @click="submit"
                >
                    {{ $t('buttons.create') }}
                    <template #loader>
                        <span>{{ $t('buttons.creating') }}...</span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
        <SelectAddressForm
            ref="selectAddressFormRef"
            :owned-places="ownedPlaces"
            @select="(id) => selectDeliveryAddress(id)"
        />
    </v-dialog>
</template>

<style scoped>
.v-expansion-panel__header {
    padding-left: 0px;
    padding-right: 0px;
}
</style>
