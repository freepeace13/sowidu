<template>
    <section id="order-summary">
        <v-sheet light width="85%" class="mx-auto elevation-5">
            <v-container>
                <v-layout row wrap>
                    <v-flex xs5>
                        <section v-if="order.type === 'outgoing'">
                            <h4 class="font-weight-bold mb-2">
                                {{ $t('labels.contractor') }}
                            </h4>
                            <AddressbookSelector
                                :readonly="!orderCopy.policies.isModifiable()"
                                v-model="summary.contractor"
                                :label="$t('hints.choose-contractor')"
                                :errors="errors.get('contractor', [])"
                            />

                            <ContractorInformation
                                :contractor="summary.contractor"
                                @clear="summary.contractor = null"
                            />
                        </section>

                        <section v-if="order.type === 'incoming'">
                            <h4 class="font-weight-bold mb-2">{{ $t('labels.customer') }}</h4>

                            <CustomerCard
                                @change="setCustomer"
                                @click:edit="changeCustomer = true"
                                :customer="summary.customer"
                                :active-selector="changeCustomer || !summary.customer"
                            />
                        </section>
                    </v-flex>

                    <v-flex xs5 offset-xs2>
                        <v-card class="mb-4 mx-auto" width="50%" height="300px">
                            <v-img
                                :src="customerAvatar"
                                min-height="100%"
                                max-height="100%"
                            />
                        </v-card>

                        <TextField
                            :label="$t('labels.order-no')"
                            :value="order.orderNumber"
                            readonly
                        />

                        <DatePicker
                            v-model="summary.orderDate"
                            :label="$t('labels.order-date')"
                            :errors="errors.get('order_date', [])"
                            :readonly="!orderCopy.policies.isModifiable()"
                        />

                        <DatePicker
                            v-model="summary.deliveryDate"
                            :label="$t('labels.delivery-date')"
                            :errors="errors.get('delivery_date', [])"
                            :readonly="!orderCopy.policies.isModifiable()"
                        />
                    </v-flex>
                </v-layout>

                <TextAreaField
                    :label="$t('labels.inputs.remarks')"
                    v-model="summary.description"
                    :errors="errors.get('description', [])"
                    :readonly="!orderCopy.policies.isModifiable()"
                />

                <PurchasingItemsGrid
                    :readonly="!orderCopy.policies.isModifiable()"
                    :enable-create-item="order.type === 'incoming'"
                    :purchased-items="summary.items"
                    @create-slot="$purchase.createSlot"
                    @delete-slot="$purchase.removeItem"
                    @update-item="$purchase.selectItem"
                    @update-quantity="$purchase.changeQty"
                />
            </v-container>
        </v-sheet>
    </section>
</template>

<script>
import CustomerCard from '../../components/CustomerCard';
import CustomerSelector from '@common/components/CustomerSelector';
import { Customer, resolveFromRaw, Order } from '~/services/models';
import { MessageBag } from '~/support/wrappers';
import CustomerInformation from '@common/components/CustomerInformation';
import ContractorInformation from '@common/components/ContractorInformation';
import PurchasingItemsGrid from '@features/product/components/PurchasingItemsGrid';
import { UsesAuthStore } from '~/components/Mixins';
import PurchasesItems from '@features/product/mixins/PurchasesItems';
import ShowOrderStates from '../../mixins/ShowOrderStates';
import ShowOrderHandlers from '../../mixins/ShowOrderHandlers';

export default {
    name: 'ShowDetailsView',

    mixins: [
        ShowOrderStates(),
        ShowOrderHandlers(),
        UsesAuthStore(),
        PurchasesItems('summary'),
    ],

    data: () => ({
        changeCustomer: false
    }),

    components: {
        CustomerInformation,
        ContractorInformation,
        PurchasingItemsGrid,
        CustomerSelector,
        CustomerCard
    },

    computed: {
        customerAvatar() {
            return this.summary.customer
                && this.summary.customer.avatar
                && this.summary.customer.avatar.url
                || this.shared('defaults.avatars.unset');
        }
    },

    methods: {
        setCustomer(customer) {
            this.summary.customer = customer;
            this.changeCustomer = false;
        }
    },

    created() {
        this.summary = Object.defineProperties({}, {
            description: {
                get: () => this.order.description,
                set: (value) => this.handleUpdateSummary({ description: value })
            },
            customer: {
                get: () => this.order.customer && resolveFromRaw(this.order.customer),
                set: (value) => this.handleUpdateSummary({ customer: value })
            },
            contractor: {
                get: () => this.order.contractor && resolveFromRaw(this.order.contractor),
                set: (value) => this.handleUpdateSummary({ contractor: value })
            },
            items: {
                get: () => this.order.items,
                set: (value) => this.handleUpdateSummary({ items: value })
            },
            orderDate: {
                get: () => this.order.formattedDates.orderDate,
                set: (value) => this.handleUpdateSummary({
                    formattedDates: { orderDate: value }
                })
            },
            deliveryDate: {
                get: () => this.order.formattedDates.deliveryDate,
                set: (value) => this.handleUpdateSummary({
                    formattedDates: { deliveryDate: value }
                })
            },
        });
    }
}
</script>