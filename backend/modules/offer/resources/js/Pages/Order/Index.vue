<script setup>
import { authCan } from '@/Composables/useAuth'
import { getPageProps } from '@/Composables/useUtils'
import JumboUploadButton from '@/Pages/Order/Files/Components/JumboUploadButton.vue'
import OfferForm from '@Offer/Components/OfferForm.vue'
import { computed, ref } from 'vue'

defineProps({
    order: {
        required: true,
        type: Object,
    },

    offers: {
        required: true,
        type: Array,
    },
})

const isPrivateUser = computed(() => getPageProps('user.isPrivateUser'))
const showOfferRouteName = computed(() =>
    isPrivateUser.value ? 'my-offers.show' : 'offers.show',
)
const offerFormRef = ref(null)
</script>
<template>
    <div class="fill-height tw-w-full">
        <portal
            to="toolbar"
            tag="span"
        >
            <v-toolbar
                absolute
                top
                flat
                color="white"
            >
                <v-btn
                    v-tooltip.top="'Go to order details'"
                    icon
                    class="hidden-xs-only"
                    @click="$inertia.get($route('orders.show', { order }))"
                >
                    <v-icon>arrow_back</v-icon>
                </v-btn>
                <v-toolbar-title>
                    {{ $t('order.labels.order-offers') }}
                </v-toolbar-title>

                <v-spacer />

                <v-btn
                    v-if="authCan('can_create_offer')"
                    color="info"
                    @click="offerFormRef.show(null, 'outgoing')"
                >
                    {{ $t('order.buttons.add-offer') }}
                </v-btn>
            </v-toolbar>
        </portal>

        <v-container
            v-if="!offers.length && authCan('can_create_offer')"
            grid-list-xs
            fill-height
        >
            <v-layout
                align-center
                justify-center
                fill-height
            >
                <v-flex xs12>
                    <JumboUploadButton
                        :title="$t('offer.buttons.create')"
                        icon="local_offer"
                    />
                    <!-- @click:card="() => invoiceFormRef.show()" -->
                </v-flex>
            </v-layout>
        </v-container>

        <v-container
            grid-list-lg
            text-xs-center
            px-0
            class="!tw-max-w-full"
        >
            <v-layout
                row
                wrap
            >
                <v-flex xs12>
                    <v-alert
                        :value="!offers.length"
                        color="info"
                        icon="info"
                        outline
                    >
                        {{ $t('offer.messages.no-offers') }}
                    </v-alert>
                </v-flex>
                <v-flex
                    v-for="offer in offers"
                    :key="offer.id"
                    xs12
                    sm4
                >
                    <v-card tile>
                        <v-card-title
                            class="tw-flex tw-flex-col tw-text-left !tw-items-start"
                        >
                            <a
                                class="title font-weight-light tw-cursor-pointer hover:tw-underline"
                                :href="
                                    $route(showOfferRouteName, {
                                        offer,
                                    })
                                "
                                target="_blank"
                            >
                                {{ offer.external_id ?? offer.internal_id }}
                            </a>
                            <v-chip
                                :color="offer.status_metadata.color"
                                dark
                                label
                                small
                                class=""
                            >
                                {{ offer?.status_metadata.label }}
                            </v-chip>
                        </v-card-title>
                        <v-card-text>
                            <v-icon
                                size="60"
                                :color="offer.status_metadata.color"
                            >
                                card_giftcard
                            </v-icon>
                        </v-card-text>
                        <v-divider />
                        <v-card-actions class="tw-justify-between">
                            <div class="tw-flex tw-gap-x-2">
                                <div class="tw-font-semibold tw-capitalize">
                                    {{ $t('offer.items.inputs.total') }}:
                                </div>
                                <div>
                                    {{ offer.grand_total_formatted }}
                                </div>
                            </div>
                            <v-btn
                                :color="offer.status_metadata.color"
                                flat
                                :href="
                                    $route(showOfferRouteName, {
                                        offer,
                                    })
                                "
                            >
                                {{ $t('buttons.open') }}
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>

        <OfferForm
            v-if="authCan('can_create_offer')"
            ref="offerFormRef"
            :order="order"
            :disable-addressbook="true"
            :disable-order="true"
            @close="offerFormRef.close()"
        />
    </div>
</template>
