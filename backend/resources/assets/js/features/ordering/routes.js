import IndexTabs from './widgets/IndexTabs.vue';
import IndexIncomingView from './views/index/incoming.vue';
import IndexOutgoingView from './views/index/outgoing.vue';

import ShowToolbar from './widgets/ShowToolbar.vue';
import ShowIndexView from './views/_id/index.vue';
import ShowMediaView from './views/_id/media.vue';
import ShowDeliveriesView from './views/_id/deliveries.vue';
import ShowTasksView from './views/_id/tasks.vue';

import { lazy } from 'vue-async-manager';
import { can, any } from '@common/middlewares/Authorize';
import middleware from 'vue-router-multiguard';
import { PERMISSIONS } from './enums';

export default [
    {
        path: '/orders',
        name: 'orders',
        redirect: { name: 'orders.incoming' },
        components: {
            header: IndexTabs,
            default: lazy(() => import('./views/index.vue'))
        },
        meta: { title: 'Orders' },
        beforeEnter: middleware([
            any([
                PERMISSIONS.VIEW_INCOMING_ORDERS,
                PERMISSIONS.VIEW_OUTGOING_ORDERS,
            ]),
        ]),
        children: [
            {
                path: 'incoming',
                name: 'orders.incoming',
                component: IndexIncomingView,
                beforeEnter: middleware([
                    can(PERMISSIONS.VIEW_INCOMING_ORDERS)
                ])
            },
            {
                path: 'outgoing',
                name: 'orders.outgoing',
                component: IndexOutgoingView,
                beforeEnter: middleware([
                    can(PERMISSIONS.VIEW_OUTGOING_ORDERS)
                ])
            }
        ]
    },
    {
        path: '/orders/:id',
        name: 'orders.show',
        redirect: { name: 'orders.show.details' },
        components: {
            header: ShowToolbar,
            default: lazy(() => import('./views/_id.vue')),
        },
        meta: { title: 'Orders' },
        children: [
            {
                path: 'details',
                name: 'orders.show.details',
                component: ShowIndexView
            },
            {
                path: 'media',
                name: 'orders.show.media',
                component: ShowMediaView
            },
            {
                path: 'deliveries',
                name: 'orders.show.deliveries',
                component: ShowDeliveriesView
            },
            {
                path: 'tasks',
                name: 'orders.show.tasks',
                component: ShowTasksView
            },
        ]
    },
];
