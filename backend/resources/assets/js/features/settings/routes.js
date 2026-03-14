import { lazy } from 'vue-async-manager';
import middleware from 'vue-router-multiguard';
import IndexView from './views/index.vue';
import AccessControlSettings from './views/access-control.vue';
import AccountInformationSettings from './views/account-information.vue';
import AddressSettings from './views/address.vue';
import { allowCan } from '@common/middlewares/Authorize';
import * as EmployeeEnums from '@features/employee/enums';

export default [
    {
        path: '/settings',
        name: 'settings',
        redirect: { name: 'settings.account-info' },
        component: IndexView,
        meta: { title: 'Settings' },
        children: [
            {
                path: 'account-information',
                name: 'settings.account-info',
                component: AccountInformationSettings
            },
            {
                path: 'address',
                name: 'settings.address',
                component: AddressSettings
            },
            {
                path: 'access-control',
                name: 'settings.access-control',
                component: lazy(() => import('./views/access-control.vue')),
                beforeEnter: middleware([
                    allowCan(
                        EmployeeEnums.APP,
                        EmployeeEnums.PERMISSIONS.VIEW_EMPLOYEES
                    )
                ])
            }
        ]
    }
];