<script>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import OrderLayout from '../OrderLayout.vue'
import DeliveryTicketMaterialsRow from './Components/DeliveryTicketMaterialsRow.vue'
import axios from 'axios'
import { router, useForm } from '@inertiajs/vue2'
import OrderProductForm from '../Products/Components/OrderProductForm.vue'
import AddressbookAutocomplete from '@/Apps/Shared/Components/AutoComplete/AddressbookAutocomplete.vue'
import DeliveryTicketAddress from '../../DeliveryTicket/Components/DeliveryTicketAddress.vue'

export default {
    layout: [AuthLayout, OrderLayout],
}
</script>
<script setup>
import { useDateFormat } from '@/Composables/useDayJs'
import TicketDetail from '@/Pages/DeliveryTicket/Components/TicketDetail.vue'
import FileAttachmentFormMenu from '@/Components/Fields/File/FileAttachmentFormMenu.vue'
import MediaDrawer from '@/Components/Media/MediaDrawer.vue'
import { computed, ref, getCurrentInstance } from 'vue'
import useGlobalVariables from '../../../Composables/useGlobalVariables'

const app = getCurrentInstance()
const { $route } = useGlobalVariables()

const props = defineProps({
    deliveryTicket: {
        required: true,
        type: Object,
    },
    order: {
        type: Object,
    },
    permissions: {
        required: true,
        type: Object,
    },
    documents: {
        required: true,
        type: Array,
    },
    totals: {
        required: true,
        type: Object,
    },
})

const ticketMaterialForm = ref(null)
const deliverer = computed(() => props.deliveryTicket.deliverer.id)
const canUpdateTicket = computed(() => props.permissions.can_update_ticket)

