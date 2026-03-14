<template>
    <v-list>
        <v-list-tile>
            <v-list-tile-action>
                <v-icon
                    v-tooltip.top="$t('order.labels.contractor')"
                    color="indigo"
                >
                    business
                </v-icon>
            </v-list-tile-action>

            <v-list-tile-content>
                <v-list-tile-title>
                    {{ client.name }}
                </v-list-tile-title>
                <v-list-tile-sub-title> Contractor name </v-list-tile-sub-title>
            </v-list-tile-content>
        </v-list-tile>

        <v-divider
            inset
            class="my-2"
        />

        <v-list-tile>
            <v-list-tile-action>
                <v-icon
                    v-tooltip.top="$t('labels.inputs.institution-type')"
                    color="indigo"
                >
                    storefront
                </v-icon>
            </v-list-tile-action>

            <v-list-tile-content>
                <v-list-tile-title>
                    {{
                        client?.institution_type?.type ??
                        client?.institution_type
                    }}
                </v-list-tile-title>
                <v-list-tile-sub-title>
                    {{ $t('labels.inputs.institution-type') }}
                </v-list-tile-sub-title>
            </v-list-tile-content>
        </v-list-tile>

        <v-divider
            inset
            class="my-2"
        />

        <v-list-tile>
            <v-list-tile-action>
                <v-icon
                    v-tooltip.top="$t('labels.inputs.legal-form')"
                    color="indigo"
                >
                    copyright
                </v-icon>
            </v-list-tile-action>

            <v-list-tile-content>
                <v-list-tile-title>
                    {{ client?.legal_form?.legal_form ?? client?.legalform }}
                </v-list-tile-title>
                <v-list-tile-sub-title>
                    {{ $t('labels.inputs.legal-form') }}
                </v-list-tile-sub-title>
            </v-list-tile-content>
        </v-list-tile>

        <v-divider
            inset
            class="my-2"
        />

        <v-list-tile>
            <v-list-tile-action>
                <v-icon
                    v-tooltip.top="'Contractor Address'"
                    color="indigo"
                >
                    local_shipping
                </v-icon>
            </v-list-tile-action>

            <v-list-tile-content>
                <v-list-tile-title class="tw-text-sm">
                    {{ deliveryAddress | distinctAddress }}
                </v-list-tile-title>
                <v-list-tile-sub-title>
                    {{ deliveryAddress | baseAddress }}
                </v-list-tile-sub-title>
            </v-list-tile-content>
            <v-list-tile-action>
                <v-btn
                    v-if="isShowEditDeliveryAddress"
                    v-tooltip="`${$t('order.labels.edit-delivery-address')}`"
                    icon
                    ripple
                    @click="$emit('click:edit-delivery-address')"
                >
                    <v-icon
                        color="info"
                        small
                    >
                        border_color
                    </v-icon>
                </v-btn>
            </v-list-tile-action>
        </v-list-tile>
    </v-list>
</template>

<script>
import { getBaseAddress, getDistinctAddress } from '@/Composables/useAddress'

export default {
    filters: {
        baseAddress: (value) => getBaseAddress(value),
        distinctAddress: (value) => getDistinctAddress(value),
    },

    props: {
        client: {
            type: Object,
            required: false,
            default: () => ({}),
        },
        deliveryAddress: {
            required: true,
            type: Object,
        },
        isShowEditDeliveryAddress: {
            type: Boolean,
            required: false,
            default: false,
        },
    },
}
</script>
