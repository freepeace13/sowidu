<script setup>
import AddressbookAutocomplete from '@/Apps/Shared/Components/AutoComplete/AddressbookAutocomplete.vue'
import OrderAutocomplete from '@/Apps/Shared/Components/AutoComplete/OrderAutocomplete.vue'
import { useDateFormat, useDateNow } from '@/Composables/useDayJs'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps, isNotEmpty } from '@/Composables/useUtils'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import { useForm } from '@inertiajs/vue2'
import axios from 'axios'
import { computed, ref } from 'vue'

defineExpose({ show })

const props = defineProps({
    order: {
        required: false,
        type: Object,
        default: () => ({}),
    },
    disableAddressbook: {
        required: false,
        type: Boolean,
        default: false,
    },
    disableOrder: {
        required: false,
        type: Boolean,
        default: false,
    },
})

const form = useForm({
    title: null,
    description: null,
    recipient: null,
    offer_date: null,
    type: null,
    order: null,
})

const isShow = ref(false)
const offer = ref(null)
const orderItems = ref([])
const orderItemsLoading = ref(false)

const { $t, $route } = useGlobalVariables()

const isCreating = computed(() => !offer.value)
const title = computed(() =>
    isCreating.value
        ? $t('offer.labels.create_offer')
        : $t('offer.labels.edit_offer'),
)
const hasOrder = computed(() => props.order && isNotEmpty(props.order))

function show(model, type = '') {
    if (type) {
        form.type = type
    }

    form.offer_date = useDateNow()

    if (model) {
        offer.value = model

        form.offer_date = useDateFormat(model.offer_date, 'YYYY-MM-DD')
        form.title = model.title
        form.description = model.description
        form.recipient = model.recipient.addressbook
        form.type = model.type.value
        form.order = model.order
    }

    if (!model && !hasOrder.value) {
        orderItems.value = []
    }

    if (hasOrder.value) {
        const newOrder = props.order
        orderItems.value = [newOrder]
        form.order = newOrder

        orderSelected(newOrder)
    }

    isShow.value = true
}

function reset() {
    form.reset()
    form.clearErrors()
}

function close() {
    isShow.value = false
    reset()
}

function submit() {
    const method = isCreating.value ? 'post' : 'patch'
    let route = $route('offers.store')

    if (!isCreating.value)
        route = $route('offers.update', {
            offer: offer.value.id,
        })

    form.transform((data) => ({
        ...data,
        recipient: {
            id: data.recipient?.id,
            type: data.recipient?.type ?? 'addressbooks',
        },
        order: {
            id: data.order?.id,
        },
    }))

    form[method](route, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            close()
        },
    })
}

async function findAddressbook(id) {
    const { data } = await axios.get(
        $route('json.autocomplete.addressbook', {
            id,
        }),
    )
    return data
}

async function orderSelected(order) {
    if (form.recipient != null || form.recipient) {
        return
    }

    const clientAddressbook = order?.client_addressbook_id
    const data = await findAddressbook(clientAddressbook)

    form.recipient = [...data].shift()
}

async function orderSearch(search) {
    if (!search) {
        return
    }

    try {
        orderItemsLoading.value = true
        orderItems.value = []

        const { data } = await axios.get(
            $route('json.autocomplete.orders', {
                q: search,
                not_in_statuses: [2], // 2 = cancelled
            }),
        )
        orderItems.value = data
    } catch (error) {
        console.error(error)
    } finally {
        orderItemsLoading.value = false
    }
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        persistent
        :max-width="$vuetify.breakpoint.mdAndUp ? '80%' : '100%'"
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ title }}
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
                    <v-layout
                        row
                        wrap
                    >
                        <v-flex xs12>
                            <AddressbookAutocomplete
                                v-model="form.recipient"
                                :loading="form.processing"
                                :disabled="
                                    form.processing || disableAddressbook
                                "
                                :label="$t('offer.inputs.recipient')"
                                :error-messages="form.errors.recipient"
                                :hide-details="!form.errors.recipient"
                                :placeholder="
                                    $t('offer.hints.search-recipient')
                                "
                                class="required-input"
                            />
                        </v-flex>

                        <v-flex xs6>
                            <v-menu
                                :close-on-content-click="true"
                                lazy
                                transition="scale-transition"
                                offset-y
                                full-width
                                min-width="290px"
                            >
                                <template #activator="{ on }">
                                    <v-text-field
                                        :value="form.offer_date"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="form.errors.offer_date"
                                        :hide-details="!form.errors.offer_date"
                                        :label="$t('offer.inputs.offer_date')"
                                        required
                                        readonly
                                        color="primary"
                                        outline
                                        class="required-input"
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker
                                    v-model="form.offer_date"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    scrollable
                                    reactive
                                    picker-date
                                />
                            </v-menu>
                        </v-flex>

                        <v-flex xs6>
                            <v-select
                                v-model="form.type"
                                outline
                                full-width
                                :items="getPageProps('offerTypes', [])"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.type"
                                :hide-details="!form.errors.type"
                                :label="$t('offer.inputs.type')"
                                class="required-input"
                                required
                            />
                        </v-flex>

                        <v-flex xs12>
                            <OrderAutocomplete
                                v-model="form.order"
                                :loading="orderItemsLoading || form.processing"
                                :disabled="form.processing || disableOrder"
                                :label="$t('offer.inputs.order')"
                                :placeholder="$t('offer.hints.search-order')"
                                :error-messages="form.errors.order"
                                :hide-details="!form.errors.order"
                                :input-items="orderItems"
                                @input="(order) => orderSelected(order)"
                                @update:search="(search) => orderSearch(search)"
                            />
                        </v-flex>

                        <v-flex xs12>
                            <v-text-field
                                v-model="form.title"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.title"
                                :hide-details="!form.errors.title"
                                :label="$t('offer.inputs.title')"
                                required
                                color="primary"
                                outline
                                class="required-input"
                            />
                        </v-flex>

                        <v-flex xs12>
                            <v-textarea
                                v-model="form.description"
                                :loading="form.processing"
                                :label="$t('offer.inputs.description')"
                                :error-messages="form.errors.description"
                                :hide-details="!form.errors.description"
                                outline
                            />
                        </v-flex>
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
                    :disabled="form.processing"
                    @click="submit"
                >
                    {{
                        isCreating ? $t('buttons.create') : $t('buttons.update')
                    }}
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
