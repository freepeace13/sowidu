<script setup>
import axios from 'axios'
import { ref, getCurrentInstance, toRef } from 'vue'
import TicketDetail from './Components/TicketDetail.vue'
import { useDateFormat } from '@/Composables/useDayJs'
import { computed } from 'vue'
import DeliveryTicketMaterialsRow from './Components/DeliveryTicketMaterialsRow.vue'
import FileAttachmentFormMenu from '@/Components/Fields/File/FileAttachmentFormMenu.vue'
import MediaDrawer from '@/Components/Media/MediaDrawer.vue'
import { router } from '@inertiajs/vue2'
import useGlobalVariables from '@/Composables/useGlobalVariables'
import OrderProductForm from '../Order/Products/Components/OrderProductForm.vue'

const props = defineProps({
    deliveryTicket: {
        required: true,
        type: Object,
    },
    documents: {
        required: true,
        type: Array,
    },
    materials: {
        required: true,
        type: Array,
    },
    allowedTypes: {
        required: true,
        type: Array,
    },
    totals: {
        required: true,
        type: Object,
    },
})

const { $confirm } = useGlobalVariables()
const app = getCurrentInstance()
const ticket = toRef(props, 'deliveryTicket')
const mediaDrawerRef = ref(null)
const fileAttachmentFormMenu = ref(null)
const orderProductFormRef = ref(null)

const deliverer = computed(() => ticket.value?.deliverer)
const order = computed(() => ticket.value?.order)

async function attachFromMedia({ uuid }) {
    await axios.post(
        window.route('delivery_tickets.documents.store', {
            deliveryTicket: ticket.value,
        }),
        {
            medias: [uuid],
        },
    )

    router.reload({
        only: ['documents'],
    })
}

async function removeDocument(document) {
    const deliveryTicket = ticket.value?.id

    $confirm({
        title: app.proxy.$root.$t('labels.delete'),
        question: app.proxy.$root.$t(
            'delivery_tickets.message.confirm_document_removing',
        ),
        type: 'delete',
        confirm: () => {
            app.proxy.$inertia.delete(
                app.proxy.$route('delivery_tickets.documents.destroy', {
                    deliveryTicket,
                    document,
                }),
                {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: () => {
                        router.reload({
                            only: ['documents'],
                        })
                    },
                },
            )
        },
    })
}
</script>
<template>
    <div>
        <v-toolbar
            color="white"
            flat
        >
            <v-toolbar-title class="title tw-flex tw-items-center">
                <div class="md:tw-text-xl tw-text-lg">
                    <v-btn
                        icon
                        class="ml-0"
                        @click="$inertia.get($route('delivery_tickets.index'))"
                    >
                        <v-icon>arrow_back</v-icon>
                    </v-btn>
                    {{ $tc('headings.delivery_tickets') }}
                    {{ $t('labels.details') }}
                </div>
            </v-toolbar-title>
            <v-spacer />
        </v-toolbar>
        <v-divider />
        <v-container fluid>
            <v-layout
                row
                wrap
                fill-height
            >
                <v-flex
                    xs12
                    class="!tw-overflow-auto !tw-grow elevation-10"
                >
                    <v-card
                        flat
                        elevation="0"
                    >
                        <v-divider />
                        <v-card-text class="">
                            <v-container
                                grid-list-md
                                fluid
                                pa-2
                            >
                                <v-layout
                                    row
                                    wrap
                                >
                                    <v-flex xs7>
                                        <TicketDetail
                                            :label="
                                                $t(
                                                    'delivery_tickets.form.ticket-no',
                                                )
                                            "
                                            :value="ticket?.internal_id"
                                        />
                                    </v-flex>
                                    <v-flex xs5>
                                        <TicketDetail
                                            :label="
                                                $t(
                                                    'delivery_tickets.form.external_id',
                                                )
                                            "
                                            :value="ticket?.external_id"
                                        />
                                    </v-flex>
                                    <v-flex xs7>
                                        <TicketDetail
                                            :label="
                                                $t(
                                                    'delivery_tickets.form.delivery_date',
                                                )
                                            "
                                            :value="
                                                useDateFormat(
                                                    ticket?.delivery_date,
                                                )
                                            "
                                        />
                                    </v-flex>
                                    <v-flex xs5>
                                        <TicketDetail
                                            :label="$t('labels.date-created')"
                                            :value="
                                                useDateFormat(
                                                    ticket?.created_at,
                                                )
                                            "
                                        />
                                    </v-flex>
                                    <v-flex xs12>
                                        <TicketDetail
                                            :label="
                                                $t(
                                                    'delivery_tickets.form.ticket-type',
                                                )
                                            "
                                            :value="ticket?.type?.name"
                                        />
                                    </v-flex>
                                    <v-flex xs12>
                                        <TicketDetail
                                            :label="
                                                $t(
                                                    'delivery_tickets.form.delivery-address',
                                                )
                                            "
                                            :value="
                                                ticket?.delivery_address?.full
                                            "
                                        />
                                    </v-flex>

                                    <v-flex
                                        xs12
                                        mt-4
                                    >
                                        <div
                                            class="tw-text-lg tw-font-semibold"
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
                                        <v-img
                                            :src="
                                                deliverer?.column_values.photo
                                            "
                                            contain
                                            class="grey lighten-2 mr-2"
                                            height="150"
                                        />
                                    </v-flex>
                                    <v-flex xs9>
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
                                            class="tw-text-lg tw-font-semibold"
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
                                        class="tw-flex tw-justify-between"
                                    >
                                        <div
                                            class="tw-text-lg tw-font-semibold tw-content-end"
                                        >
                                            {{
                                                $t(
                                                    'delivery_tickets.labels.materials',
                                                )
                                            }}:
                                        </div>

                                        <v-btn
                                            color="info"
                                            @click="
                                                () =>
                                                    orderProductFormRef.show(
                                                        $route(
                                                            'delivery_tickets.materials.store',
                                                            { deliveryTicket },
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
                                            :delivery-ticket="deliveryTicket"
                                            :materials="materials"
                                            :totals="totals"
                                            @click:add-materials="
                                                () => orderProductFormRef.show()
                                            "
                                        />
                                    </v-flex>
                                    <v-flex
                                        xs12
                                        mt-4
                                    >
                                        <div
                                            class="tw-text-lg tw-font-semibold"
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
                                                    ) in documents ?? []"
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
                                                        tile
                                                        flat
                                                        hover
                                                        min-height="245"
                                                        height="100%"
                                                        class="tw-text-center card-border-dashed tw-cursor-pointer !tw-flex tw-justify-center tw-items-center tw-flex-col"
                                                        @click="
                                                            (e) =>
                                                                fileAttachmentFormMenu.show(
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
            :allowed-types="allowedTypes"
            @attach:from-file="(file) => attachFromMedia(file)"
            @attach:from-media="() => mediaDrawerRef.show()"
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
            ref="orderProductFormRef"
            :title="$t('delivery_tickets.labels.add-materials-to-delivery')"
            :submit-btn-text="
                $t('delivery_tickets.buttons.add-to-delivery-ticket')
            "
            @refresh="() => router.reload({ only: ['materials', 'totals'] })"
        />
    </div>
</template>
