import axios from 'axios'
import pusherjs from 'pusher-js'
import Vue from 'vue'
import Confirm from '@/Plugins/Confirm'
import Tooltip from 'vue-directive-tooltip'
import PortalVue from 'portal-vue'
import VueI18n from 'vue-i18n'

window.axios = axios

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['X-Feature-Request'] = true
axios.defaults.headers.common['X-Internal-Json-Request'] = true

window.Pusher = pusherjs

import '@/theme'
import '@/echo'

Vue.directive('click-outside', {
    bind(el, binding) {
        el.clickOutsideEvent = function (event) {
            if (!(el == event.target || el.contains(event.target))) {
                if (typeof binding.value === 'function') {
                    binding.value(event)
                }
            }
        }
        document.body.addEventListener('click', el.clickOutsideEvent)
    },
    unbind(el) {
        document.body.removeEventListener('click', el.clickOutsideEvent)
    },
})

Vue.use(Confirm)
Vue.use(Tooltip)
Vue.use(PortalVue)
Vue.use(VueI18n)
