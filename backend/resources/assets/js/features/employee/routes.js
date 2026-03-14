import { lazy } from 'vue-async-manager';
import { allowCan } from '@common/middlewares/Authorize';
import middleware from 'vue-router-multiguard';
import * as EmployeeEnums from './enums';

export default [
    {
        path: '/employees',
        name: 'employees',
        meta: { title: 'Employees' },
        component: lazy(() => import('./views/index.vue')),
        beforeEnter: middleware([
            allowCan(
                EmployeeEnums.APP,
                EmployeeEnums.PERMISSIONS.VIEW_EMPLOYEES
            )
        ])
    }
]