<script setup>
import AddressbookAutocomplete from '@/Apps/Shared/Components/AutoComplete/AddressbookAutocomplete.vue'
import CareOfAutoComplete from '@/Apps/Shared/Components/AutoComplete/CareOfAutoComplete.vue'
import OrderAutocomplete from '@/Apps/Shared/Components/AutoComplete/OrderAutocomplete.vue'
import AddressAutocomplete from '@/Components/AddressAutocomplete.vue'
import FileAttachmentFormMenu from '@/Components/Fields/File/FileAttachmentFormMenu.vue'
import MediaDrawer from '@/Components/Media/MediaDrawer.vue'
import { useDate, useDateFormat } from '@/Composables/useDayJs'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import { getPageProps, isEmpty, isNotEmpty } from '@/Composables/useUtils'
import JumboUploadButton from '@/Pages/Order/Files/Components/JumboUploadButton.vue'
import SubmitButton from '@/Components/Forms/SubmitButton.vue'
import { router, useForm } from '@inertiajs/vue2'
import axios from 'axios'
import { computed, provide, ref } from 'vue'
const { $t, $route } = useGlobalVariables()

const props = defineProps({
    order: {
        //required: true,
        type: Object,
        default: () => ({}),
    },
    invoiceDefaults: {
        required: false,
        type: Object,
        default: () => ({
            payment_terms: null,
        }),
    },
})

const emit = defineEmits(['flash.validation', 'refresh'])

const form = useForm({
    biller: null,
    order: null,
    delivery_address: null,
    delivery_date: null,
    payment_date: null,
    documents: [],
    external_id: null,
    type: null,
    kind: null,
    care_of_id: null,
    invoice_deductions: [],
})

const isShow = ref(false)
const invoice = ref(null)
const documents = ref([])
const mediaDrawerRef = ref(null)
const paidInvoices = ref([])

const isIncoming = computed(() => form.type == 1)
const isOutgoing = computed(() => form.type == 2)
const isCreating = computed(() => !invoice.value)
const title = computed(() =>
    isCreating.value
        ? $t('invoices.form.create-invoice')
        : $t('invoices.form.update-invoice'),
)

const kindItems = computed(() => getPageProps('invoiceKinds', []))

const attachFromMedia = (media) => {
    form.documents.push(media.uuid)
    documents.value.push(media)
}

const openMediaDrawer = () => mediaDrawerRef.value?.show()

const fetchInvoice = async (selectedInvoice) => {
    try {
        form.processing = true
        const { data } = await axios.get(
            $route('json.invoices.show', {
                invoice: selectedInvoice,
            }),
        )
        return data
    } catch (error) {
        console.error(error)
    } finally {
        form.processing = false
    }
}

const setInvoice = async (selectedInvoice) => {
    const { order, biller, delivery_address } = await fetchInvoice(
        selectedInvoice,
    )
    invoice.value = selectedInvoice
    form.delivery_date = useDateFormat(
        selectedInvoice.delivery_date,
        'YYYY-MM-DD',
    )
    form.payment_date = useDateFormat(
        selectedInvoice.payment_date,
        'YYYY-MM-DD',
    )

    form.biller = biller
    form.order = order
    form.delivery_address = delivery_address

    form.external_id = selectedInvoice?.external_id
    form.type = selectedInvoice.type.value
}

const show = (invoice = null, type = null) => {
    if (invoice) {
        setInvoice(invoice)
    }

    if (isNotEmpty(props.order) && type) {
        setDefaultValues(props.order, type)
    }

    // Set default value for payment_date
    if (props.invoiceDefaults.payment_terms) {
        const paymentDate = useDate().add(
            props.invoiceDefaults.payment_terms,
            'days',
        )

        form.payment_date = paymentDate.format('YYYY-MM-DD')
    }

    // Set default value for biller if order is not set
    if (isEmpty(props.order) && isNotEmpty(type)) {
        if (type == 'outgoing') {
            const currentCompany = getPageProps('user.tenant')

            if (currentCompany) {
                form.biller = {
                    ...currentCompany,
                    ...{
                        column_values: {
                            name: currentCompany.name,
                            email: '',
                            photo: currentCompany.photo,
                        },
                    },
                }
            }

            form.type = 2
        } else {
            form.type = 1
        }
    }

    isShow.value = true
}

const findAddressbook = async (id) => {
    const { data } = await axios.get(
        $route('json.autocomplete.addressbook', {
            id,
        }),
    )
    return data
}

const orderSelected = async (order) => {
    if (isIncoming.value) {
        // Biller = order client
        const clientAddressbook = order?.client_addressbook_id
        const data = await findAddressbook(clientAddressbook)
        form.biller = [...data].shift()
    }

    if (isOutgoing.value) {
        // Biller = order contractor
        const contractor = order?.contractor
        form.biller = {
            ...contractor,
            ...{
                column_values: {
                    name: contractor.name,
                    email: '',
                    photo: contractor.photo,
                },
            },
        }
    }

    form.delivery_address = order?.delivery_address

    getOrderPaidInvoices(order)

    return order
}

