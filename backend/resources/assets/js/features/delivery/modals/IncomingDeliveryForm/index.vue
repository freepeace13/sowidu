<template>
    <ModalLayout :title="$attrs.modal.options.title" :id="$attrs.modal.id">
        <v-subheader class="px-4">{{ $t('labels.customer') }}</v-subheader>

        <v-container grid-list-lg fluid class="py-0">
            <CustomerSelector
                v-model="delivery.customer"
                :label="$t('labels.inputs.biller')"
                :errors="$deliveries.$errors.get('customer_id')"
            />

            <CustomerInformation :customer="delivery.customer" />
        </v-container>

        <v-divider></v-divider>
        <v-subheader class="px-4">{{ $t('labels.general') }}</v-subheader>

        <v-container grid-list-lg fluid class="py-0">
            <TextField
                :label="$t('labels.inputs.title')"
                v-model="delivery.title"
                :errors="$deliveries.$errors.get('title')"
            />

            <v-layout row>
                <v-flex xs6>
                    <DatePicker
                        v-model="delivery.deliveryDate"
                        :label="$t('labels.delivery-date')"
                        :errors="$deliveries.$errors.get('schedule')"
                    />
                </v-flex>
                <v-spacer></v-spacer>
                <v-flex xs6>
                    <TimePicker
                        v-model="delivery.deliveryTime"
                        :label="$t('labels.delivery-time')"
                        :errors="$deliveries.$errors.get('schedule')"
                    />
                </v-flex>
            </v-layout>

            <TextAreaField :label="$t('labels.inputs.remarks')" v-model="delivery.remarks" />
        </v-container>

        <v-divider></v-divider>
        <v-subheader class="px-4">{{ $t('headings.purchased-items') }}</v-subheader>

        <v-container grid-list-lg fluid class="py-0">
            <PurchasingItemsGrid
                :purchased-items="delivery.items"
                :errors="$deliveries.$errors.get('items')"
                @create-slot="$purchase.createSlot"
                @delete-slot="$purchase.removeItem"
                @update-item="$purchase.selectItem"
                @update-quantity="$purchase.changeQty"
            />
        </v-container>

        <v-divider></v-divider>
        <v-subheader class="px-4">{{ $t('labels.inputs.members') }}</v-subheader>

        <v-container grid-list-lg fluid class="pt-0">
            <EmployeeSelector
                v-model="delivery.members"
                multiple
                :errors="$deliveries.$errors.get('members')"
            />
        </v-container>

        <FormsMediaList :media="delivery.media" />
        <FormsTasksList :tasks="delivery.tasks" @remove="removeTask" />
        <FormsOrdersList :orders="delivery.orders" @remove="removeOrder" />

        <template v-slot:actions>
            <BrowsersMenu
                :except="['deliveries']"
                @browse-orders="browseOrders"
                @browse-media="browseMedia"
                @browse-tasks="browseTasks"
            />

            <v-spacer></v-spacer>

            <v-btn color="primary" :loading="$deliveries.$loading" @click="save" block>
                {{ delivery.exists() ? $t('buttons.save-changes') : $t('buttons.create') }}
            </v-btn>

            <v-btn color="grey darken-3" block @click="$modal.close($vnode.key)">
                {{ $t('buttons.cancel') }}
            </v-btn>
        </template>
    </ModalLayout>
</template>

<script>
import { Response } from '~/services/events/modal';
import { DELIVERY_TYPES } from '~/support/constants';
import DeliveryService from '~/services/DeliveryService';
import { Delivery, Item } from '~/services/models';
import { isFunction, isNullOrUndefined } from '~/support/helpers';
import PurchasesItems from '@features/product/mixins/PurchasesItems';
import UsesDeliveryStore from '../../mixins/UsesDeliveryStore';
import { UsesAuthStore } from '~/components/Mixins';
import CustomerInformation from '@common/components/CustomerInformation';
import PurchasingItemsGrid from '@features/product/components/PurchasingItemsGrid';
import BrowsersMenu from '@common/components/BrowseButton';
import TogglesMedia from '@features/media/mixins/TogglesMedia';
import TogglesOrders from '@features/ordering/mixins/TogglesOrders';
import TogglesTasks from '@features/task/mixins/TogglesTasks';
import FormsTasksList from '@features/task/components/TasksAttachForm';
import FormsOrdersList from '@features/ordering/components/OrdersAttachForm';
import FormsMediaList from '@features/media/components/MediaAttachForm';

export default {
    name: 'DeliveryIncomingFormModal',

    mixins: [
        UsesAuthStore(),
        UsesDeliveryStore(),
        TogglesTasks('delivery'),
        TogglesMedia('delivery'),
        TogglesOrders('delivery'),
        PurchasesItems('delivery'),
    ],

    components: {
        BrowsersMenu,
        FormsTasksList,
        FormsMediaList,
        FormsOrdersList,
        PurchasingItemsGrid,
        CustomerInformation,
    },

    props: {
        deliveryId: {
            validator(prop) {
                return prop === undefined || typeof prop === 'number';
            }
        },

        onSuccess: {
            default: null,
            validator(prop) {
                return isNullOrUndefined(prop) || isFunction(prop);
           }
        },
    },

    data: () => ({
        delivery: Delivery.create({
            type: 'incoming',
            title: null,
            customer: null,
            deliveryTime: null,
            deliveryDate: null,
            members: [],
            items: [],
            orders: [],
            tasks: []
        }),
    }),

    methods: {
        async save() {
            if (this.delivery.exists()) {
                this.delivery = await this.$deliveries.update(this.delivery);
            } else {
                this.delivery = await this.$deliveries.create(this.delivery);
            }

            if (isFunction(this.onSuccess)) {
                this.onSuccess(new Response(this, this.delivery));
            } else {
                this.$modal.close(this.$vnode.key);
            }
        },
    },

    async created() {
        if (this.deliveryId !== undefined) {
            this.delivery = await DeliveryService.retrieve(this.deliveryId);
        }
    }
}
</script>
