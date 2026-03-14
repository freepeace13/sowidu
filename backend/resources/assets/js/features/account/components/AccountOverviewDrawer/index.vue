<template>
    <v-navigation-drawer
        :value="open"
        :mini-variant="variant"
        class="grey darken-4"
        fixed
        app
    >
        <v-layout fill-height column justify-space-around>
            <template v-if="account">
                <v-toolbar flat>
                    <v-list class="pa-0 transparent">
                        <v-list-tile avatar>
                            <v-list-tile-avatar>
                                <img :src="account.avatar.url" :lazy-src="account.avatar.url">
                            </v-list-tile-avatar>

                            <v-list-tile-content>
                                <v-list-tile-title>{{ account.name }}</v-list-tile-title>
                            </v-list-tile-content>
                        </v-list-tile>
                    </v-list>
                </v-toolbar>

                <v-divider></v-divider>
            </template>
            <v-flex>
            <v-list dense two-line class="py-0 transparent">
                <NavigatorItem
                    :title="$t('labels.desktop')"
                    icon="home"
                    :to="{ name: 'desktop' }"
                />

                <NavigatorItem
                    :title="$t('labels.employees')"
                    icon="supervisor_account"
                    :to="{ name: 'employees' }"
                    v-can:allow="allowViewEmployees"
                />

                <NavigatorItem
                    :title="$t('labels.orders')"
                    icon="reorder"
                    :to="{ name: 'orders' }"
                    v-can:any="allowViewOrders"
                />

                <NavigatorItem
                    :title="$t('labels.deliveries')"
                    icon="directions_bus"
                    :to="{ name: 'deliveries' }"
                    v-can:any="allowViewDeliveries"
                />

                <NavigatorItem
                    :title="$t('tasks')"
                    icon="done"
                    :to="{ name: 'tasks' }"
                    v-can="allowViewTasks"
                />

                <NavigatorItem
                    :title="$t('labels.contacts')"
                    icon="library_books"
                    :to="{ name: 'contacts' }"
                    v-can="allowViewContacts"
                />

                <NavigatorItem
                    :title="$t('labels.media')"
                    icon="image"
                    :to="{ name: 'media' }"
                    v-can="allowViewMedia"
                />

                <NavigatorItem
                    :title="$t('labels.products-and-equipments')"
                    icon="event_note"
                    href="/apps/products"
                    v-can="allowViewProducts"
                />
            </v-list>
            </v-flex>
            <v-flex shrink>
                <v-divider></v-divider>
                <v-btn flat block class="ma-0 pa-0" @click="variant = !variant;">
                    <v-icon v-if="mini">chevron_right</v-icon>
                    <template v-else>
                        <v-icon left>chevron_left</v-icon> Collapse
                    </template>
                </v-btn>
            </v-flex>
        </v-layout>
    </v-navigation-drawer>
</template>

<script>
import { User, Company } from '~/services/models';
import NavigatorItem from '@common/components/NavigatorItem';
import * as EmployeeEnums from '@features/employee/enums';
import * as DeliveryEnums from '@features/delivery/enums';
import * as OrderEnums from '@features/ordering/enums';
import * as MediaEnums from '@features/media/enums';
import * as ContactEnums from '@features/contact/enums';
import * as ProductEnums from '@features/product/enums';
import * as TaskEnums from '@features/task/enums';

export default {
    name: 'AccountOverviewDrawer',

    components: {
        NavigatorItem
    },

    props: {
        account: {
            type: [User, Company],
            default: null
        },

        open: {
            type: Boolean,
            default: true
        },

        mini: {
            type: Boolean,
            default: true
        }
    },

    data() {
        return {
            variant: this.mini
        }
    },

    computed: {
        allowViewEmployees() {
            return EmployeeEnums.APP;
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

    watch: {
        variant(value) {
            this.$emit('update:mini', value);
        }
    }
}
</script>