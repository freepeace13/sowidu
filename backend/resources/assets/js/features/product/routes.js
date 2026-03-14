import IndexTabs from './widgets/IndexTabs.vue';
import { lazy } from 'vue-async-manager';
import { can } from '@common/middlewares/Authorize';
import middleware from 'vue-router-multiguard';
import { PERMISSIONS } from './enums';

export default [
    {
        path: '/products/:id?',
        name: 'products',
        components: {
            header: IndexTabs,
            default: lazy(() => import('./views/index.vue'))
        },
        meta: {
            title: 'Product &amp; Equipment'
        },
        beforeEnter: middleware([
            can(PERMISSIONS.VIEW_PRODUCTS)
        ])
    }
]