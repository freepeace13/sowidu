<script>
import { useDateFormat } from '@/Composables/useDayJs'
import SubmitButton from '@components/Forms/SubmitButton.vue'
import AddressbookAutocomplete from '@/Apps/Shared/Components/AutoComplete/AddressbookAutocomplete.vue'
import OrderAutocomplete from '@/Apps/Shared/Components/AutoComplete/OrderAutocomplete.vue'
import AddressAutocomplete from '../../../Components/AddressAutocomplete.vue'
import axios from 'axios'
import FileAttachmentFormMenu from '@/Components/Fields/File/FileAttachmentFormMenu.vue'
import MediaDrawer from '@/Components/Media/MediaDrawer.vue'
import JumboUploadButton from '@/Pages/Order/Files/Components/JumboUploadButton.vue'
import { isNotNull } from '@/Composables/useUtils'

export default {
    components: {
        SubmitButton,
        AddressbookAutocomplete,
        OrderAutocomplete,
        AddressAutocomplete,
        FileAttachmentFormMenu,
        MediaDrawer,
        JumboUploadButton,
    },

    props: {
        order: {
            required: false,
            type: Object,
            default: null,
        },
    },

    data: (vm) => ({
        form: vm.$inertia.form({
            deliverer: null,
            order: null,
            delivery_address: null,
            delivery_date: null,
            medias: [],
            external_id: null,
            type: null,
        }),
        isShow: false,
        deliveryTicket: null,
        files: [],
    }),

    computed: {
        isCreating() {
            return !this.deliveryTicket
        },

        title() {
            return this.isCreating
                ? this.$t('delivery_tickets.form.create-delivery-ticket')
                : this.$t('delivery_tickets.form.update-delivery-ticket')
        },

        hasOrderProps() {
            return isNotNull(this.order?.id)
        },
    },

    methods: {
        attachFromMedia(media) {
            this.form.medias.push(media.uuid)
            this.files.push(media)
        },

        openMediaDrawer() {
            this.$refs.mediaDrawerRef.show()
        },

        show(deliveryTicket = null) {
            this.form.reset()

            if (deliveryTicket) {
                this.setDeliveryTicket(deliveryTicket)
            }

            // Check if has props `order`
            if (this.hasOrderProps) {
                const order = this.order
                this.form.order = order
                this.orderSelected(order)
            }

            this.isShow = true
        },

        async setDeliveryTicket(deliveryTicket) {
            const { order, delivery_address } = await this.fetchDeliveryTickets(
                deliveryTicket,
            )

            this.deliveryTicket = deliveryTicket
            this.form.delivery_date = useDateFormat(
                deliveryTicket.delivery_date,
                'YYYY-MM-DD',
            )

            // this.form.deliverer = deliverer
            this.form.order = order
            this.form.delivery_address = delivery_address
            this.form.external_id = deliveryTicket?.external_id
            this.form.type = deliveryTicket.type.value
        },

        async fetchDeliveryTickets(deliveryTicket) {
            try {
                this.form.processing = true
                const { data } = await axios.get(
                    this.$route('json.delivery_tickets.show', {
                        deliveryTicket,
                    }),
                )
                return data
            } catch (error) {
                console.error(error)
            } finally {
                this.form.processing = false
            }
        },

        close() {
            this.isShow = false
            this.reset()
        },

        reset() {
            this.form.reset()
            this.form.clearErrors()
            this.deliveryTicket = null
            this.files = []
        },

        submit() {
            const deliveryTicket = this.deliveryTicket

            const method = this.isCreating ? 'post' : 'patch'
            let route = this.$route('delivery_tickets.store')

            if (!this.isCreating)
                route = this.$route('delivery_tickets.update', {
                    deliveryTicket,
                })

            this.form.transform((data) => ({
                ...data,
                deliverer: { id: data.deliverer?.id },
                order: { id: data.order?.id },
                delivery_address: { id: data.delivery_address?.id },
            }))

            this.form[method](route, {
                preserveState: true,
                preserveScroll: true,
                only: ['errors', 'flash'],
                onSuccess: () => {
                    this.$emit('refresh')
                    this.close()
                },
                onError: (errors) =>
                    this.$root.$emit('flash.validation', errors),
            })
        },

        async orderSelected(order) {
            // if (this.form.deliverer) return

            // const { data } = await axios.get(
            //     this.$route('json.autocomplete.addressbook', {
            //         id: order?.client_addressbook_id,
            //     }),
            // )
            // const addressbook = [...data].shift()
            // this.form.deliverer = addressbook
            this.form.delivery_address = order?.delivery_address
            this.form.type = order?.type
        },
    },
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
                                :label="$t('delivery_tickets.form.order')"
                                :placeholder="
                                    $t('delivery_tickets.form.search-order')
                                "
                                :error-messages="form.errors.order"
                                :hide-details="!form.errors.order"
                                @input="(order) => orderSelected(order)"
                            />
                        </v-flex>
                        <v-flex xs12>
                            <AddressbookAutocomplete
                                v-model="form.deliverer"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :label="$t('delivery_tickets.form.deliverer')"
                                :error-messages="form.errors.deliverer"
                                :hide-details="!form.errors.deliverer"
                            />
                        </v-flex>
                        <v-flex xs12>
                            <AddressAutocomplete
                                v-model="form.delivery_address"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :label="
                                    $t('delivery_tickets.form.delivery-address')
                                "
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
                                            $t(
                                                'delivery_tickets.form.delivery_date',
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
                                    v-model="form.delivery_date"
                                    :loading="form.processing"
                                    :disabled="form.processing"
                                    scrollable
                                    reactive
                                    picker-date
                                />
                            </v-menu>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field
                                v-model="form.external_id"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.external_id"
                                :hide-details="!form.errors.external_id"
                                :label="$t('delivery_tickets.form.external_id')"
                                required
                                color="primary"
                                class="required-input"
                                outline
                            />
                        </v-flex>
                        <v-flex xs12>
                            <v-select
                                v-model="form.type"
                                outline
                                full-width
                                :items="$page.props.deliveryTicketTypes"
                                :loading="form.processing"
                                :disabled="form.processing"
                                :error-messages="form.errors.type"
                                :hide-details="!form.errors.type"
                                :label="$t('delivery_tickets.form.ticket-type')"
                                class="required-input"
                                required
                            />
                        </v-flex>
                        <v-flex
                            xs12
                            mt-1
                        >
                            <v-label>
                                {{ $t('delivery_tickets.labels.documents') }}:
                            </v-label>
                            <v-container
                                v-if="files.length"
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
                                        v-for="(file, fileIndex) in files"
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
                                                        'delivery_tickets.form.add-document',
                                                    )
                                                }}
                                            </v-card-actions>
                                        </v-card>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-flex>
                        <v-flex
                            v-show="!files.length"
                            xs12
                        >
                            <JumboUploadButton
                                v-show="!form.media"
                                :title="
                                    $t('delivery_tickets.form.add-document')
                                "
                                :container-size="{ xs12: true }"
                                @click:card="
                                    (e) => $refs.fileAttachmentFormMenu.show(e)
                                "
                            >
                                <div class="v-messages error--text">
                                    {{ form.errors.media }}
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
