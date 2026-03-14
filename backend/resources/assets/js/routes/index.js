import { auth, guest, permission } from '~/routes/middlewares'
import Vue from 'vue'
import Meta from 'vue-meta'
import store from '~/services/store'
import Router from 'vue-router'
import { sync } from 'vuex-router-sync'
import asyncProps from './asyncProps';
import TaskRoutes from '@features/task/routes';
import ProductRoutes from '@features/product/routes';
import ContactRoutes from '@features/contact/routes';
import OrderRoutes from '@features/ordering/routes';
import DeliveryRoutes from '@features/delivery/routes';
import MediaRoutes from '@features/media/routes';
import EmployeeRoutes from '@features/employee/routes';
import SettingRoutes from '@features/settings/routes';

import {
    setting,
    company,
} from './modules';

Vue.use(Meta)
Vue.use(Router)

export default make([
    {
        path: '/verification/:token',
        name: 'middleforms.verifications',
        component: require('~/views/MiddleForms/Verification').default
    },
    {
        path: '/account/reset-password',
        name: 'middleforms.resetpassword',
        component: require('~/views/MiddleForms/ResetPassword').default
    },

    ...guest([
        {
            path: '/',
            name: 'auth.login',
            component: require('~/views/Login').default
        },
        {   path: '/auth/register',
            name: 'auth.register',
            component: require('~/views/auth/register').default
        },
        {
            path: '/auth/:token/verify',
            name: 'auth.verify',
            component: require('~/views/auth/verify').default
        },
        {
            path: '/auth/forgot-password',
            name: 'auth.password.forgot',
            component: require('~/views/auth/forgot-password').default
        },
        {
            path: '/auth/reset-password',
            name: 'auth.password.reset',
            component: require('~/views/auth/reset-password').default
        },
    ]),

    // Authenticated routes
    ...auth([
        {
            path: '/desktop',
            name: 'desktop',
            component: require('~/views/desktop').default,
        },
        ...TaskRoutes,
        ...ContactRoutes,
        ...SettingRoutes,
        ...company,
        ...EmployeeRoutes,
        ...OrderRoutes,
        ...ProductRoutes,
        ...MediaRoutes,
        ...DeliveryRoutes
    ])
])

function make (routes) {
    const router = new Router({
        routes,
        scrollBehavior,
        mode: 'history'
    })

    // Register before guard.
    router.beforeEach(async (to, from, next) => {
        setLayout(router, to, from)
        next();
    })

    router.afterEach((to, from) => {
        to.meta.prevRoute = from;
        
        if (to.matched.some(m => m.meta.title)) {
            to.meta.title = to.matched.find(m => m.meta.title).meta.title;
        }
        
        // router.app.$nextTick(() => {
        //     router.app.$loading.finish();
        // })
    })

    return router;
}

function setLayout (router, to, from) {
    // Get the first matched component.
    const [component] = router.getMatchedComponents({ ...to })

    if (component) {
        router.app.$nextTick(() => {
            // if (component.loading !== false) {
            //     router.app.$loading.start()
            // }
            if (store.state.ui.layout !== component.layout) {
                // Set application layout.
                store.commit(`ui/SET_LAYOUT`, component.layout);
            }
        })
    }
}

function scrollBehavior (to, from, savedPosition) {
    if (savedPosition) {
        return savedPosition
    }

    const position = {}

    if (to.hash) {
        position.selector = to.hash
    }

    if (to.matched.some(m => m.meta.scrollToTop)) {
        position.x = 0
        position.y = 0
    }

    return position
}
