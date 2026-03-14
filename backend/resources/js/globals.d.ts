/// <reference types="vite/client" />

import { route } from '@inertiajs/vue2'
import router from 'vendor/tightenco/ziggy/src/js/Router'

declare global {
    function route(
        routeName: string,
        parameters?: any[] | any,
        absolute? = true,
    ): string | router
}

declare module 'vue/types/vue' {
    interface Route {
        uri: string
        methods: Array<
            'GET' | 'HEAD' | 'POST' | 'PATCH' | 'PUT' | 'OPTIONS' | 'DELETE'
        >
        domain?: null | string | undefined
    }

    interface Vue {
        $route: route<Route>
    }
}

declare module 'vue' {
    import 'vuetify/types/index'

    interface Route {
        uri: string
        methods: Array<
            'GET' | 'HEAD' | 'POST' | 'PATCH' | 'PUT' | 'OPTIONS' | 'DELETE'
        >
        domain?: null | string | undefined
    }

    interface ComponentCustomProperties {
        $route: route<Route>
    }
}

declare module '*.js' {
    import { type DefineComponent } from 'vue'
    const component: DefineComponent<{}, {}, any>
    export default component
}

import { router as inertiaRouter } from '@inertiajs/vue2'

declare module '@inertiajs/vue2' {
    export const router: typeof inertiaRouter
}

declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        $inertia: typeof inertiaRouter
    }
}
