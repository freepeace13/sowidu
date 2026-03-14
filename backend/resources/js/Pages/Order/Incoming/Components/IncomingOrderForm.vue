<script setup>
import {
    useSearchAddressbook,
    useShowAddressbook,
} from '@/Composables/useAddressbook'
import { useDateIsBefore, useDateNow } from '@/Composables/useDayJs'
import { useDefaultAddress } from '@/Composables/useDefaults'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import { useForm, usePage } from '@inertiajs/vue2'
import { useDebounceFn } from '@vueuse/core'
import { computed, reactive, ref, watch } from 'vue'
import SearchInput from '../../../../Components/Fields/SearchInput.vue'
import ClientDetails from './ClientDetails.vue'
import DeliveryAddressForm from './DeliveryAddressForm.vue'
import ForeignClientForm from './ForeignClientForm.vue'

const emit = defineEmits(['refresh'])

defineExpose({ show })

const { $t, $route, $root, $confirm } = useGlobalVariables()
const page = usePage()

const form = useForm({
    id: null,
    client: {
        address: useDefaultAddress,
    },
    description: '',
    order_date: useDateNow(),
    planned_start_date: null,
    planned_finish_date: null,
    delivery_address: useDefaultAddress,
})

const deliverySameToCurrentAddress = ref(true)
const isShow = ref(false)
const client = ref(null)
const clientIsForeign = ref(false)
const deliveryAddressForm = ref(null)

const search = reactive({
    isLoading: false,
    items: [],
    input: '',
})

const hasChosenClient = computed(() => client.value || clientIsForeign.value)

const defaultClientAvatar = computed(
    () => page.props.defaults.avatars.foreign_client,
)

const deliveryCompleteAddress = computed(() => {
    if (deliverySameToCurrentAddress.value) return form.client?.address?.full

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

const searchPerson = useDebounceFn(async (keyword) => {
    try {
        search.input = keyword
        if (!keyword) return

        search.isLoading = true
        search.items = []
        const { data } = await useSearchAddressbook(keyword, 5)
        search.items = data
    } catch (error) {
        $root.$emit('flash.error', error)
    } finally {
        search.isLoading = false
    }
}, 500)

watch(
    client,
    async (selected) => {
        if (!selected || !selected?.id) return

        const { data: clientData } = await useShowAddressbook(selected.id)
        setClient(clientData)
    },
    { deep: true },
)

function show() {
    form.reset()
    isShow.value = true
}

function close() {
    isShow.value = false
    reset()
}

function getDefaultAddress() {
    return useDefaultAddress
}

function reset() {
    form.reset()
    form.clearErrors()

    client.value = null
    clientIsForeign.value = false
}

function foreignerSelected() {
    clientIsForeign.value = true
    const [first_name, ...lastName] = search.input.split(' ')

    setClient({
        first_name,
        last_name: lastName.length ? lastName.join(' ') : '',
        email: null,
        phone: null,
        address: getDefaultAddress(),
    })
}

function setClient(clientData) {
    form.client = clientData
    form.delivery_address.id = clientData.address.id
}

function submit() {
    const routeName = clientIsForeign.value
        ? 'orders.incoming.foreign_client.store'
        : 'orders.incoming.store'

    if (!clientIsForeign.value) {
        form.transform((data) => ({
            ...data,
            client_id: client.value.id,
        }))
    }

    if (clientIsForeign.value) {
        // Add delivery-address
        let delivery_address = form.delivery_address
        if (deliverySameToCurrentAddress.value) {
            delivery_address = form.client.address
        }

        form.transform((data) => ({
            ...data,
            delivery_address,
            client: {
                ...data.client,
                address: {
                    ...data.client.address,
                    country: data.client.address.country?.code,
                },
            },
        }))
    }

    form.post($route(routeName), {
        preserveState: true,
        preserveScroll: true,
        only: ['paginator', 'orders'],
        onSuccess: () => {
            $root.$emit('flash.success', 'New incoming order has been created.')
            emit('refresh')

            close()
        },
        onError: (errors) => $root.$emit('flash.validation', errors),
    })
}

function deliveryAddressChanged(isSameAsAddress) {
    form.delivery_address = getDefaultAddress()
    if (isSameAsAddress) {
        // Delivery address is same as current address
        form.delivery_address.id = form.client.address.id
    }

    if (!isSameAsAddress) {
        deliveryAddressForm.value?.show()
    }
}

function checkSelectedDate(date) {
    if (useDateIsBefore(date)) {
        $confirm.ask({
            title: 'Attention',
            question: 'The date is in the past, are you sure?',
            type: 'info',
            confirm: () => {},
            cancel: () => (form.planned_start_date = null),
        })
    }
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
                    {{ $t('order.incoming.labels.create-order') }}
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
                        <SearchInput
                            v-show="!clientIsForeign"
                            v-model="client"
                            :items="search.items"
                            :is-loading="search.isLoading"
                            :menu-props="{
                                closeOnContentClick: true,
                            }"
                            placeholder="Search client name or email..."
                            @update:search-input="searchPerson"
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
                                        v-if="!item?.legalform"
                                        small
                                        color="primary"
                                    >
                                        account_circle
                                    </v-icon>
                                    <v-icon
                                        v-if="item?.legalform"
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
                                            Add "{{ search.input }}" as new
                                            Contact
                                        </v-list-tile-title>
                                    </v-list-tile-content>
                                </v-list-tile>
                            </template>
                        </SearchInput>
                        <v-divider
                            v-if="!clientIsForeign"
                            class="my-2"
                        />
                        <v-expand-transition>
                            <!-- Client Details -->
                            <div v-show="hasChosenClient">
                                <ForeignClientForm
                                    v-if="clientIsForeign"
                                    v-model="form"
                                />
                                <ClientDetails
                                    v-if="!clientIsForeign"
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
                                                    label="Same as client address"
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
                                        <v-card-text
                                            v-show="
                                                !deliverySameToCurrentAddress
                                            "
                                            class="px-2"
                                        >
                                            <DeliveryAddressForm
                                                ref="deliveryAddressForm"
                                                v-model="form.delivery_address"
                                                :errors="form.errors"
                                            />
                                        </v-card-text>
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
                                                    label="Order Date"
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
                                                    label="Planned Start Date"
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
                                                @input="
                                                    (val) =>
                                                        checkSelectedDate(val)
                                                "
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
                                                    label="Planned finished date"
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
                                                @input="
                                                    (val) =>
                                                        checkSelectedDate(val)
                                                "
                                            />
                                        </v-menu>
                                    </v-flex>
                                </v-layout>

                                <v-flex xs12>
                                    <v-textarea
                                        v-model="form.description"
                                        :loading="form.processing"
                                        label="Order description"
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
                    Cancel
                </v-btn>
                <v-spacer />
                <SubmitButton
                    :loading="form.processing"
                    :disabled="form.processing || !hasChosenClient"
                    @click="submit"
                >
                    {{ $t('buttons.create') }}
                    <template #loader>
                        <span>{{ $t('buttons.creating') }}...</span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style scoped>
.v-expansion-panel__header {
    padding-left: 0px;
    padding-right: 0px;
}
</style>
