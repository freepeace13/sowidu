<template>
    <v-container
        fill-height
        fluid
        pt-0
    >
        <OrderSidebar
            :pages="pages"
            :order-id="order?.id"
            :order-files-links="orderFilesLinks"
            :badges="requiresResponse"
            :order-on="orderOn"
        />

        <portal-target
            name="toolbar"
            :slot-props="{ pages }"
        />

        <v-container
            fluid
            py-2
            px-0
            grid-list-md
            mt-0
            class="has-navbar-on-top tw-flex tw-h-full"
        >
            <slot />
        </v-container>
    </v-container>
</template>
<script>
import '@/../css/views/order.css'
import HasOwnTranslations from '@/Mixins/HasOwnTranslations'
import SharedData from '@/Mixins/SharedData'
import OrderSidebar from './Components/OrderSidebar.vue'
import { router } from '@inertiajs/vue2'

export default {
    components: { OrderSidebar },

    mixins: [SharedData, HasOwnTranslations],

    props: {
        requiresResponse: {
            required: false,
            type: Object,
            default: () => ({ outgoing: 0, incoming: 0 }),
        },
        orderOn: {
            required: false,
            type: String,
            default: '',
        },
        order: {
            required: false,
            type: Object,
            default: () => ({ id: 0 }),
        },
    },

    data: () => ({
        orderFilesLinks: [],
    }),

    computed: {
        pages() {
            let additional = []
            if (this.user.impersonating) {
                additional = [
                    {
                        title: this.$t('order.labels.incoming-orders'),
                        icon: 'move_to_inbox',
                        route: 'orders.incoming.index',
                        matchRoute: 'orders.incoming.*',
                        name: 'incoming',
                    },
                ]
            }

            return [
                ...additional,
                {
                    title: this.$t('order.labels.outgoing-orders'),
                    icon: 'outbox',
                    route: 'orders.outgoing.index',
                    matchRoute: 'orders.outgoing.*',
                    name: 'outgoing',
                },
            ]
        },
    },

    created() {
        router.on('navigate', () => {
            this.orderFilesLinks = []
            if (
                !['orders.incoming.index', 'orders.outgoing.index'].includes(
                    this.$route().current(),
                )
            ) {
                const user = this.user

                this.orderFilesLinks = [
                    {
                        title: this.$t('order.buttons.time-logs'),
                        icon: 'history',
                        route: 'orders.show.time_logs.index',
                        matchRoute: 'orders.show.time_logs.*',
                        name: 'time_logs',
                    },
                    {
                        title: this.$t('order.labels.media'),
                        icon: 'photo_library',
                        route: 'orders.show.files.medias.index',
                        matchRoute: 'orders.show.files.medias.*',
                        name: 'media',
                    },
                    {
                        title: this.$t('order.labels.used-products'),
                        icon: 'shopping_cart',
                        route: 'orders.show.products.index',
                        matchRoute: 'orders.show.products.*',
                        name: 'products',
                    },
                    {
                        title: this.$t('headings.offers'),
                        icon: 'discount',
                        route: 'orders.offers.index',
                        matchRoute: 'orders.offers.*',
                        name: 'offers',
                    },
                ]

                if (user.can['can access invoices']) {
                    this.orderFilesLinks.push({
                        title: this.$t('headings.invoices'),
                        icon: 'receipt_long',
                        route: 'orders.show.invoices.index',
                        matchRoute: 'orders.show.invoices.*',
                        name: 'invoices',
                    })
                }

                if (user.can['can access delivery tickets']) {
                    this.orderFilesLinks.push({
                        title: this.$t('headings.delivery_tickets'),
                        icon: 'local_shipping',
                        route: 'orders.show.delivery_tickets.index',
                        matchRoute: 'orders.show.delivery_tickets.*',
                        name: 'delivery_tickets',
                    })
                }
            }
        })
    },
}
</script>
