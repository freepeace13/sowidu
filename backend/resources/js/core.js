import { createInertiaApp } from '@inertiajs/vue2'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import PortalVue from 'portal-vue'
import Vue from 'vue'
import VueI18n from 'vue-i18n'
import TranslationFormatter from './Modules/TranslationFormatter'

import './base'

import '../css/styles.css'

Vue.use(VueI18n)
Vue.use(PortalVue)

Vue.directive('click-outside', {
    bind(el, binding) {
        el.clickOutsideEvent = function (event) {
            if (!(el == event.target || el.contains(event.target))) {
                if (typeof binding.value === 'function') {
                    binding.value(event)
                }
            }
        }
        // Attach event listener to the document
        document.body.addEventListener('click', el.clickOutsideEvent)
    },
    unbind(el) {
        // Remove the event listener when the element is destroyed
        document.body.removeEventListener('click', el.clickOutsideEvent)
    },
})

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
import './Components' // Import global app components
import './Filters' // Import global app filters
import GuestLayout from './Layouts/GuestLayout.vue'
import './Plugins' // Import app plugins

// Pre-load all module pages for dynamic resolution
const modulePages = import.meta.glob(
    '../../modules/*/resources/js/Pages/**/*.vue',
)

createInertiaApp({
    title: (title) => `${import.meta.env.VITE_APP_NAME} - ${title}`,

    resolve: async (name) => {
        // Check if it's a module page (@module/PageName)
        if (name.startsWith('@')) {
            const parts = name.substring(1).split('/')
            const moduleName = parts[0].toLowerCase()
            const pagePath = parts.slice(1).join('/')

            // Build the expected module page path
            const expectedPath = `../../modules/${moduleName}/resources/js/Pages/${pagePath}.vue`

            // Find matching page key (handle path normalization)
            const pageKey = Object.keys(modulePages).find((key) => {
                // Normalize paths for comparison (handle both / and \ separators)
                const normalizedKey = key.replace(/\\/g, '/')
                const normalizedExpected = expectedPath.replace(/\\/g, '/')

                // Case-insensitive comparison for module name
                const keyLower = normalizedKey.toLowerCase()
                const expectedLower = normalizedExpected.toLowerCase()

                // Exact match (case-insensitive for module name, case-sensitive for page path)
                if (keyLower === expectedLower) {
                    return true
                }

                // Fallback: check if key ends with the expected module/page path
                // This handles cases where the glob returns slightly different paths
                const pagePathLower = pagePath.toLowerCase()
                return (
                    keyLower.includes(
                        `modules/${moduleName}/resources/js/pages/`,
                    ) && keyLower.endsWith(`${pagePathLower}.vue`)
                )
            })

            if (pageKey && modulePages[pageKey]) {
                console.log(modulePages[pageKey])
                const page = await modulePages[pageKey]()

                // Apply layout logic (use page name without @module prefix for layout check)
                page.default.layout = pagePath.startsWith('Auth')
                    ? GuestLayout
                    : page.default.layout || AuthLayout

                return page
            }

            // If module page not found, fall through to main app pages
            console.warn(
                `Module page not found: ${name}, falling back to main app pages`,
            )
        }

        // Fall back to main app pages
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
