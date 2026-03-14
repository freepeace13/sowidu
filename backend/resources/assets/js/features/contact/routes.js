import { lazy } from 'vue-async-manager';
import IndexTabs from './widgets/IndexTabs.vue';

import { FILTER_TYPES } from './enums';

const resourceTypes = Object.values(FILTER_TYPES.RESOURCE);

const typeQueryProps = (route) => ({
    typeQuery: resourceTypes.includes(route.query.type)
        ? route.query.type
        : FILTER_TYPES.RESOURCE.DEFAULT
})

const contact = [
    {
        path: '/contacts',
        name: 'contacts',
        redirect: { name: 'contacts.public' },
        components: {
            header: IndexTabs,
            default: lazy(() => import('./views/index.vue')),
        },
        props: {
            default: true,
            header: (route) => ({
                ...typeQueryProps(route)
            })
        },
        meta: {
            title: 'Contacts'
        },
        children: [
            {
                path: 'public',
                name: 'contacts.public',
                component: lazy(() => import('./views/index/public.vue')),
                props: (route) => ({
                    ...typeQueryProps(route)
                })
            },
            {
                path: 'addressbook',
                name: 'contacts.addressbook',
                component: lazy(() => import('./views/index/addressbook.vue')),
                props: (route) => ({
                    ...typeQueryProps(route)
                })
            },
            {
                path: 'invitations',
                name: 'contacts.invitations',
                component: lazy(() => import('./views/index/invitations.vue')),
            }
        ]
    }
]

export default contact
