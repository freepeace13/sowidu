<template>
    <v-dialog
        v-model="isShow"
        persistent
        max-width="600px"
    >
        <v-card>
            <v-toolbar
                color="transparent"
                flat
            >
                <v-toolbar-title> Delivery address </v-toolbar-title>
                <v-spacer />
                <v-btn
                    icon
                    @click="close"
                >
                    <v-icon>close</v-icon>
                </v-btn>
            </v-toolbar>
            <v-divider />
            <v-card-text v-show="!isShowCreateAddressForm">
                <div class="">Select address:</div>
                <v-container
                    grid-list-lg
                    fluid
                    pa-2
                >
                    <v-layout column>
                        <v-flex>
                            <v-list two-line>
                                <div
                                    v-for="place in ownedPlaces"
                                    :key="`client-owned-place-${place.id}`"
                                >
                                    <v-list-tile
                                        :class="{
                                            'light-blue lighten-5':
                                                place.id === selectedAddress,
                                        }"
                                        @click.stop="selectAddress(place.id)"
                                    >
                                        <v-list-tile-action>
                                            <input
                                                :id="`place-radio-${place.id}`"
                                                v-model="selectedAddress"
                                                type="radio"
                                                :value="place.id"
                                            />
                                        </v-list-tile-action>

                                        <v-list-tile-content>
                                            <v-list-tile-title>
                                                {{ place.label }}
                                            </v-list-tile-title>
                                            <v-list-tile-sub-title>
                                                {{ place.full }}
                                            </v-list-tile-sub-title>
                                        </v-list-tile-content>
                                    </v-list-tile>
                                    <v-divider />
                                </div>
                            </v-list>
                        </v-flex>
                        <v-flex
                            xs12
                            class="tw-flex tw-justify-center"
                        >
                            <v-btn
                                color="secondary"
                                @click="isShowCreateAddressForm = true"
                            >
                                {{ $t('order.labels.add-new-address') }}
                            </v-btn>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-text v-show="isShowCreateAddressForm">
                <v-container
                    grid-list-lg
                    fluid
                    pa-0
                >
                    <v-layout
                        row
                        wrap
                    >
                        <v-flex xs12>
                            <DeliveryAddressForm @select="saveAndSelect" />
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <SubmitButton
                    v-show="!isShowCreateAddressForm"
                    :disabled="!selectedAddress"
                    @click="selectAndClose(selectedAddress)"
                >
                    {{ $t('buttons.done') }}
                </SubmitButton>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import SubmitButton from '@components/Forms/SubmitButton.vue'
import DeliveryAddressForm from '../Outgoing/Components/DeliveryAddressForm.vue'

export default {
    components: { SubmitButton, DeliveryAddressForm },
    props: {
        ownedPlaces: {
            type: Array,
            required: true,
        },
    },
    data: () => ({
        isShow: false,
        isShowCreateAddressForm: false,
        selectedAddress: null,
    }),

    methods: {
        show() {
            this.isShow = true
        },
        close() {
            this.isShow = false
            this.reset()
        },
        reset() {
            this.selectedAddress = null
        },
        selectAddress(id) {
            this.selectedAddress = id
        },
        saveAndSelect({ id }) {
            this.isShowCreateAddressForm = false
            this.selectAndClose(id)
        },
        selectAndClose(addressId) {
            this.$emit('select', addressId)
            this.close()
        },
    },
}
</script>
