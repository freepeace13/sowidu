import Vue from 'vue'
import VueI18n from 'vue-i18n'
import PortalVue from 'portal-vue'
import AuthLayout from '~Shared/Layouts/AuthLayout.vue'
import GuestLayout from '~Shared/Layouts/GuestLayout.vue'
import { createInertiaApp } from '@inertiajs/vue2'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

import './bootstrap'

const isDev = import.meta.env.DEV
const openInEditorHost = import.meta.env.VITE_OPEN_IN_EDITOR_HOST

if (isDev) {
    Vue.config.devtools = true
    window.VUE_DEVTOOLS_CONFIG = { openInEditorHost }
}

Vue.use(PortalVue)
Vue.use(VueI18n)

Vue.prototype.$route = window.route

createInertiaApp({
    title: (title) => `${import.meta.env.VITE_APP_NAME} - ${title}`,

    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        )

        page.default.layout = name.startsWith('Auth')
            ? GuestLayout
            : page.default.layout || AuthLayout

        return page
    },

    setup({ el, App, props, plugin }) {
        Vue.use(plugin)

        const page = JSON.parse(el.dataset.page)

        new Vue({
            i18n: new VueI18n({
                fallbackLocale: 'en',
                locale: page.props.locale,
                messages: page.props.translations,
                silentTranslationWarn: true,
                formatter: {
                    interpolate(message, values) {
                        if (values && typeof values === 'object') {
                            Object.keys(values).forEach(
                                (key) =>
                                    (message = message.replace(
                                        `:${key}`,
                                        values[key],
                                    )),
                            )
                        }

                        return [message]
                    },
                },
            }),
            render: (h) => h(App, props),
        }).$mount(el)
    },

    progress: {
        delay: 0,
        includeCSS: true,
    },
})