const setDefaultValues = async (order, type) => {
    if (type == 'outgoing') {
        // Set default value for type `Outgoing`
        form.type = 2
    }

    if (type == 'incoming') {
        // Set default value for type `Incoming`
        form.type = 1
    }

    form.order = order
    await orderSelected(order)
}

const reset = () => {
    form.reset()
    form.clearErrors()
    invoice.value = null
    documents.value = []
    paidInvoices.value = []
}

const close = () => {
    isShow.value = false
    reset()
}

const submit = () => {
    const method = isCreating.value ? 'post' : 'patch'
    let route = $route('invoices.store')

    if (!isCreating.value)
        route = $route('invoices.update', {
            invoice: invoice.value.id,
        })

    form.transform((data) => ({
        ...data,
        biller: {
            id: data.biller?.id,
            type: data.biller?.type ?? 'addressbooks',
        },
        order: { id: data.order?.id },
        delivery_address: { id: data.delivery_address?.id },
    }))

    form[method](route, {
        preserveState: true,
        preserveScroll: true,
        // only: ['errors', 'flash'],
        onSuccess: ({ props: { flash_data } }) => {
            const invoice = flash_data?.invoice

            if (!invoice) {
                emit('refresh')
                close()
                return
            }

            router.get($route('invoices.show', { invoice }))
        },
        onError: (errors) => {
            emit('flash.validation', errors)
        },
    })
}

const setCareOf = (id) => (form.care_of_id = id)

provide('setCareOf', setCareOf)

defineExpose({
    show,
    close,
})

