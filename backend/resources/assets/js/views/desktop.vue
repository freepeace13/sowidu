<template>
  <v-layout row wrap>
    <DesktopItem
      v-can:allow-can="allowViewEmployees"
      :title="$t('labels.employees')"
      :to="{ name: 'employees' }"
    />

    <DesktopItem
      v-can="allowViewMedia"
      :title="$t('labels.media')"
      href="/apps/media/d"
    />

    <DesktopItem
      v-can="allowViewTasks"
      :title="$t('labels.tasks')"
      :to="{ name: 'tasks' }"
    />
        
    <DesktopItem
      v-can="allowViewContacts"
      :title="$t('labels.contacts')"
      :to="{ name: 'contacts' }"
    />

    <DesktopItem
      v-can:any="allowViewDeliveries"
      :title="$t('labels.deliveries')"
      :to="{ name: 'deliveries' }"
    />

    <DesktopItem
      v-can:any="allowViewOrders"
      :title="$t('labels.orders')"
      :to="{ name: 'orders' }"
    />

    <DesktopItem
      v-can="allowViewProducts"
      :title="$t('labels.products-and-equipments')"
      href="/apps/products"
    />
  </v-layout>
</template>

<script>
import DesktopItem from '@common/components/DesktopItem';
import * as EmployeeEnums from '@features/employee/enums';
import * as DeliveryEnums from '@features/delivery/enums';
import * as OrderEnums from '@features/ordering/enums';
import * as MediaEnums from '@features/media/enums';
import * as ContactEnums from '@features/contact/enums';
import * as ProductEnums from '@features/product/enums';
import * as TaskEnums from '@features/task/enums';

export default {
    components: {
        DesktopItem,
    },

    computed: {
        allowViewEmployees() {
            return [
                EmployeeEnums.APP,
                EmployeeEnums.PERMISSIONS.VIEW_EMPLOYEES,
            ];
        },

        allowViewDeliveries() {
            return [
                DeliveryEnums.PERMISSIONS.VIEW_INCOMING_DELIVERIES,
                DeliveryEnums.PERMISSIONS.VIEW_OUTGOING_DELIVERIES,
            ];
        },

        allowViewOrders() {
            return [
                OrderEnums.PERMISSIONS.VIEW_INCOMING_ORDERS,
                OrderEnums.PERMISSIONS.VIEW_OUTGOING_ORDERS,
            ];
        },

        allowViewMedia() {
            return MediaEnums.PERMISSIONS.VIEW_MEDIA;
        },

        allowViewContacts() {
            return ContactEnums.PERMISSIONS.VIEW_CONTACTS;
        },

        allowViewProducts() {
            return ProductEnums.PERMISSIONS.VIEW_PRODUCTS;
        },

        allowViewTasks() {
            return TaskEnums.PERMISSIONS.VIEW_TASKS;
        },
    },
};
</script>
