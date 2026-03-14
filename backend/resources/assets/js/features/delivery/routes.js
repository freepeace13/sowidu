import IndexTabsWidget from './widgets/IndexTabs.vue';

import IndexIncomingView from './views/index/incoming.vue';
import IndexOutgoingView from './views/index/outgoing.vue'; 

import { lazy } from 'vue-async-manager';
import { can, any } from '@common/middlewares/Authorize';
import middleware from 'vue-router-multiguard';
import { PERMISSIONS } from './enums';

export default [
    {
        path: '/deliveries',
        name: 'deliveries',
        redirect: { name: 'deliveries.incoming' },
        components: {
            header: IndexTabsWidget,
            default: lazy(() => import('./views/index.vue'))
        },
        meta: { title: 'Deliveries' },
        children: [
            {
                path: 'incoming',
                name: 'deliveries.incoming',
                component: IndexIncomingView,
                beforeEnter: middleware([
                    can(PERMISSIONS.VIEW_INCOMING_DELIVERIES)
                ])
            },
            {
                path: 'outgoing',
                name: 'deliveries.outgoing',
                component: IndexOutgoingView,
                beforeEnter: middleware([
                    can(PERMISSIONS.VIEW_OUTGOING_DELIVERIES)
                ])
            }
        ]
    }
];