const form = useForm({ deliverer: props.deliveryTicket.deliverer })
async function attachFromMedia({ uuid }) {
    const deliveryTicket = props.deliveryTicket.id
    await axios.post(
        window.route('delivery_tickets.documents.store', {
            deliveryTicket,
        }),
        {
            medias: [uuid],
        },
    )

    router.reload({
        only: ['documents'],
    })
}
const update = () => {
    form.transform((data) => ({
        deliverer: data.deliverer.id,
    })).put(
        $route('orders.show.delivery_tickets.update', {
            order: props.order.id,
            deliveryTicket: props.deliveryTicket.id,
        }),
        {
            only: ['errors', 'flash', 'order', 'deliveryTicket'],
            preserveState: true,
            preserveScroll: true,
            onError: (errors) =>
                app.proxy.$root.$emit('flash.validation', errors),
        },
    )
}
</script>
<template>
    <div class="fill-height tw-w-full">
        <portal
            to="toolbar"
            tag="span"
        >
            <v-toolbar
                id="dropdown-example"
                absolute
                top
                flat
                color="white"
            >
                <v-btn
                    v-tooltip.top="`${$t('buttons.go-back')}`"
                    icon
                    class="hidden-xs-only"
                    @click="
                        $inertia.get(
                            $route('orders.show.delivery_tickets.index', {
                                order,
                            }),
                        )
                    "
                >
                    <v-icon>arrow_back</v-icon>
                </v-btn>
                <v-toolbar-title>
                    {{ $tc('headings.delivery_tickets') }}
                    {{ $t('labels.details') }}
                </v-toolbar-title>

                <v-spacer />
            </v-toolbar>
        </portal>

        <v-container
            grid-list-lg
            text-xs-center
            px-0
            class="!tw-max-w-full"
        >
            <v-layout
                row
                wrap
                fill-height
            >
                <v-flex
                    xs12
                    class="!tw-overflow-auto !tw-grow elevation-10"
                >
                    <v-card flat>
                        <v-card-title
                            v-if="deliveryTicket.is_paid"
                            primary-title
                        >
                            <v-alert
                                type="success"
                                :value="true"
                                class="w-full"
                            >
                                {{
                                    $t(
                                        'order.delivery_tickets.message.delivery-ticket-is-paid',
                                    )
                                }}
                            </v-alert>
                        </v-card-title>
                        <v-card-text>
                            <v-container
                                grid-list-xs
                                fluid
                                pa-2
                            >
                                <v-layout
                                    row
                                    wrap
                                >
                                    <v-flex
                                        xs6
                                        mt-3
                                    >
                                        <TicketDetail
                                            :label="
                                                $t(
                                                    'delivery_tickets.form.ticket-no',
                                                )
                                            "
                                            :value="deliveryTicket?.internal_id"
                                        />
                                    </v-flex>
                                    <v-flex xs6>
                                        <TicketDetail
                                            :label="
                                                $t(
                                                    'delivery_tickets.form.external_id',
                                                )
                                            "
                                            :value="deliveryTicket?.external_id"
                                        />
                                    </v-flex>
                                    <v-flex xs6>
                                        <TicketDetail
                                            :label="
                                                $t(
                                                    'delivery_tickets.form.delivery_date',
                                                )
                                            "
                                            :value="
                                                useDateFormat(
                                                    deliveryTicket?.delivery_date,
                                                )
                                            "
                                        />
                                    </v-flex>
                                    <v-flex xs6>
                                        <TicketDetail
                                            :label="$t('labels.date-created')"
                                            :value="
                                                useDateFormat(
                                                    deliveryTicket?.created_at,
                                                )
                                            "
                                        />
                                    </v-flex>
                                    <v-flex xs6>
                                        <TicketDetail
                                            :label="
                                                $t(
                                                    'delivery_tickets.form.ticket-type',
                                                )
                                            "
                                            :value="deliveryTicket?.type?.name"
                                        />
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        mt-4
                                    >
                                        <div
                                            class="tw-text-lg tw-font-semibold tw-text-left"
                                        >
                                            {{
                                                $t(
                                                    'delivery_tickets.form.delivery-address',
                                                )
                                            }}
                                            {{ $t('labels.details') }}:
                                        </div>
                                    </v-flex>
                                    <v-flex
                                        xs3
                                        mt-4
                                    >
                                        <DeliveryTicketAddress
                                            :address="
                                                deliveryTicket.delivery_address ??
                                                deliveryTicket.order.address
                                            "
                                            :delivery-ticket-id="
                                                deliveryTicket.id
                                            "
                                        />
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        mt-4
                                    >
                                        <div
                                            class="tw-text-lg tw-font-semibold tw-text-left"
                                        >
                                            {{
                                                $t(
                                                    'delivery_tickets.form.deliverer',
                                                )
                                            }}
                                            {{ $t('labels.details') }}:
                                        </div>
                                    </v-flex>
                                    <v-flex xs3>
                                        <AddressbookAutocomplete
                                            v-model="form.deliverer"
                                            :loading="form.processing"
                                            :disabled="form.processing"
                                            :label="
                                                $t(
                                                    'delivery_tickets.form.deliverer',
                                                )
                                            "
                                            :error-messages="
                                                form.errors.deliverer
                                            "
                                            :hide-details="
                                                !form.errors.deliverer
                                            "
                                            @input="update"
                                        />
                                    </v-flex>
                                    <v-flex
                                        v-if="deliverer && deliverer.length"
                                        xs9
                                    >
                                        <div>
                                            <TicketDetail
                                                :label="
                                                    $t('labels.inputs.name')
                                                "
                                                :value="
                                                    deliverer?.column_values
                                                        .name
                                                "
                                            />
                                            <TicketDetail
                                                :label="
                                                    $t('labels.inputs.email')
                                                "
                                                class="tw-italic tw-font-light"
                                                :value="
                                                    deliverer?.column_values
                                                        .email
                                                "
                                            />
                                            <TicketDetail
                                                label="Phone"
                                                :value="
                                                    deliverer?.column_values
                                                        ?.phone ?? '--'
                                                "
                                            />
                                            <TicketDetail
                                                :label="
                                                    $t(
                                                        'delivery_tickets.labels.address',
                                                    )
                                                "
                                                :value="
                                                    deliverer?.column_values
                                                        ?.address?.full ?? '--'
                                                "
                                            />
                                        </div>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        mt-4
                                    >
                                        <div
                                            class="tw-text-lg tw-font-semibold tw-text-left"
                                        >
                                            {{
                                                $t(
                                                    'delivery_tickets.form.order',
                                                )
                                            }}
                                            {{ $t('labels.details') }}:
                                        </div>
                                    </v-flex>
                                    <v-flex xs12>
                                        <div class="tw-grid tw-grid-cols-2">
                                            <TicketDetail
                                                :label="$t('labels.order-no')"
                                                :value="order?.order_number"
                                            />
                                            <TicketDetail
                                                :label="
                                                    $t('labels.date-created')
                                                "
                                                :value="
                                                    useDateFormat(
                                                        order?.created_at,
                                                        'ddd, MMM D, YYYY',
                                                    )
                                                "
                                            />
                                            <TicketDetail
                                                :label="
                                                    $t(
                                                        'order.labels.inputs.planned-start-date',
                                                    )
                                                "
                                                :value="
                                                    useDateFormat(
                                                        order?.planned_start_date,
                                                        'ddd, MMM D, YYYY',
                                                    )
                                                "
                                            />
                                            <TicketDetail
                                                :label="
                                                    $t(
                                                        'order.labels.inputs.planned-finish-date',
                                                    )
                                                "
                                                :value="
                                                    useDateFormat(
                                                        order?.planned_finish_date,
                                                        'ddd, MMM D, YYYY',
                                                    )
                                                "
                                            />
                                            <div class="tw-col-span-2">
                                                <TicketDetail
                                                    :label="
                                                        $t(
                                                            'labels.inputs.description',
                                                        )
                                                    "
                                                    :value="order?.description"
                                                />
                                            </div>
                                        </div>
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        mt-4
                                        class="tw-flex tw-justify-between tw-items-center"
                                    >
                                        <div
                                            class="tw-text-lg tw-font-semibold"
                                        >
                                            {{
                                                $t(
                                                    'delivery_tickets.labels.materials',
                                                )
                                            }}:
                                        </div>

                                        <v-btn
                                            v-if="canUpdateTicket"
                                            color="info"
                                            small
                                            @click="
                                                () =>
                                                    ticketMaterialForm.show(
                                                        $route(
                                                            'delivery_tickets.materials.store',
                                                            {
                                                                deliveryTicket,
                                                            },
                                                        ),
                                                    )
                                            "
                                        >
                                            {{
                                                $t(
                                                    'delivery_tickets.buttons.add-materials',
                                                )
                                            }}
                                        </v-btn>
                                    </v-flex>
                                    <v-flex xs12>
                                        <DeliveryTicketMaterialsRow
                                            v-if="deliveryTicket?.id"
                                            :totals="totals"
                                            :delivery-ticket="deliveryTicket"
                                            :can-update-materials="
                                                canUpdateTicket
                                            "
                                            card-text-class="px-0"
                                            @click:add-materials="
                                                (ticket) =>
                                                    ticketMaterialForm.show(
                                                        $route(
                                                            'delivery_tickets.materials.store',
                                                            {
                                                                deliveryTicket,
                                                            },
                                                        ),
                                                    )
                                            "
                                        />
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        mt-4
                                    >
                                        <div
                                            class="tw-text-lg tw-font-semibold tw-text-left"
                                        >
                                            {{
                                                $t(
                                                    'delivery_tickets.labels.documents',
                                                )
                                            }}:
                                        </div>
                                    </v-flex>
                                    <v-flex xs12>
                                        <v-container
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
                                                    v-for="(
                                                        file, fileIndex
                                                    ) in documents"
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
                                                        <v-btn
                                                            v-if="
                                                                canUpdateTicket
                                                            "
                                                            v-tooltip="
                                                                `${$t(
                                                                    'buttons.remove',
                                                                )}`
                                                            "
                                                            fab
                                                            small
                                                            absolute
                                                            color="error"
                                                            @click="
                                                                () =>
                                                                    removeDocument(
                                                                        file.delivery_ticket_document_id,
                                                                    )
                                                            "
                                                        >
                                                            <v-icon
                                                                dark
                                                                small
                                                            >
                                                                close
                                                            </v-icon>
                                                        </v-btn>
                                                    </v-card>
                                                </v-flex>
                                                <v-flex xs4>
                                                    <v-card
                                                        v-if="canUpdateTicket"
                                                        tile
                                                        flat
                                                        hover
                                                        min-height="245"
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
                                                                    'delivery_tickets.form.add-document',
                                                                )
                                                            }}
                                                        </v-card-actions>
                                                    </v-card>
                                                </v-flex>
                                            </v-layout>
                                        </v-container>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-card-text>
                        <v-divider />
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>
        <FileAttachmentFormMenu
            ref="fileAttachmentFormMenu"
            :allowed-types="$page.props.allowedTypes"
            @attach:from-file="(file) => attachFromMedia(file)"
            @attach:from-media="() => $refs.mediaDrawerRef.show()"
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

        <OrderProductForm
            ref="ticketMaterialForm"
            :title="$t('delivery_tickets.labels.add-materials-to-delivery')"
            :submit-btn-text="
                $t('delivery_tickets.buttons.add-to-delivery-ticket')
            "
            @refresh="() => router.reload({ only: ['materials'] })"
        />
    </div>
</template>
