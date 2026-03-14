<template>
    <v-card>
        <v-img
            :src="contractor.photo"
            height="200px"
            contain
        />
        <v-list>
            <v-list-tile>
                <v-list-tile-action>
                    <v-icon
                        v-tooltip.top="$t('order.labels.contractor')"
                        color="light-blue darken-2"
                    >
                        business
                    </v-icon>
                </v-list-tile-action>

                <v-list-tile-content>
                    <v-list-tile-title>
                        {{ contractor.name }}
                    </v-list-tile-title>
                    <v-list-tile-sub-title>
                        Contractor name
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
                        v-tooltip.top="$t('labels.inputs.institution-type')"
                        color="light-blue darken-2"
                    >
                        storefront
                    </v-icon>
                </v-list-tile-action>

                <v-list-tile-content>
                    <v-list-tile-title>
                        {{ contractor.institution_type.type }}
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
                        color="light-blue darken-2"
                    >
                        copyright
                    </v-icon>
                </v-list-tile-action>

                <v-list-tile-content>
                    <v-list-tile-title>
                        {{ contractor.legal_form.legal_form }}
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
                        color="light-blue darken-2"
                    >
                        location_on
                    </v-icon>
                </v-list-tile-action>

                <v-list-tile-content>
                    <v-list-tile-title class="tw-text-sm">
                        {{ contractor.address | distinctAddress }}
                    </v-list-tile-title>
                    <v-list-tile-sub-title>
                        {{ contractor.address | baseAddress }}
                    </v-list-tile-sub-title>
                </v-list-tile-content>
            </v-list-tile>
        </v-list>
    </v-card>
</template>
<script>
import { getBaseAddress, getDistinctAddress } from '@/Composables/useAddress'

export default {
    filters: {
        baseAddress: (value) => getBaseAddress(value),
        distinctAddress: (value) => getDistinctAddress(value),
    },

    props: {
        contractor: {
            type: Object,
            default: () => ({}),
        },
    },
}
</script>
