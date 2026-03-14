import IndexTabsWidget from './widgets/IndexTabs.vue';
import { lazy } from 'vue-async-manager';
import { can } from '@common/middlewares/Authorize';
import middleware from 'vue-router-multiguard';
import { PERMISSIONS } from './enums';

export default [
    {
        path: '/media',
        name: 'media',
        components: {
            header: IndexTabsWidget,
            default: lazy(() => import('./views/index.vue'))
        },
        meta: { title: 'Media' },
        beforeEnter: middleware([
            can(PERMISSIONS.VIEW_MEDIA)
        ])
    }
]
