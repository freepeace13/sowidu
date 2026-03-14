<script setup>
import axios from 'axios'
import { ref, getCurrentInstance } from 'vue'
import TicketDetail from './TicketDetail.vue'
import { useDateFormat } from '@/Composables/useDayJs'
import { computed } from 'vue'
import DeliveryTicketMaterialsRow from './DeliveryTicketMaterialsRow.vue'
import FileAttachmentFormMenu from '@/Components/Fields/File/FileAttachmentFormMenu.vue'
import MediaDrawer from '@/Components/Media/MediaDrawer.vue'

const isLoading = ref(true)
const isShow = ref(false)
const ticket = ref({})
const app = getCurrentInstance()

defineExpose({ show })

const deliverer = computed(() => ticket.value?.deliverer)
const order = computed(() => ticket.value?.order)

async function show(id) {
    try {
        isLoading.value = true
        isShow.value = true

        // Fetch full details
        const { data } = await axios.get(
            window.route('json.delivery_tickets.show', {
                deliveryTicket: id,
                full: true,
            }),
        )

        ticket.value = data
    } catch (error) {
        console.error(error)
    } finally {
        isLoading.value = false
    }
}

function close() {
    ticket.value = null
    isLoading.value = true
    isShow.value = false
}

async function attachFromMedia({ uuid }) {
    await axios.post(
        window.route('delivery_tickets.documents.store', {
            deliveryTicket: ticket.value,
        }),
        {
            medias: [uuid],
        },
    )

    show(ticket.value.id)
}

async function removeDocument(document) {
    const deliveryTicket = ticket.value?.id

    app.proxy.$root.$confirm.ask({
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
                        show(deliveryTicket)
                    },
                },
            )
        },
    })
}
</script>
<template>
    <v-dialog
        v-model="isShow"
        persistent
        width="800px"
        lazy
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title>
                    {{ $tc('headings.delivery_tickets') }}
                    {{ $t('labels.details') }}
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
            <v-card-text class="pt-0">
                <v-container
                    grid-list-md
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
                                :label="$t('delivery_tickets.form.ticket-no')"
                                :value="ticket?.internal_id"
                            />
                        </v-flex>
                        <v-flex xs6>
                            <TicketDetail
                                :label="$t('delivery_tickets.form.external_id')"
                                :value="ticket?.external_id"
                            />
                        </v-flex>
                        <v-flex xs6>
                            <TicketDetail
                                :label="
                                    $t('delivery_tickets.form.delivery_date')
                                "
                                :value="useDateFormat(ticket?.delivery_date)"
                            />
                        </v-flex>
                        <v-flex xs6>
                            <TicketDetail
                                :label="$t('labels.date-created')"
                                :value="useDateFormat(ticket?.created_at)"
                            />
                        </v-flex>
                        <v-flex xs6>
                            <TicketDetail
                                :label="$t('delivery_tickets.form.ticket-type')"
                                :value="ticket?.type?.name"
                            />
                        </v-flex>
                        <v-flex xs12>
                            <TicketDetail
                                :label="
                                    $t('delivery_tickets.form.delivery-address')
                                "
                                :value="ticket?.delivery_address?.full"
                            />
                        </v-flex>

                        <v-flex
                            xs12
                            mt-4
                        >
                            <div class="tw-text-lg tw-font-semibold">
                                {{ $t('delivery_tickets.form.deliverer') }}
                                {{ $t('labels.details') }}:
                            </div>
                        </v-flex>
                        <v-flex xs3>
                            <v-img
                                :src="deliverer?.column_values.photo"
                                contain
                                class="grey lighten-2 mr-2"
                                height="150"
                            />
                        </v-flex>
                        <v-flex xs9>
                            <div>
                                <TicketDetail
                                    :label="$t('labels.inputs.name')"
                                    :value="deliverer?.column_values.name"
                                />
                                <TicketDetail
                                    :label="$t('labels.inputs.email')"
                                    class="tw-italic tw-font-light"
                                    :value="deliverer?.column_values.email"
                                />
                                <TicketDetail
                                    label="Phone"
                                    :value="
                                        deliverer?.column_values?.phone ?? '--'
                                    "
                                />
                                <TicketDetail
                                    :label="
                                        $t('delivery_tickets.labels.address')
                                    "
                                    :value="
                                        deliverer?.column_values?.address
                                            ?.full ?? '--'
                                    "
                                />
                            </div>
                        </v-flex>
                        <v-flex
                            xs12
                            mt-4
                        >
                            <div class="tw-text-lg tw-font-semibold">
                                {{ $t('delivery_tickets.form.order') }}
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
                                    :label="$t('labels.date-created')"
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
                                        :label="$t('labels.inputs.description')"
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
                            <div class="tw-text-lg tw-font-semibold">
                                {{ $t('delivery_tickets.labels.materials') }}:
                            </div>

                            <v-btn
                                color="info"
                                small
                                @click="$emit('click:add-materials', ticket)"
                            >
                                {{
                                    $t('delivery_tickets.buttons.add-materials')
                                }}
                            </v-btn>
                        </v-flex>
                        <v-flex xs12>
                            <DeliveryTicketMaterialsRow
                                v-if="ticket?.id"
                                :delivery-ticket="ticket"
                                :hide-label="true"
                                card-text-class="px-0"
                                @click:add-materials="
                                    (ticket) =>
                                        $emit('click:add-materials', ticket)
                                "
                            />
                        </v-flex>
                        <v-flex
                            xs12
                            mt-4
                        >
                            <div class="tw-text-lg tw-font-semibold">
                                {{ $t('delivery_tickets.labels.documents') }}:
                            </div>
                        </v-flex>
                        <v-flex xs12>
                            <v-container
                                v-if="ticket?.documents?.length"
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
                                        ) in ticket?.documents ?? []"
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
                                                    `${$t('buttons.remove')}`
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
            <v-card-actions class="px-4 py-3">
                <v-spacer />
                <v-btn
                    outline
                    depressed
                    @click="close"
                >
                    {{ $t('buttons.cancel') }}
                </v-btn>
            </v-card-actions>
        </v-card>
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
    </v-dialog>
</template>
