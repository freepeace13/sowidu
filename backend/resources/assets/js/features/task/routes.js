import IndexTabs from './widgets/IndexTabs.vue';
import { lazy } from 'vue-async-manager';
import { can } from '@common/middlewares/Authorize';
import middleware from 'vue-router-multiguard';
import { PERMISSIONS } from './enums';

export default [
    {
        path: '/tasks/:id?',
        name: 'tasks',
        components: {
            header: IndexTabs,
            default: lazy(() => import('./views/index.vue')),
        },
        meta: {
            title: 'Tasks'
        },
        beforeEnter: middleware([
            can(PERMISSIONS.VIEW_TASKS)
        ])
    }
];