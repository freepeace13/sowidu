<template>
    <section id="order-summary">
        <v-sheet light width="85%" class="mx-auto">
            <v-container>
                <v-layout row wrap>
                    <v-flex xs5>
                        <section v-if="order.type === 'outgoing'">
                            <h4 class="font-weight-bold mb-2">CONTRACTOR</h4>
                            <AddressbookSelector
                                :readonly="!orderCopy.policies.isModifiable()"
                                v-model="summary.contractor"
                                label="Choose Contractor"
                                :errors="errors.get('contractor', [])"
                            />

                            <ContractorInformation
                                :contractor="summary.contractor"
                                @clear="summary.contractor = null"
                            />
                        </section>

                        <section v-if="order.type === 'incoming'">
                            <h4 class="font-weight-bold mb-2">CUSTOMER</h4>
                            <CustomerSelector
                                v-model="summary.customer"
                                :errors="errors.get('customer_id', [])"
                            />

                            <CustomerInformation
                                :customer="summary.customer"
                                @clear="summary.customer = null"
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
                            label="Order Number"
                            :value="order.orderNumber"
                            readonly
                        />

                        <DatePicker
                            v-model="summary.orderDate"
                            label="Order Date"
                            :errors="errors.get('order_date', [])"
                            :readonly="!orderCopy.policies.isModifiable()"
                        />

                        <DatePicker
                            v-model="summary.deliveryDate"
                            label="Delivery Date"
                            :errors="errors.get('delivery_date', [])"
                            :readonly="!orderCopy.policies.isModifiable()"
                        />
                    </v-flex>
                </v-layout>

                <TextAreaField
                    label="Remarks"
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
import { Customer, resolveFromRaw, Order } from '~/services/models';
import { MessageBag } from '~/support/wrappers';
import CustomerInformation from '~/components/UI/Panels/CustomerInformation';
import ContractorInformation from '~/components/UI/Panels/ContractorInformation';
import PurchasingItemsGrid from '~/components/UI/Grids/PurchasingItemsGrid';
import { PurchasesItems, UsesAuthStore } from '~/components/Mixins';
import PageMixin from '../mixins/PageMixin';

export default {
    name: 'EditOrdersSummary',

    mixins: [
        PageMixin,
        UsesAuthStore(),
        PurchasesItems('summary'),
    ],

    components: {
        CustomerInformation,
        ContractorInformation,
        PurchasingItemsGrid
    },

    props: {
        errors: {
            type: MessageBag,
            default: new MessageBag
        }
    },

    computed: {
        customerAvatar() {
            return this.summary.customer
                && this.summary.customer.avatar
                && this.summary.customer.avatar.url
                || this.shared('defaults.avatars.unset');
        }
    },

    created() {
        const eventName = 'update-summary';

        this.summary = Object.defineProperties({}, {
            description: {
                get: () => this.order.description,
                set: (value) => this.$emit(eventName, { description: value })
            },
            customer: {
                get: () => Customer.create(this.order.customer),
                set: (value) => this.$emit(eventName, { customer: value })
            },
            contractor: {
                get: () => this.order.contractor && resolveFromRaw(this.order.contractor),
                set: (value) => this.$emit(eventName, { contractor: value })
            },
            items: {
                get: () => this.order.items,
                set: (value) => this.$emit(eventName, { items: value })
            },
            orderDate: {
                get: () => this.order.formattedDates.orderDate,
                set: (value) => this.$emit(eventName, {
                    formattedDates: { orderDate: value }
                })
            },
            deliveryDate: {
                get: () => this.order.formattedDates.deliveryDate,
                set: (value) => this.$emit(eventName, {
                    formattedDates: { deliveryDate: value }
                })
            },
        });
    }
}
</script>