async function getOrderPaidInvoices(order) {
    paidInvoices.value = []

    if (!order || form.kind !== 3 || form.type !== 2) {
        return
    }

    const { data } = await axios.get($route('json.order.invoices', { order }), {
        params: {
            fullyPaid: true,
        },
    })

    paidInvoices.value = data
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
                            <OrderAutocomplete
                                v-model="form.order"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :label="$t('invoices.form.order')"
                                :placeholder="$t('invoices.form.search-order')"
                                :error-messages="form.errors.order"
                                :hide-details="!form.errors.order"
                                @input="(order) => orderSelected(order)"
                            />
                        </v-flex>

                        <v-flex xs12>
                            <AddressbookAutocomplete
                                v-model="form.biller"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :label="$t('invoices.form.biller')"
                                :error-messages="form.errors.biller"
                                :hide-details="!form.errors.biller"
                            />
                        </v-flex>

                        <v-flex xs12>
                            <CareOfAutoComplete
                                outline
                                full-width
                                :loading="form.processing"
                                :disabled="form.processing"
                                :label="$t('invoices.form.careof')"
                            />

                            <AddressAutocomplete
                                v-model="form.delivery_address"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :label="$t('invoices.form.delivery-address')"
                                :error-messages="form.errors.delivery_address"
                                :hide-details="!form.errors.delivery_address"
                            />
                        </v-flex>

                        <v-flex xs12>
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
                                        :value="form.payment_date"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="
                                            form.errors.payment_date
                                        "
                                        :hide-details="
                                            !form.errors.payment_date
                                        "
                                        :label="
                                            $t('invoices.labels.payment-date')
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
                                    v-model="form.payment_date"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    scrollable
                                    reactive
                                    picker-date
                                />
                            </v-menu>
                        </v-flex>

                        <v-flex xs12>
                            <v-select
                                v-model="form.type"
                                outline
                                full-width
                                :items="$page.props.invoiceTypes"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.type"
                                :hide-details="!form.errors.type"
                                :label="$t('invoices.form.invoice-type')"
                                class="required-input"
                                required
                            />
                        </v-flex>
                        <v-flex xs12>
                            <v-select
                                v-model="form.kind"
                                outline
                                full-width
                                :items="kindItems"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.kind"
                                :hide-details="!form.errors.kind"
                                :label="$t('invoices.labels.kind')"
                                class="required-input"
                                required
                                @change="
                                    (val) => getOrderPaidInvoices(form.order)
                                "
                            />
                        </v-flex>
                        <v-flex
                            v-show="paidInvoices.length"
                            xs12
                        >
                            <v-card>
                                <v-card-title
                                    primary-title
                                    class="pt-3"
                                >
                                    <div class="tw-text-lg">
                                        {{
                                            $t(
                                                'invoices.form.select-deductions',
                                            )
                                        }}:
                                    </div>
                                    <div class="tw-w-full">
                                        <v-checkbox
                                            v-for="(
                                                paidInvoice, index
                                            ) in paidInvoices"
                                            :key="index"
                                            v-model="form.invoice_deductions"
                                            :value="paidInvoice.id"
                                            multiple
                                            hide-details
                                            class="dense tw-w-full block"
                                            color="success"
                                            :height="30"
                                        >
                                            <template #label>
                                                <div
                                                    class="tw-flex tw-items-center tw-w-full"
                                                >
                                                    <div
                                                        class="tw-text-lg success--text tw-font-semibold"
                                                    >
                                                        {{
                                                            paidInvoice.internal_id
                                                        }}
                                                    </div>
                                                    <div
                                                        class="tw-text-sm ml-1"
                                                    >
                                                        ({{
                                                            paidInvoice.total_amount_formatted
                                                        }})
                                                    </div>
                                                    <div class="tw-ml-auto">
                                                        <v-chip
                                                            small
                                                            :color="
                                                                paidInvoice
                                                                    .status
                                                                    .color
                                                            "
                                                            label
                                                            text-color="white"
                                                            class="!tw-text-xs"
                                                        >
                                                            <v-avatar>
                                                                <v-icon
                                                                    color="white"
                                                                    small
                                                                >
                                                                    {{
                                                                        paidInvoice
                                                                            ?.status
                                                                            ?.icon
                                                                    }}
                                                                </v-icon>
                                                            </v-avatar>
                                                            {{
                                                                paidInvoice
                                                                    ?.status
                                                                    .label
                                                            }}
                                                        </v-chip>
                                                    </div>
                                                </div>
                                            </template>
                                        </v-checkbox>
                                    </div>
                                </v-card-title>
                            </v-card>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field
                                v-model="form.external_id"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.external_id"
                                :hide-details="!form.errors.external_id"
                                :label="$t('invoices.form.external_id')"
                                required
                                color="primary"
                                outline
                            />
                        </v-flex>
                        <v-flex xs12>
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
                                        :value="form.delivery_date"
                                        :loading="form.processing"
                                        :disabled="form.processing"
                                        :error-messages="
                                            form.errors.delivery_date
                                        "
                                        :hide-details="
                                            !form.errors.delivery_date
                                        "
                                        :label="
                                            $t('invoices.form.delivery_date')
                                        "
                                        required
                                        readonly
                                        color="primary"
                                        outline
                                        v-on="on"
                                    />
                                </template>
                                <v-date-picker
                                    v-model="form.delivery_date"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    scrollable
                                    reactive
                                    picker-date
                                />
                            </v-menu>
                        </v-flex>

                        <v-flex
                            xs12
                            mt-1
                        >
                            <v-label> {{ $t('labels.documents') }}: </v-label>
                            <v-container
                                v-if="documents.length"
                                grid-list-sm
                                fluid
                                pt-0
                                px-0
                                mt-1
                            >
                                <v-layout
                                    row
                                    wrap
                                >
                                    <v-flex
                                        v-for="(file, fileIndex) in documents"
                                        :key="fileIndex"
                                        xs4
                                        d-flex
                                    >
                                        <v-card
                                            flat
                                            tile
                                            class="d-flex"
                                            hover
                                        >
                                            <v-img
                                                :src="file.url"
                                                aspect-ratio="1"
                                                class="grey lighten-2"
                                            />
                                        </v-card>
                                    </v-flex>
                                    <v-flex
                                        xs4
                                        d-flex
                                        class="tw-items-center"
                                    >
                                        <v-card
                                            tile
                                            flat
                                            hover
                                            height="100%"
                                            class="tw-text-center card-border-dashed tw-cursor-pointer !tw-flex tw-justify-center tw-items-center tw-flex-col"
                                            @click="
                                                (e) =>
                                                    $refs.fileAttachmentFormMenu.show(
                                                        e,
                                                    )
                                            "
                                        >
                                            <v-icon
                                                color="primary"
                                                size="60"
                                            >
                                                add
                                            </v-icon>
                                            <v-card-actions
                                                class="tw-justify-center"
                                            >
                                                {{
                                                    $t(
                                                        'invoices.form.add-documents',
                                                    )
                                                }}
                                            </v-card-actions>
                                        </v-card>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-flex>
                        <v-flex
                            v-show="!documents.length"
                            xs12
                        >
                            <JumboUploadButton
                                v-show="!form.documents.length"
                                :title="$t('invoices.form.add-documents')"
                                :container-size="{ xs12: true }"
                                @click:card="
                                    (e) => $refs.fileAttachmentFormMenu.show(e)
                                "
                            >
                                <div class="v-messages error--text">
                                    {{ form.errors.documents }}
                                </div>
                            </JumboUploadButton>
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
                    <template #loader>
                        <span>
                            {{
                                isCreating
                                    ? $t('buttons.creating')
                                    : $t('buttons.updating')
                            }}
                            ...
                        </span>
                    </template>
                </SubmitButton>
            </v-card-actions>
        </v-card>
        <FileAttachmentFormMenu
            ref="fileAttachmentFormMenu"
            :allowed-types="$page.props.allowedTypes"
            @attach:from-file="(file) => attachFromMedia(file)"
            @attach:from-media="openMediaDrawer"
        />

        <MediaDrawer
            ref="mediaDrawerRef"
            :allowed-types="['images', 'videos', 'documents']"
            right
            absolute
            width="320"
            style="z-index: 10"
            @attach="(media) => attachFromMedia(media)"
        />
    </v-dialog>
</template>
