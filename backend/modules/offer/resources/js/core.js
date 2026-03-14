import Vue from 'vue'
import VueI18n from 'vue-i18n'
import TranslationFormatter from '@/Modules/TranslationFormatter'
import { createInertiaApp } from '@inertiajs/vue2'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

import './base'
import '../css/styles.css'

Vue.prototype.$route = window.route

const i18n = (page) =>
    new VueI18n({
        locale: page.props.locale,
        formatter: new TranslationFormatter(),
        messages: page.props.translations,
        silentTranslationWarn: true,
        fallbackLocale: 'en',
    })

import AuthLayout from '@/Layouts/AuthLayout.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'

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
            i18n: i18n(page),
            render: (h) => h(App, props),
        }).$mount(el)
    },

    progress: {
        delay: 0,
        includeCSS: true,
    },
})
if (import.meta.env.DEV) {
    Vue.config.devtools = true
    window.VUE_DEVTOOLS_CONFIG = {
        openInEditorHost: import.meta.env.VITE_OPEN_IN_EDITOR_HOST,
    }
}
